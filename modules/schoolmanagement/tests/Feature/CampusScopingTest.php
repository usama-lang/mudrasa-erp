<?php

declare(strict_types=1);

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\SchoolManagement\Models\Campus;
use Modules\SchoolManagement\Services\SchoolScopeService;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

pest()->use(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutMiddleware(VerifyCsrfToken::class);

    foreach (['school_campus.view', 'school_campus.create', 'school_campus.edit', 'school_campus.delete', 'school_department.view'] as $perm) {
        Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
    }

    $managerRole = Role::firstOrCreate(['name' => 'campus_manager', 'guard_name' => 'web']);
    $managerRole->syncPermissions(['school_campus.view', 'school_department.view']);

    $adminRole = Role::firstOrCreate(['name' => 'superadmin', 'guard_name' => 'web']);
    $adminRole->syncPermissions(['school_campus.view', 'school_campus.create', 'school_campus.edit', 'school_campus.delete', 'school_department.view']);
});

test('superadmin scope service returns null campus id', function () {
    $superadmin = User::factory()->create();
    $superadmin->assignRole('superadmin');

    $campusId = SchoolScopeService::getUserCampusId($superadmin);

    expect($campusId)->toBeNull();
});

test('campus manager scope service returns their campus id', function () {
    $manager = User::factory()->create();
    $manager->assignRole('campus_manager');

    $campus = Campus::factory()->create(['manager_id' => $manager->id]);

    $campusId = SchoolScopeService::getUserCampusId($manager);

    expect($campusId)->toBe($campus->id);
});

test('campus manager cannot view another campus show page', function () {
    $manager = User::factory()->create();
    $manager->assignRole('campus_manager');

    $ownCampus = Campus::factory()->create(['manager_id' => $manager->id]);
    $otherCampus = Campus::factory()->create();

    $response = $this->actingAs($manager)->get(route('school.campuses.show', $otherCampus->id));

    $response->assertStatus(403);
});

test('campus manager can view their own campus show page', function () {
    $manager = User::factory()->create();
    $manager->assignRole('campus_manager');

    $campus = Campus::factory()->create(['manager_id' => $manager->id]);

    $response = $this->actingAs($manager)->get(route('school.campuses.show', $campus->id));

    $response->assertStatus(200);
});

test('is superadmin helper returns true for superadmin role', function () {
    $user = User::factory()->create();
    $user->assignRole('superadmin');

    expect(SchoolScopeService::isSuperAdmin($user))->toBeTrue();
});

test('is superadmin helper returns false for campus manager', function () {
    $user = User::factory()->create();
    $user->assignRole('campus_manager');

    expect(SchoolScopeService::isSuperAdmin($user))->toBeFalse();
});
