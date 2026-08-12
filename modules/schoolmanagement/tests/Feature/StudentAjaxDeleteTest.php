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

    Permission::firstOrCreate(['name' => 'school_student.delete', 'guard_name' => 'web']);
    $role->syncPermissions(['school_student.delete']);
    $this->admin->assignRole($role);
});

test('ajax delete returns json instead of redirecting', function () {
    $student = Student::factory()->create();

    $response = $this->actingAs($this->admin)->deleteJson(route('school.students.destroy', $student));

    $response->assertOk()->assertJson(['success' => true]);
    $this->assertSoftDeleted('school_students', ['id' => $student->id]);
});

test('non-ajax delete still redirects with a flash message', function () {
    $student = Student::factory()->create();

    $response = $this->actingAs($this->admin)->delete(route('school.students.destroy', $student));

    $response->assertRedirect(route('school.students.index'));
    $this->assertSoftDeleted('school_students', ['id' => $student->id]);
});
