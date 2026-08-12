<?php

declare(strict_types=1);

namespace Modules\MadrasaFunds\Services;

use App\Support\Facades\Hook;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Modules\MadrasaFunds\Enums\FundType;
use Modules\MadrasaFunds\Enums\Hooks\ReceiptFilterHook;
use Modules\MadrasaFunds\Models\Receipt;

class ReportService
{
    /**
     * Shared filtered query for all receipt reports. Only counts non-cancelled
     * receipts by default.
     *
     * @param  array<string, mixed>  $filters
     */
    public function reportQuery(array $filters): Builder
    {
        $query = Receipt::query()
            ->when(! ($filters['include_cancelled'] ?? false), fn (Builder $q) => $q->where('is_cancelled', false))
            ->when($filters['fund_id'] ?? null, fn (Builder $q, $v) => $q->where('fund_id', $v))
            ->when($filters['fund_type'] ?? null, fn (Builder $q, $v) => $q->whereHas('fund', fn (Builder $f) => $f->where('type', $v)))
            ->when($filters['department_id'] ?? null, fn (Builder $q, $v) => $q->where('department_id', $v))
            ->when($filters['student_id'] ?? null, fn (Builder $q, $v) => $q->where('student_id', $v))
            ->when($filters['date_from'] ?? null, fn (Builder $q, $v) => $q->whereDate('receipt_date', '>=', $v))
            ->when($filters['date_to'] ?? null, fn (Builder $q, $v) => $q->whereDate('receipt_date', '<=', $v));

        return Hook::applyFilters(ReceiptFilterHook::REPORT_QUERY, $query);
    }

    /**
     * Totals grouped by fund.
     *
     * @param  array<string, mixed>  $filters
     */
    public function fundWise(array $filters): Collection
    {
        return $this->reportQuery($filters)
            ->selectRaw('fund_id, COUNT(*) as receipts, SUM(amount) as total')
            ->groupBy('fund_id')
            ->with('fund')
            ->get();
    }

    /**
     * Totals grouped by department.
     *
     * @param  array<string, mixed>  $filters
     */
    public function departmentWise(array $filters): Collection
    {
        return $this->reportQuery($filters)
            ->selectRaw('department_id, COUNT(*) as receipts, SUM(amount) as total')
            ->groupBy('department_id')
            ->with('department')
            ->get();
    }

    /**
     * Student fee receipts (fund type = fees), grouped by student.
     *
     * @param  array<string, mixed>  $filters
     */
    public function studentFees(array $filters): Collection
    {
        $filters['fund_type'] = FundType::FEES->value;

        return $this->reportQuery($filters)
            ->whereNotNull('student_id')
            ->selectRaw('student_id, COUNT(*) as receipts, SUM(amount) as total')
            ->groupBy('student_id')
            ->with('student.department')
            ->get();
    }

    /**
     * Consolidated, paginated receipt list with summary totals.
     *
     * @param  array<string, mixed>  $filters
     * @return array{summary: array{count: int, total: float}}
     */
    public function summary(array $filters): array
    {
        $base = $this->reportQuery($filters);

        return [
            'summary' => [
                'count' => (clone $base)->count(),
                'total' => (float) (clone $base)->sum('amount'),
            ],
        ];
    }
}
