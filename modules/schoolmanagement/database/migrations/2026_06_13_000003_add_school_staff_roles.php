<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $guard = 'web';
        $now = now();

        // New permissions for staff management
        $newPermissions = [
            'school_manager.view', 'school_manager.create', 'school_manager.edit', 'school_manager.delete',
            'school_account.view', 'school_account.create', 'school_account.edit', 'school_account.delete',
            'school_exam.view', 'school_exam.create', 'school_exam.edit', 'school_exam.delete',
            'school_staff.view',
        ];

        foreach ($newPermissions as $name) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $name, 'guard_name' => $guard],
                ['name' => $name, 'guard_name' => $guard, 'created_at' => $now, 'updated_at' => $now]
            );
        }

        // New roles
        $newRoles = [
            'campus_main_manager',
            'campus_assistant_manager',
            'campus_account_manager',
            'campus_assistant_account_manager',
            'exam_manager',
            'exam_assistant_manager',
        ];

        foreach ($newRoles as $roleName) {
            DB::table('roles')->updateOrInsert(
                ['name' => $roleName, 'guard_name' => $guard],
                ['name' => $roleName, 'guard_name' => $guard, 'created_at' => $now, 'updated_at' => $now]
            );
        }

        // Helper to assign permissions to a role
        $assignPermissions = function (string $roleName, array $permNames) use ($guard): void {
            $role = DB::table('roles')->where('name', $roleName)->where('guard_name', $guard)->first();
            if (! $role) {
                return;
            }
            foreach ($permNames as $permName) {
                $perm = DB::table('permissions')->where('name', $permName)->where('guard_name', $guard)->first();
                if ($perm) {
                    DB::table('role_has_permissions')->updateOrInsert([
                        'permission_id' => $perm->id,
                        'role_id'       => $role->id,
                    ]);
                }
            }
        };

        // superadmin gets all new permissions
        $allExistingSchoolPerms = DB::table('permissions')
            ->where('guard_name', $guard)
            ->where('name', 'like', 'school_%')
            ->pluck('name')
            ->toArray();

        $assignPermissions('superadmin', $allExistingSchoolPerms);

        // campus_main_manager — everything except campus create/delete
        $mainManagerPerms = DB::table('permissions')
            ->where('guard_name', $guard)
            ->where('name', 'like', 'school_%')
            ->whereNotIn('name', ['school_campus.create', 'school_campus.delete'])
            ->pluck('name')
            ->toArray();
        $assignPermissions('campus_main_manager', $mainManagerPerms);

        // campus_assistant_manager — same as main but cannot manage other managers
        $assistantManagerPerms = array_filter($mainManagerPerms, fn ($p) => ! str_starts_with($p, 'school_manager.'));
        $assignPermissions('campus_assistant_manager', array_values($assistantManagerPerms));

        // campus_account_manager
        $assignPermissions('campus_account_manager', [
            'school_account.view', 'school_account.create', 'school_account.edit', 'school_account.delete',
            'school_student.view',
            'school_staff.view',
        ]);

        // campus_assistant_account_manager
        $assignPermissions('campus_assistant_account_manager', [
            'school_account.view', 'school_account.create',
            'school_student.view',
        ]);

        // exam_manager
        $assignPermissions('exam_manager', [
            'school_exam.view', 'school_exam.create', 'school_exam.edit', 'school_exam.delete',
            'school_class.view', 'school_section.view', 'school_student.view',
            'school_staff.view',
        ]);

        // exam_assistant_manager
        $assignPermissions('exam_assistant_manager', [
            'school_exam.view', 'school_exam.create',
            'school_class.view', 'school_section.view', 'school_student.view',
        ]);
    }

    public function down(): void
    {
        $guard = 'web';

        $permNames = [
            'school_manager.view', 'school_manager.create', 'school_manager.edit', 'school_manager.delete',
            'school_account.view', 'school_account.create', 'school_account.edit', 'school_account.delete',
            'school_exam.view', 'school_exam.create', 'school_exam.edit', 'school_exam.delete',
            'school_staff.view',
        ];

        $permIds = DB::table('permissions')
            ->whereIn('name', $permNames)
            ->where('guard_name', $guard)
            ->pluck('id');

        DB::table('role_has_permissions')->whereIn('permission_id', $permIds)->delete();
        DB::table('permissions')->whereIn('id', $permIds)->delete();

        DB::table('roles')
            ->whereIn('name', [
                'campus_main_manager', 'campus_assistant_manager',
                'campus_account_manager', 'campus_assistant_account_manager',
                'exam_manager', 'exam_assistant_manager',
            ])
            ->where('guard_name', $guard)
            ->delete();
    }
};
