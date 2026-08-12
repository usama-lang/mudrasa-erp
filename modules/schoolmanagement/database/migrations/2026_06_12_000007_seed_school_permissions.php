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

        $permissions = [
            'school_campus.view', 'school_campus.create', 'school_campus.edit', 'school_campus.delete',
            'school_department.view', 'school_department.create', 'school_department.edit', 'school_department.delete',
            'school_class.view', 'school_class.create', 'school_class.edit', 'school_class.delete',
            'school_section.view', 'school_section.create', 'school_section.edit', 'school_section.delete',
            'school_teacher.view', 'school_teacher.create', 'school_teacher.edit', 'school_teacher.delete',
            'school_student.view', 'school_student.create', 'school_student.edit', 'school_student.delete',
            'school_report.view',
        ];

        foreach ($permissions as $name) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $name, 'guard_name' => $guard],
                ['name' => $name, 'guard_name' => $guard, 'created_at' => $now, 'updated_at' => $now]
            );
        }

        // Create new roles
        $roles = [
            ['name' => 'campus_manager', 'guard_name' => $guard],
            ['name' => 'accountant', 'guard_name' => $guard],
            ['name' => 'teacher', 'guard_name' => $guard],
            ['name' => 'student', 'guard_name' => $guard],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['name' => $role['name'], 'guard_name' => $role['guard_name']],
                array_merge($role, ['created_at' => $now, 'updated_at' => $now])
            );
        }

        // Assign all school permissions to campus_manager role
        $campusManagerRole = DB::table('roles')
            ->where('name', 'campus_manager')
            ->where('guard_name', $guard)
            ->first();

        if ($campusManagerRole) {
            $campusManagerPermissions = [
                'school_campus.view',
                'school_department.view', 'school_department.create', 'school_department.edit', 'school_department.delete',
                'school_class.view', 'school_class.create', 'school_class.edit', 'school_class.delete',
                'school_section.view', 'school_section.create', 'school_section.edit', 'school_section.delete',
                'school_teacher.view', 'school_teacher.create', 'school_teacher.edit', 'school_teacher.delete',
                'school_student.view', 'school_student.create', 'school_student.edit', 'school_student.delete',
                'school_report.view',
            ];

            foreach ($campusManagerPermissions as $permName) {
                $perm = DB::table('permissions')->where('name', $permName)->where('guard_name', $guard)->first();
                if ($perm) {
                    DB::table('role_has_permissions')->updateOrInsert([
                        'permission_id' => $perm->id,
                        'role_id' => $campusManagerRole->id,
                    ]);
                }
            }
        }

        // Assign view permissions to accountant
        $accountantRole = DB::table('roles')
            ->where('name', 'accountant')
            ->where('guard_name', $guard)
            ->first();

        if ($accountantRole) {
            foreach (['school_student.view', 'school_report.view'] as $permName) {
                $perm = DB::table('permissions')->where('name', $permName)->where('guard_name', $guard)->first();
                if ($perm) {
                    DB::table('role_has_permissions')->updateOrInsert([
                        'permission_id' => $perm->id,
                        'role_id' => $accountantRole->id,
                    ]);
                }
            }
        }

        // Assign view permissions to teacher
        $teacherRole = DB::table('roles')
            ->where('name', 'teacher')
            ->where('guard_name', $guard)
            ->first();

        if ($teacherRole) {
            foreach (['school_class.view', 'school_section.view', 'school_student.view'] as $permName) {
                $perm = DB::table('permissions')->where('name', $permName)->where('guard_name', $guard)->first();
                if ($perm) {
                    DB::table('role_has_permissions')->updateOrInsert([
                        'permission_id' => $perm->id,
                        'role_id' => $teacherRole->id,
                    ]);
                }
            }
        }

        // Assign view permissions to student
        $studentRole = DB::table('roles')
            ->where('name', 'student')
            ->where('guard_name', $guard)
            ->first();

        if ($studentRole) {
            foreach (['school_class.view', 'school_section.view'] as $permName) {
                $perm = DB::table('permissions')->where('name', $permName)->where('guard_name', $guard)->first();
                if ($perm) {
                    DB::table('role_has_permissions')->updateOrInsert([
                        'permission_id' => $perm->id,
                        'role_id' => $studentRole->id,
                    ]);
                }
            }
        }

        // Assign all school permissions to existing superadmin role
        $superadminRole = DB::table('roles')
            ->where('name', 'superadmin')
            ->where('guard_name', $guard)
            ->first();

        if ($superadminRole) {
            foreach ($permissions as $permName) {
                $perm = DB::table('permissions')->where('name', $permName)->where('guard_name', $guard)->first();
                if ($perm) {
                    DB::table('role_has_permissions')->updateOrInsert([
                        'permission_id' => $perm->id,
                        'role_id' => $superadminRole->id,
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        $guard = 'web';

        $permissionNames = [
            'school_campus.view', 'school_campus.create', 'school_campus.edit', 'school_campus.delete',
            'school_department.view', 'school_department.create', 'school_department.edit', 'school_department.delete',
            'school_class.view', 'school_class.create', 'school_class.edit', 'school_class.delete',
            'school_section.view', 'school_section.create', 'school_section.edit', 'school_section.delete',
            'school_teacher.view', 'school_teacher.create', 'school_teacher.edit', 'school_teacher.delete',
            'school_student.view', 'school_student.create', 'school_student.edit', 'school_student.delete',
            'school_report.view',
        ];

        $permissionIds = DB::table('permissions')
            ->whereIn('name', $permissionNames)
            ->where('guard_name', $guard)
            ->pluck('id');

        DB::table('role_has_permissions')->whereIn('permission_id', $permissionIds)->delete();
        DB::table('permissions')->whereIn('id', $permissionIds)->delete();

        DB::table('roles')
            ->whereIn('name', ['campus_manager', 'accountant', 'teacher', 'student'])
            ->where('guard_name', $guard)
            ->delete();
    }
};
