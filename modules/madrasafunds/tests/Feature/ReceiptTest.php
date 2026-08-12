<?php

declare(strict_types=1);

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\MadrasaFunds\Models\Fund;
use Modules\MadrasaFunds\Models\Receipt;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

pest()->use(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutMiddleware(VerifyCsrfToken::class);

    $this->admin = User::factory()->create();
    $role = Role::firstOrCreate(['name' => 'superadmin', 'guard_name' => 'web']);

    $permissions = [
        'madrasa_funds.view_any',
        'madrasa_funds.create',
        'madrasa_funds.edit',
        'madrasa_funds.delete',
        'madrasa_funds.print_receipt',
        'madrasa_funds.view_reports',
        'madrasa_funds.manage_funds',
        'madrasa_funds.manage_departments',
    ];

    foreach ($permissions as $perm) {
        Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
    }

    $role->syncPermissions($permissions);
    $this->admin->assignRole($role);
});

test('admin can view receipt list', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.madrasafunds.receipts.index'))
        ->assertOk();
});

test('admin can create a donation receipt and it gets an auto receipt number', function () {
    $fund = Fund::factory()->donation()->create();

    $response = $this->actingAs($this->admin)
        ->post(route('admin.madrasafunds.receipts.store'), [
            'receipt_date' => now()->toDateString(),
            'fund_id' => $fund->id,
            'donor_name' => 'Abdullah',
            'amount' => 5000,
        ]);

    $receipt = Receipt::query()->latest('id')->first();

    expect($receipt)->not->toBeNull();
    $response->assertRedirect(route('admin.madrasafunds.receipts.print', $receipt));

    $this->assertDatabaseHas('madrasa_receipts', [
        'fund_id' => $fund->id,
        'donor_name' => 'Abdullah',
        'created_by' => $this->admin->id,
    ]);

    expect($receipt->receipt_number)->toStartWith('RCP-' . now()->year . '-');
});

test('receipt numbers increment sequentially', function () {
    $fund = Fund::factory()->donation()->create();

    $first = Receipt::create([
        'receipt_date' => now()->toDateString(),
        'fund_id' => $fund->id,
        'amount' => 100,
        'created_by' => $this->admin->id,
    ]);

    $second = Receipt::create([
        'receipt_date' => now()->toDateString(),
        'fund_id' => $fund->id,
        'amount' => 200,
        'created_by' => $this->admin->id,
    ]);

    expect($first->receipt_number)->toBe('RCP-' . now()->year . '-0001');
    expect($second->receipt_number)->toBe('RCP-' . now()->year . '-0002');
});

test('admin can cancel a receipt', function () {
    $receipt = Receipt::factory()->create(['created_by' => $this->admin->id]);

    $this->actingAs($this->admin)
        ->patch(route('admin.madrasafunds.receipts.cancel', $receipt), [
            'cancelled_reason' => 'Duplicate entry',
        ])
        ->assertRedirect(route('admin.madrasafunds.receipts.index'));

    $this->assertDatabaseHas('madrasa_receipts', [
        'id' => $receipt->id,
        'is_cancelled' => true,
        'cancelled_reason' => 'Duplicate entry',
    ]);
});

test('admin can print a receipt', function () {
    $receipt = Receipt::factory()->create(['created_by' => $this->admin->id]);

    $this->actingAs($this->admin)
        ->get(route('admin.madrasafunds.receipts.print', $receipt))
        ->assertOk()
        ->assertSee($receipt->receipt_number);
});

test('creating a receipt requires an amount', function () {
    $fund = Fund::factory()->create();

    $this->actingAs($this->admin)
        ->post(route('admin.madrasafunds.receipts.store'), [
            'receipt_date' => now()->toDateString(),
            'fund_id' => $fund->id,
        ])
        ->assertSessionHasErrors('amount');
});
