<?php

declare(strict_types=1);

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\SchoolManagement\Models\Student;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

pest()->use(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutMiddleware(VerifyCsrfToken::class);

    $this->admin = User::factory()->create();
    $role = Role::firstOrCreate(['name' => 'superadmin', 'guard_name' => 'web']);

    foreach (['school_student.view', 'school_student.edit'] as $perm) {
        Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
    }

    $role->syncPermissions(['school_student.view', 'school_student.edit']);
    $this->admin->assignRole($role);
});

test('user with permission can update a student para quantity', function () {
    $student = Student::factory()->create(['para_quantity' => null]);

    $response = $this->actingAs($this->admin)->patchJson(
        route('school.students.para-quantity.update', $student),
        ['para_quantity' => 5.5]
    );

    $response->assertOk()->assertJson(['success' => true, 'para_quantity' => 5.5]);

    $this->assertDatabaseHas('school_students', [
        'id' => $student->id,
        'para_quantity' => 5.5,
    ]);
});

test('para quantity must be numeric and within range', function () {
    $student = Student::factory()->create();

    $this->actingAs($this->admin)->patchJson(
        route('school.students.para-quantity.update', $student),
        ['para_quantity' => 'not-a-number']
    )->assertStatus(422);

    $this->actingAs($this->admin)->patchJson(
        route('school.students.para-quantity.update', $student),
        ['para_quantity' => 999]
    )->assertStatus(422);
});

test('user without permission cannot update para quantity', function () {
    $user = User::factory()->create();
    $student = Student::factory()->create();

    $this->actingAs($user)->patchJson(
        route('school.students.para-quantity.update', $student),
        ['para_quantity' => 3]
    )->assertStatus(403);
});

test('students data endpoint returns para quantity', function () {
    Student::factory()->create(['para_quantity' => 12.5]);

    $response = $this->actingAs($this->admin)->getJson(route('school.students.data'));

    $response->assertOk()->assertJsonFragment(['para_quantity' => 12.5]);
});
