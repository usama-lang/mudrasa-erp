<?php

declare(strict_types=1);

namespace Modules\SchoolManagement\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\SchoolManagement\Models\Campus;
use Modules\SchoolManagement\Models\Department;
use Modules\SchoolManagement\Models\FeePayment;
use Modules\SchoolManagement\Models\SchoolClass;
use Modules\SchoolManagement\Models\Student;
use Modules\SchoolManagement\Services\FeeService;
use Modules\SchoolManagement\Services\SchoolScopeService;

class FeeReportController extends SchoolManagementController
{
    public function __construct(
        private readonly FeeService $feeService
    ) {
    }

    public function index(Request $request): Renderable
    {
        abort_unless(auth()->user()->can('school_fee.report'), 403);

        $filters = $this->resolveFilters($request);

        $query = $this->feeService->reportQuery($filters)
            ->with(['student', 'schoolClass', 'section'])
            ->orderByDesc('payment_date')
            ->orderByDesc('id');

        $payments = $query->paginate(20)->withQueryString();

        // Summary totals across the full filtered set (not just the page).
        $summaryBase = $this->feeService->reportQuery($filters);
        $summary = [
            'count'          => (clone $summaryBase)->count(),
            'total_due'      => (clone $summaryBase)->sum(DB::raw('monthly_fee + COALESCE(food_charges, 0)')),
            'total_paid'     => (clone $summaryBase)->sum('amount_paid'),
            'total_discount' => (clone $summaryBase)->sum('discount'),
            'total_balance'  => (clone $summaryBase)->sum('balance'),
        ];

        $this->setBreadcrumbTitle(bi('Fee Reports'))
            ->setBreadcrumbIcon('lucide:chart-bar')
            ->addBreadcrumbItem(bi('Fees'), route('school.fees.index'));

        return $this->renderViewWithBreadcrumbs('schoolmanagement::pages.fees.reports.index', array_merge(
            $this->filterOptions($request),
            [
                'payments' => $payments,
                'summary'  => $summary,
                'filters'  => $filters,
            ]
        ));
    }

    public function departmentSummary(Request $request): Renderable
    {
        abort_unless(auth()->user()->can('school_fee.report'), 403);

        $month = $request->filled('month')
            ? $request->date('month')->startOfMonth()
            : now()->startOfMonth();

        $campusId = SchoolScopeService::getUserCampusId(auth()->user());

        $departments = Department::query()
            ->when($campusId, fn ($q) => $q->where('campus_id', $campusId))
            ->orderBy('name')
            ->get();

        $rows = $departments->map(function (Department $department) use ($month) {
            $students = Student::where('department_id', $department->id)
                ->where('enrollment_status', 'enrolled')
                ->count();

            $payments = FeePayment::where('department_id', $department->id)
                ->whereDate('fee_month', $month->toDateString());

            $collected = (clone $payments)->sum('amount_paid');
            $due = (clone $payments)->sum(DB::raw('monthly_fee + COALESCE(food_charges, 0)'));

            return [
                'department' => $department->name,
                'students'   => $students,
                'due'        => (float) $due,
                'collected'  => (float) $collected,
                'pending'    => max((float) $due - (float) $collected, 0),
            ];
        });

        $this->setBreadcrumbTitle(bi('Department Summary'))
            ->setBreadcrumbIcon('lucide:chart-bar')
            ->addBreadcrumbItem(bi('Fee Reports'), route('school.fees.reports.index'));

        return $this->renderViewWithBreadcrumbs('schoolmanagement::pages.fees.reports.department-summary', [
            'rows'  => $rows,
            'month' => $month->format('Y-m'),
        ]);
    }

    public function classSummary(Request $request): Renderable
    {
        abort_unless(auth()->user()->can('school_fee.report'), 403);

        $month = $request->filled('month')
            ? $request->date('month')->startOfMonth()
            : now()->startOfMonth();

        $campusId = SchoolScopeService::getUserCampusId(auth()->user());
        $departmentId = $request->integer('department_id') ?: null;

        $classes = SchoolClass::query()
            ->when($campusId, fn ($q) => $q->where('campus_id', $campusId))
            ->when($departmentId, fn ($q) => $q->where('department_id', $departmentId))
            ->orderBy('name')
            ->get();

        $rows = $classes->map(function (SchoolClass $class) use ($month) {
            $students = Student::where('class_id', $class->id)
                ->where('enrollment_status', 'enrolled')
                ->count();

            $payments = FeePayment::where('class_id', $class->id)
                ->whereDate('fee_month', $month->toDateString());

            $collected = (clone $payments)->sum('amount_paid');
            $due = (clone $payments)->sum(DB::raw('monthly_fee + COALESCE(food_charges, 0)'));

            return [
                'class'     => $class->name,
                'students'  => $students,
                'due'       => (float) $due,
                'collected' => (float) $collected,
                'pending'   => max((float) $due - (float) $collected, 0),
            ];
        });

        $departments = Department::query()
            ->when($campusId, fn ($q) => $q->where('campus_id', $campusId))
            ->orderBy('name')
            ->get();

        $this->setBreadcrumbTitle(bi('Class Summary'))
            ->setBreadcrumbIcon('lucide:chart-bar')
            ->addBreadcrumbItem(bi('Fee Reports'), route('school.fees.reports.index'));

        return $this->renderViewWithBreadcrumbs('schoolmanagement::pages.fees.reports.class-summary', [
            'rows'         => $rows,
            'month'        => $month->format('Y-m'),
            'departments'  => $departments,
            'departmentId' => $departmentId,
        ]);
    }

    private function resolveFilters(Request $request): array
    {
        $campusId = SchoolScopeService::getUserCampusId(auth()->user());

        return [
            'campus_id'     => $campusId ?? ($request->integer('campus_id') ?: null),
            'department_id' => $request->integer('department_id') ?: null,
            'class_id'      => $request->integer('class_id') ?: null,
            'status'        => $request->string('status')->value() ?: null,
            'month_from'    => $request->string('month_from')->value() ?: null,
            'month_to'      => $request->string('month_to')->value() ?: null,
        ];
    }

    private function filterOptions(Request $request): array
    {
        $campusId = SchoolScopeService::getUserCampusId(auth()->user());

        return [
            'campuses'     => $campusId === null ? Campus::orderBy('name')->get() : collect(),
            'departments'  => Department::when($campusId, fn ($q) => $q->where('campus_id', $campusId))->orderBy('name')->get(),
            'classes'      => SchoolClass::when($campusId, fn ($q) => $q->where('campus_id', $campusId))->orderBy('name')->get(),
            'isSuperAdmin' => $campusId === null,
        ];
    }
}
