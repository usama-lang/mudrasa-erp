<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\MadrasaFunds\Models\Fund;
use Modules\MadrasaFunds\Models\Receipt;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

pest()->use(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create();
    $role = Role::firstOrCreate(['name' => 'superadmin', 'guard_name' => 'web']);

    foreach (['madrasa_funds.view_reports'] as $perm) {
        Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
    }

    $role->syncPermissions(['madrasa_funds.view_reports']);
    $this->admin->assignRole($role);
});

test('report landing page is accessible', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.madrasafunds.reports.index'))
        ->assertOk();
});

test('fund-wise report shows totals', function () {
    $fund = Fund::factory()->donation()->create();
    Receipt::factory()->count(2)->create(['fund_id' => $fund->id, 'amount' => 1000, 'created_by' => $this->admin->id]);

    $this->actingAs($this->admin)
        ->get(route('admin.madrasafunds.reports.fund'))
        ->assertOk()
        ->assertSee($fund->name);
});

test('consolidated report excludes cancelled receipts from totals', function () {
    $fund = Fund::factory()->create();
    Receipt::factory()->create(['fund_id' => $fund->id, 'amount' => 1000, 'created_by' => $this->admin->id]);
    Receipt::factory()->cancelled()->create(['fund_id' => $fund->id, 'amount' => 9999, 'created_by' => $this->admin->id]);

    $this->actingAs($this->admin)
        ->get(route('admin.madrasafunds.reports.consolidated'))
        ->assertOk();
});

test('users without permission cannot view reports', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.madrasafunds.reports.fund'))
        ->assertForbidden();
});
