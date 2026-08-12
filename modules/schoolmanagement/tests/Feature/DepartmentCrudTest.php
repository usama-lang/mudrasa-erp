<?php

declare(strict_types=1);

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\SchoolManagement\Models\Campus;
use Modules\SchoolManagement\Models\Department;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

pest()->use(RefreshDatabase::class);


beforeEach(function () {
    $this->withoutMiddleware(VerifyCsrfToken::class);

    $this->admin = User::factory()->create();
    $role = Role::firstOrCreate(['name' => 'superadmin', 'guard_name' => 'web']);

    foreach (['school_department.view', 'school_department.create', 'school_department.edit', 'school_department.delete'] as $perm) {
        Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
    }

    $role->syncPermissions([
        'school_department.view',
        'school_department.create',
        'school_department.edit',
        'school_department.delete',
    ]);

    $this->admin->assignRole($role);
    $this->campus = Campus::factory()->create();
});

test('admin can view department list', function () {
    $response = $this->actingAs($this->admin)->get(route('school.departments.index'));

    $response->assertStatus(200);
});

test('admin can create department', function () {
    $response = $this->actingAs($this->admin)->post(route('school.departments.store'), [
        'campus_id' => $this->campus->id,
        'name' => 'Computer Science',
        'description' => 'CS Department',
        'status' => 'active',
    ]);

    $response->assertRedirect(route('school.departments.index'));
    $this->assertDatabaseHas('school_departments', ['name' => 'Computer Science', 'campus_id' => $this->campus->id]);
});

test('admin can update department', function () {
    $dept = Department::factory()->create(['campus_id' => $this->campus->id]);

    $response = $this->actingAs($this->admin)->put(route('school.departments.update', $dept->id), [
        'campus_id' => $this->campus->id,
        'name' => 'Updated Department',
        'status' => 'active',
    ]);

    $response->assertRedirect(route('school.departments.index'));
    $this->assertDatabaseHas('school_departments', ['id' => $dept->id, 'name' => 'Updated Department']);
});

test('admin can delete department', function () {
    $dept = Department::factory()->create(['campus_id' => $this->campus->id]);

    $response = $this->actingAs($this->admin)->delete(route('school.departments.destroy', $dept->id));

    $response->assertRedirect(route('school.departments.index'));
    $this->assertSoftDeleted('school_departments', ['id' => $dept->id]);
});
