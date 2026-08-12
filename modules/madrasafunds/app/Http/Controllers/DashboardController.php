<?php

declare(strict_types=1);

namespace Modules\MadrasaFunds\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Modules\MadrasaFunds\Models\Fund;
use Modules\MadrasaFunds\Models\Receipt;

class DashboardController extends MadrasaFundsController
{
    public function __invoke(): Renderable
    {
        abort_unless(auth()->user()->can('madrasa_funds.view_any'), 403);

        $monthStart = now()->startOfMonth();

        $thisMonth = (float) Receipt::query()->active()
            ->whereDate('receipt_date', '>=', $monthStart->toDateString())
            ->sum('amount');

        $totalCollection = (float) Receipt::query()->active()->sum('amount');

        $fundTotals = Fund::query()
            ->active()
            ->withSum(['receipts as total' => fn ($q) => $q->where('is_cancelled', false)], 'amount')
            ->orderBy('name')
            ->get();

        $recentReceipts = Receipt::query()
            ->with(['fund', 'department', 'student'])
            ->latest('receipt_date')
            ->latest('id')
            ->limit(10)
            ->get();

        $this->setBreadcrumbTitle(mf_bi('Madrasa Funds'))
            ->setBreadcrumbIcon('lucide:hand-coins')
            ->setBreadcrumbActionButton(
                route('admin.madrasafunds.receipts.create'),
                mf_bi('New Receipt'),
                'lucide:plus',
                'madrasa_funds.create'
            );

        return $this->renderViewWithBreadcrumbs('madrasafunds::pages.dashboard.index', [
            'thisMonth' => $thisMonth,
            'totalCollection' => $totalCollection,
            'fundTotals' => $fundTotals,
            'recentReceipts' => $recentReceipts,
        ]);
    }
}
