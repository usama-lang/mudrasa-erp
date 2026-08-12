<?php

declare(strict_types=1);

namespace Modules\MadrasaFunds\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\MadrasaFunds\Http\Requests\CancelReceiptRequest;
use Modules\MadrasaFunds\Http\Requests\StoreReceiptRequest;
use Modules\MadrasaFunds\Http\Requests\UpdateReceiptRequest;
use Modules\MadrasaFunds\Models\Department;
use Modules\MadrasaFunds\Models\Fund;
use Modules\MadrasaFunds\Models\Receipt;
use Modules\MadrasaFunds\Models\Student;
use Modules\MadrasaFunds\Services\ReceiptService;

class ReceiptController extends MadrasaFundsController
{
    public function __construct(
        private readonly ReceiptService $receiptService
    ) {
    }

    public function index(): Renderable
    {
        abort_unless(auth()->user()->can('madrasa_funds.view_any'), 403);

        $this->setBreadcrumbTitle(mf_bi('Receipts'))
            ->setBreadcrumbIcon('lucide:receipt')
            ->setBreadcrumbActionButton(
                route('admin.madrasafunds.receipts.create'),
                mf_bi('New Receipt'),
                'lucide:plus',
                'madrasa_funds.create'
            );

        return $this->renderViewWithBreadcrumbs('madrasafunds::pages.receipts.index', [
            'funds' => Fund::query()->active()->orderBy('name')->get(),
            'departments' => Department::query()->active()->orderBy('name')->get(),
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        abort_unless(auth()->user()->can('madrasa_funds.view_any'), 403);

        $query = Receipt::query()->with(['fund', 'department', 'student']);

        if ($search = $request->string('search')->trim()->value()) {
            $query->where(function ($q) use ($search) {
                $q->where('receipt_number', 'like', "%{$search}%")
                    ->orWhere('donor_name', 'like', "%{$search}%")
                    ->orWhere('donor_phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('fund')) {
            $query->where('fund_id', $request->integer('fund'));
        }

        if ($request->filled('department')) {
            $query->where('department_id', $request->integer('department'));
        }

        if ($request->filled('donor')) {
            $query->where('donor_name', 'like', '%' . $request->string('donor')->value() . '%');
        }

        if ($request->filled('date_from')) {
            $query->whereDate('receipt_date', '>=', $request->date('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('receipt_date', '<=', $request->date('date_to'));
        }

        $sortable = ['id', 'receipt_number', 'receipt_date', 'amount', 'created_at'];
        $sort = in_array($request->string('sort')->value(), $sortable, true) ? $request->string('sort')->value() : 'receipt_date';
        $dir = $request->string('direction')->value() === 'asc' ? 'asc' : 'desc';

        $paginated = $query->orderBy($sort, $dir)->orderByDesc('id')->paginate($request->integer('per_page', 15));

        $data = $paginated->getCollection()->map(fn (Receipt $r) => [
            'id' => $r->id,
            'receipt_number' => $r->receipt_number,
            'receipt_date' => $r->receipt_date?->format('Y-m-d'),
            'fund_name' => $r->fund?->name ?? '-',
            'department_name' => $r->department?->name ?? '-',
            'payer' => $r->student?->name ?? $r->donor_name ?? '-',
            'amount' => number_format((float) $r->amount, 2),
            'is_cancelled' => $r->is_cancelled,
        ]);

        return response()->json([
            'data' => $data,
            'meta' => [
                'total' => $paginated->total(),
                'per_page' => $paginated->perPage(),
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
            ],
        ]);
    }

    public function create(): Renderable
    {
        abort_unless(auth()->user()->can('madrasa_funds.create'), 403);

        $this->setBreadcrumbTitle(mf_bi('New Receipt'))
            ->setBreadcrumbIcon('lucide:receipt')
            ->addBreadcrumbItem(mf_bi('Receipts'), route('admin.madrasafunds.receipts.index'));

        return $this->renderViewWithBreadcrumbs('madrasafunds::pages.receipts.create', [
            'receipt' => null,
            'funds' => Fund::query()->active()->orderBy('name')->get(),
            'departments' => Department::query()->active()->orderBy('name')->get(),
        ]);
    }

    public function store(StoreReceiptRequest $request): RedirectResponse
    {
        $receipt = $this->receiptService->create($request->validated());

        session()->flash('success', mf_bi('Receipt created successfully.'));

        return redirect()->route('admin.madrasafunds.receipts.print', $receipt);
    }

    public function show(Receipt $receipt): Renderable
    {
        abort_unless(auth()->user()->can('madrasa_funds.view_any'), 403);

        $receipt->load(['fund', 'department', 'student', 'createdBy']);

        $this->setBreadcrumbTitle($receipt->receipt_number)
            ->setBreadcrumbIcon('lucide:receipt')
            ->addBreadcrumbItem(mf_bi('Receipts'), route('admin.madrasafunds.receipts.index'));

        return $this->renderViewWithBreadcrumbs('madrasafunds::pages.receipts.show', [
            'receipt' => $receipt,
        ]);
    }

    public function print(Request $request, Receipt $receipt): Renderable
    {
        abort_unless(auth()->user()->can('madrasa_funds.print_receipt'), 403);

        $receipt->load(['fund', 'department', 'student', 'createdBy']);

        $this->setBreadcrumbTitle(mf_bi('Print Receipt'))
            ->setBreadcrumbIcon('lucide:printer')
            ->addBreadcrumbItem(mf_bi('Receipts'), route('admin.madrasafunds.receipts.index'));

        return $this->renderViewWithBreadcrumbs('madrasafunds::pages.receipts.print', [
            'receipt' => $receipt,
            'autoPrint' => ! $request->has('noprint'),
        ]);
    }

    public function edit(Receipt $receipt): Renderable
    {
        abort_unless(auth()->user()->can('madrasa_funds.edit'), 403);

        $this->setBreadcrumbTitle(mf_bi('Edit'))
            ->setBreadcrumbIcon('lucide:receipt')
            ->addBreadcrumbItem(mf_bi('Receipts'), route('admin.madrasafunds.receipts.index'));

        return $this->renderViewWithBreadcrumbs('madrasafunds::pages.receipts.edit', [
            'receipt' => $receipt,
            'funds' => Fund::query()->active()->orderBy('name')->get(),
            'departments' => Department::query()->active()->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateReceiptRequest $request, Receipt $receipt): RedirectResponse
    {
        $this->receiptService->update($receipt, $request->validated());

        session()->flash('success', mf_bi('Receipt updated successfully.'));

        return redirect()->route('admin.madrasafunds.receipts.index');
    }

    public function cancel(CancelReceiptRequest $request, Receipt $receipt): RedirectResponse
    {
        $this->receiptService->cancel($receipt, $request->validated()['cancelled_reason']);

        session()->flash('success', mf_bi('Receipt cancelled successfully.'));

        return redirect()->route('admin.madrasafunds.receipts.index');
    }

    public function destroy(Receipt $receipt): RedirectResponse
    {
        abort_unless(auth()->user()->can('madrasa_funds.delete'), 403);

        $this->receiptService->delete($receipt);

        session()->flash('success', mf_bi('Receipt deleted successfully.'));

        return redirect()->route('admin.madrasafunds.receipts.index');
    }

    public function searchStudent(Request $request): JsonResponse
    {
        abort_unless(auth()->user()->can('madrasa_funds.create'), 403);

        $search = $request->string('q')->trim()->value();

        $students = Student::query()
            ->with('department')
            ->active()
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('father_name', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->limit(15)
            ->get();

        return response()->json(
            $students->map(fn (Student $s) => [
                'id' => $s->id,
                'name' => $s->name,
                'father_name' => $s->father_name ?? '-',
                'class' => $s->class ?? '-',
                'department_id' => $s->department_id,
                'department_name' => $s->department?->name ?? '-',
            ])->values()
        );
    }
}
