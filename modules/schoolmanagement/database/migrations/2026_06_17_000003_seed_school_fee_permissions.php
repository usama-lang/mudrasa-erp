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
            'school_fee.view',
            'school_fee.collect',
            'school_fee.report',
            'school_fee.delete',
        ];

        foreach ($permissions as $name) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $name, 'guard_name' => $guard],
                ['name' => $name, 'guard_name' => $guard, 'created_at' => $now, 'updated_at' => $now]
            );
        }

        // Assign all fee permissions to campus_manager and superadmin.
        foreach (['campus_manager', 'superadmin'] as $roleName) {
            $role = DB::table('roles')->where('name', $roleName)->where('guard_name', $guard)->first();
            if (! $role) {
                continue;
            }

            foreach ($permissions as $permName) {
                $perm = DB::table('permissions')->where('name', $permName)->where('guard_name', $guard)->first();
                if ($perm) {
                    DB::table('role_has_permissions')->updateOrInsert([
                        'permission_id' => $perm->id,
                        'role_id' => $role->id,
                    ]);
                }
            }
        }

        // Accountant can view, collect and report (not delete).
        $accountantRole = DB::table('roles')->where('name', 'accountant')->where('guard_name', $guard)->first();
        if ($accountantRole) {
            foreach (['school_fee.view', 'school_fee.collect', 'school_fee.report'] as $permName) {
                $perm = DB::table('permissions')->where('name', $permName)->where('guard_name', $guard)->first();
                if ($perm) {
                    DB::table('role_has_permissions')->updateOrInsert([
                        'permission_id' => $perm->id,
                        'role_id' => $accountantRole->id,
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        $guard = 'web';

        $permissionNames = [
            'school_fee.view',
            'school_fee.collect',
            'school_fee.report',
            'school_fee.delete',
        ];

        $permissionIds = DB::table('permissions')
            ->whereIn('name', $permissionNames)
            ->where('guard_name', $guard)
            ->pluck('id');

        DB::table('role_has_permissions')->whereIn('permission_id', $permissionIds)->delete();
        DB::table('permissions')->whereIn('id', $permissionIds)->delete();
    }
};
