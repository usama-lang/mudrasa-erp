<?php

declare(strict_types=1);

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\SchoolManagement\Models\Campus;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

pest()->use(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutMiddleware(VerifyCsrfToken::class);

    $this->admin = User::factory()->create();
    $role = Role::firstOrCreate(['name' => 'superadmin', 'guard_name' => 'web']);

    foreach (['school_campus.view', 'school_campus.create', 'school_campus.edit', 'school_campus.delete'] as $perm) {
        Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
    }

    $role->syncPermissions([
        'school_campus.view',
        'school_campus.create',
        'school_campus.edit',
        'school_campus.delete',
    ]);

    $this->admin->assignRole($role);
});

test('superadmin can view campus list', function () {
    $response = $this->actingAs($this->admin)->get(route('school.campuses.index'));

    $response->assertStatus(200);
});

test('superadmin can view create campus form', function () {
    $response = $this->actingAs($this->admin)->get(route('school.campuses.create'));

    $response->assertStatus(200);
});

test('superadmin can create campus', function () {
    $response = $this->actingAs($this->admin)->post(route('school.campuses.store'), [
        'name' => 'Test Campus',
        'code' => 'TEST',
        'email' => 'test@campus.edu',
        'phone' => '+1-555-0001',
        'status' => 'active',
    ]);

    $response->assertRedirect(route('school.campuses.index'));
    $this->assertDatabaseHas('school_campuses', ['name' => 'Test Campus', 'code' => 'TEST']);
});

test('campus code must be unique', function () {
    Campus::factory()->create(['code' => 'DUP']);

    $response = $this->actingAs($this->admin)->post(route('school.campuses.store'), [
        'name' => 'Another Campus',
        'code' => 'DUP',
        'status' => 'active',
    ]);

    $response->assertSessionHasErrors('code');
});

test('superadmin can edit campus', function () {
    $campus = Campus::factory()->create();

    $response = $this->actingAs($this->admin)->get(route('school.campuses.edit', $campus->id));

    $response->assertStatus(200);
});

test('superadmin can update campus', function () {
    $campus = Campus::factory()->create(['name' => 'Old Name']);

    $response = $this->actingAs($this->admin)->put(route('school.campuses.update', $campus->id), [
        'name' => 'Updated Name',
        'code' => $campus->code,
        'status' => 'active',
    ]);

    $response->assertRedirect(route('school.campuses.index'));
    $this->assertDatabaseHas('school_campuses', ['id' => $campus->id, 'name' => 'Updated Name']);
});

test('superadmin can delete campus', function () {
    $campus = Campus::factory()->create();

    $response = $this->actingAs($this->admin)->delete(route('school.campuses.destroy', $campus->id));

    $response->assertRedirect(route('school.campuses.index'));
    $this->assertSoftDeleted('school_campuses', ['id' => $campus->id]);
});

test('user without permission cannot access campus index', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('school.campuses.index'));

    $response->assertStatus(403);
});
