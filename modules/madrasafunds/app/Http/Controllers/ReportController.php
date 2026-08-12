<?php

declare(strict_types=1);

namespace Modules\MadrasaFunds\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Modules\MadrasaFunds\Enums\FundType;
use Modules\MadrasaFunds\Models\Department;
use Modules\MadrasaFunds\Models\Fund;
use Modules\MadrasaFunds\Services\ReportService;

class ReportController extends MadrasaFundsController
{
    public function __construct(
        private readonly ReportService $reportService
    ) {
    }

    public function index(): Renderable
    {
        abort_unless(auth()->user()->can('madrasa_funds.view_reports'), 403);

        $this->setBreadcrumbTitle(mf_bi('Reports'))
            ->setBreadcrumbIcon('lucide:chart-bar');

        return $this->renderViewWithBreadcrumbs('madrasafunds::pages.reports.index');
    }

    public function fund(Request $request): Renderable
    {
        abort_unless(auth()->user()->can('madrasa_funds.view_reports'), 403);

        $filters = $this->resolveFilters($request);

        $this->reportBreadcrumb(mf_bi('Fund-wise Report'));

        return $this->renderViewWithBreadcrumbs('madrasafunds::pages.reports.fund', array_merge(
            $this->filterOptions(),
            [
                'rows' => $this->reportService->fundWise($filters),
                'summary' => $this->reportService->summary($filters)['summary'],
                'filters' => $filters,
            ]
        ));
    }

    public function department(Request $request): Renderable
    {
        abort_unless(auth()->user()->can('madrasa_funds.view_reports'), 403);

        $filters = $this->resolveFilters($request);

        $this->reportBreadcrumb(mf_bi('Department-wise Report'));

        return $this->renderViewWithBreadcrumbs('madrasafunds::pages.reports.department', array_merge(
            $this->filterOptions(),
            [
                'rows' => $this->reportService->departmentWise($filters),
                'summary' => $this->reportService->summary($filters)['summary'],
                'filters' => $filters,
            ]
        ));
    }

    public function student(Request $request): Renderable
    {
        abort_unless(auth()->user()->can('madrasa_funds.view_reports'), 403);

        $filters = $this->resolveFilters($request);

        $this->reportBreadcrumb(mf_bi('Student Fees Report'));

        return $this->renderViewWithBreadcrumbs('madrasafunds::pages.reports.student', array_merge(
            $this->filterOptions(),
            [
                'rows' => $this->reportService->studentFees($filters),
                'filters' => $filters,
            ]
        ));
    }

    public function food(Request $request): Renderable
    {
        abort_unless(auth()->user()->can('madrasa_funds.view_reports'), 403);

        $filters = $this->resolveFilters($request);

        // The food/meal fund is identified by name; fall back to all fees funds.
        $foodFund = Fund::query()
            ->where('type', FundType::FEES->value)
            ->where(fn ($q) => $q->where('name', 'like', '%Food%')->orWhere('name', 'like', '%Khana%')->orWhere('name_urdu', 'like', '%کھانا%'))
            ->first();

        if ($foodFund) {
            $filters['fund_id'] = $foodFund->id;
        } else {
            $filters['fund_type'] = FundType::FEES->value;
        }

        $this->reportBreadcrumb(mf_bi('Food Fees Report'));

        $query = $this->reportService->reportQuery($filters)
            ->with(['student.department', 'fund'])
            ->orderByDesc('receipt_date')
            ->orderByDesc('id');

        return $this->renderViewWithBreadcrumbs('madrasafunds::pages.reports.food', array_merge(
            $this->filterOptions(),
            [
                'receipts' => $query->paginate(20)->withQueryString(),
                'summary' => $this->reportService->summary($filters)['summary'],
                'filters' => $filters,
            ]
        ));
    }

    public function consolidated(Request $request): Renderable
    {
        abort_unless(auth()->user()->can('madrasa_funds.view_reports'), 403);

        $filters = $this->resolveFilters($request);

        $this->reportBreadcrumb(mf_bi('Consolidated Report'));

        $query = $this->reportService->reportQuery($filters)
            ->with(['fund', 'department', 'student'])
            ->orderByDesc('receipt_date')
            ->orderByDesc('id');

        return $this->renderViewWithBreadcrumbs('madrasafunds::pages.reports.consolidated', array_merge(
            $this->filterOptions(),
            [
                'receipts' => $query->paginate(20)->withQueryString(),
                'summary' => $this->reportService->summary($filters)['summary'],
                'filters' => $filters,
            ]
        ));
    }

    /**
     * @return array<string, mixed>
     */
    private function resolveFilters(Request $request): array
    {
        return [
            'fund_id' => $request->integer('fund_id') ?: null,
            'fund_type' => $request->string('fund_type')->value() ?: null,
            'department_id' => $request->integer('department_id') ?: null,
            'student_id' => $request->integer('student_id') ?: null,
            'date_from' => $request->string('date_from')->value() ?: null,
            'date_to' => $request->string('date_to')->value() ?: null,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function filterOptions(): array
    {
        return [
            'funds' => Fund::query()->orderBy('name')->get(),
            'departments' => Department::query()->orderBy('name')->get(),
            'fundTypes' => FundType::options(),
        ];
    }

    private function reportBreadcrumb(string $title): void
    {
        $this->setBreadcrumbTitle($title)
            ->setBreadcrumbIcon('lucide:chart-bar')
            ->addBreadcrumbItem(mf_bi('Reports'), route('admin.madrasafunds.reports.index'));
    }
}
