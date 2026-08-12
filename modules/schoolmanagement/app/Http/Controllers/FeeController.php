<?php

declare(strict_types=1);

namespace Modules\SchoolManagement\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\SchoolManagement\Http\Requests\StoreFeeRequest;
use Modules\SchoolManagement\Models\Campus;
use Modules\SchoolManagement\Models\FeePayment;
use Modules\SchoolManagement\Models\Student;
use Modules\SchoolManagement\Services\FeeService;
use Modules\SchoolManagement\Services\SchoolScopeService;

class FeeController extends SchoolManagementController
{
    public function __construct(
        private readonly FeeService $feeService
    ) {
    }

    public function index(Request $request): Renderable
    {
        abort_unless(auth()->user()->can('school_fee.view'), 403);

        $campusId = SchoolScopeService::getUserCampusId(auth()->user());

        $query = FeePayment::query()->with(['student', 'schoolClass', 'section']);

        if ($campusId !== null) {
            $query->where('campus_id', $campusId);
        } elseif ($request->filled('campus')) {
            $query->where('campus_id', $request->integer('campus'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->value());
        }

        if ($request->filled('month')) {
            $query->whereDate('fee_month', $request->date('month')->startOfMonth());
        }

        $payments = $query->orderByDesc('payment_date')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $stats = $this->feeService->getDashboardStats($campusId);

        $this->setBreadcrumbTitle(bi('Fees'))
            ->setBreadcrumbIcon('lucide:wallet')
            ->setBreadcrumbActionButton(
                route('school.fees.create'),
                bi('Collect Fee'),
                'lucide:plus',
                'school_fee.collect'
            );

        return $this->renderViewWithBreadcrumbs('schoolmanagement::pages.fees.index', [
            'payments'     => $payments,
            'stats'        => $stats,
            'campuses'     => $campusId === null ? Campus::orderBy('name')->get() : collect(),
            'isSuperAdmin' => $campusId === null,
        ]);
    }

    public function create(): Renderable
    {
        abort_unless(auth()->user()->can('school_fee.collect'), 403);

        $this->setBreadcrumbTitle(bi('Collect Fee'))
            ->setBreadcrumbIcon('lucide:wallet')
            ->addBreadcrumbItem(bi('Fees'), route('school.fees.index'));

        return $this->renderViewWithBreadcrumbs('schoolmanagement::pages.fees.create');
    }

    public function store(StoreFeeRequest $request): RedirectResponse
    {
        $campusId = SchoolScopeService::getUserCampusId(auth()->user());

        if ($campusId !== null) {
            $student = Student::findOrFail($request->integer('student_id'));
            abort_unless($student->campus_id === $campusId, 403);
        }

        try {
            $payment = $this->feeService->collectFee($request->validated());
        } catch (QueryException $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', bi('A payment for this month already exists.'));
        }

        session()->flash('success', bi('Fee paid successfully.'));

        return redirect()->route('school.fees.show', $payment->id);
    }

    public function show(Request $request, FeePayment $feePayment): Renderable
    {
        abort_unless(auth()->user()->can('school_fee.view'), 403);

        $campusId = SchoolScopeService::getUserCampusId(auth()->user());
        if ($campusId !== null) {
            abort_unless($feePayment->campus_id === $campusId, 403);
        }

        $feePayment->load(['student', 'campus', 'schoolClass', 'section', 'collectedBy']);

        $this->setBreadcrumbTitle(bi('Fee Receipt'))
            ->setBreadcrumbIcon('lucide:receipt')
            ->addBreadcrumbItem(bi('Fees'), route('school.fees.index'));

        return $this->renderViewWithBreadcrumbs('schoolmanagement::pages.fees.show', [
            'payment'  => $feePayment,
            'autoPrint' => $request->boolean('print'),
        ]);
    }

    public function searchStudent(Request $request): JsonResponse
    {
        abort_unless(auth()->user()->can('school_fee.collect'), 403);

        $search = $request->string('q')->trim()->value();

        $query = Student::query()
            ->with(['campus', 'schoolClass', 'section'])
            ->where('enrollment_status', 'enrolled');

        $campusId = SchoolScopeService::getUserCampusId(auth()->user());
        if ($campusId !== null) {
            $query->where('campus_id', $campusId);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('student_name', 'like', "%{$search}%")
                  ->orWhere('admission_no', 'like', "%{$search}%");
            });
        }

        $students = $query->orderBy('student_name')->limit(15)->get();

        return response()->json(
            $students->map(fn (Student $s) => [
                'id'                 => $s->id,
                'student_name'       => $s->student_name ?? '-',
                'admission_no'       => $s->admission_no,
                'campus_name'        => $s->campus?->name ?? '-',
                'class_name'         => $s->schoolClass?->name ?? '-',
                'section_name'       => $s->section?->name ?? '-',
                'monthly_fee'        => (float) $s->monthly_fee,
                'food_charges'       => (float) $s->food_charges,
                'food_at_madrasa'    => (bool) $s->food_at_madrasa,
                'residential_status' => $s->residential_status,
                'pending_months'     => $this->feeService->getStudentPendingMonths($s),
            ])->values()
        );
    }
}
