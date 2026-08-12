<?php

declare(strict_types=1);

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\MadrasaFunds\Models\Fund;
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

test('admin can view fund list', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.madrasafunds.funds.index'))
        ->assertOk();
});

test('admin can create a fund', function () {
    $this->actingAs($this->admin)
        ->post(route('admin.madrasafunds.funds.store'), [
            'name' => 'General Donation',
            'name_urdu' => 'عمومی چندہ',
            'type' => 'donation',
            'is_active' => true,
        ])
        ->assertRedirect(route('admin.madrasafunds.funds.index'));

    $this->assertDatabaseHas('madrasa_funds', ['name' => 'General Donation', 'type' => 'donation']);
});

test('admin can update a fund', function () {
    $fund = Fund::factory()->create();

    $this->actingAs($this->admin)
        ->put(route('admin.madrasafunds.funds.update', $fund), [
            'name' => 'Updated Fund',
            'type' => 'trust',
            'is_active' => true,
        ])
        ->assertRedirect(route('admin.madrasafunds.funds.index'));

    $this->assertDatabaseHas('madrasa_funds', ['id' => $fund->id, 'name' => 'Updated Fund', 'type' => 'trust']);
});

test('admin can delete a fund', function () {
    $fund = Fund::factory()->create();

    $this->actingAs($this->admin)
        ->delete(route('admin.madrasafunds.funds.destroy', $fund))
        ->assertRedirect(route('admin.madrasafunds.funds.index'));

    $this->assertDatabaseMissing('madrasa_funds', ['id' => $fund->id]);
});

test('fund data endpoint returns json', function () {
    Fund::factory()->count(3)->create();

    $this->actingAs($this->admin)
        ->getJson(route('admin.madrasafunds.funds.data'))
        ->assertOk()
        ->assertJsonStructure(['data', 'meta' => ['total', 'per_page', 'current_page', 'last_page']]);
});
