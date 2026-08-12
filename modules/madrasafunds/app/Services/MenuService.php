<?php

declare(strict_types=1);

namespace Modules\MadrasaFunds\Services;

use App\Services\MenuService\AdminMenuItem;
use Illuminate\Support\Facades\Route;

class MenuService
{
    /**
     * Add the module menu to the admin sidebar.
     */
    public function addMenu(array $groups): array
    {
        $groups[__('Main')][] = $this->getMenu();

        return $groups;
    }

    /**
     * Build the Madrasa Funds menu tree.
     */
    public function getMenu(): AdminMenuItem
    {
        $children = [
            (new AdminMenuItem())->setAttributes([
                'label' => mf_bi('Overview'),
                'route' => route('admin.madrasafunds.dashboard'),
                'active' => Route::is('admin.madrasafunds.dashboard'),
                'priority' => 1,
                'permissions' => ['madrasa_funds.view_any'],
            ]),
            (new AdminMenuItem())->setAttributes([
                'label' => mf_bi('New Receipt'),
                'route' => route('admin.madrasafunds.receipts.create'),
                'active' => Route::is('admin.madrasafunds.receipts.create'),
                'priority' => 10,
                'permissions' => ['madrasa_funds.create'],
            ]),
            (new AdminMenuItem())->setAttributes([
                'label' => mf_bi('Receipts'),
                'route' => route('admin.madrasafunds.receipts.index'),
                'active' => Route::is('admin.madrasafunds.receipts.*') && ! Route::is('admin.madrasafunds.receipts.create'),
                'priority' => 20,
                'permissions' => ['madrasa_funds.view_any'],
            ]),
            (new AdminMenuItem())->setAttributes([
                'label' => mf_bi('Students'),
                'route' => route('admin.madrasafunds.students.index'),
                'active' => Route::is('admin.madrasafunds.students.*'),
                'priority' => 30,
                'permissions' => ['madrasa_funds.view_any'],
            ]),
            (new AdminMenuItem())->setAttributes([
                'label' => mf_bi('Reports'),
                'route' => route('admin.madrasafunds.reports.index'),
                'active' => Route::is('admin.madrasafunds.reports.*'),
                'priority' => 40,
                'permissions' => ['madrasa_funds.view_reports'],
            ]),
            (new AdminMenuItem())->setAttributes([
                'label' => mf_bi('Funds'),
                'route' => route('admin.madrasafunds.funds.index'),
                'active' => Route::is('admin.madrasafunds.funds.*'),
                'priority' => 50,
                'permissions' => ['madrasa_funds.manage_funds'],
            ]),
            (new AdminMenuItem())->setAttributes([
                'label' => mf_bi('Departments'),
                'route' => route('admin.madrasafunds.departments.index'),
                'active' => Route::is('admin.madrasafunds.departments.*'),
                'priority' => 60,
                'permissions' => ['madrasa_funds.manage_departments'],
            ]),
        ];

        return (new AdminMenuItem())->setAttributes([
            'label' => mf_bi('Madrasa Funds'),
            'icon' => 'lucide:hand-coins',
            'id' => 'madrasa-funds',
            'active' => Route::is('admin.madrasafunds.*'),
            'priority' => 3,
            'permissions' => [
                'madrasa_funds.view_any',
                'madrasa_funds.view_reports',
                'madrasa_funds.manage_funds',
                'madrasa_funds.manage_departments',
            ],
        ])->setChildren($children);
    }
}
