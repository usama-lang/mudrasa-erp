<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    /**
     * @var array<int, string>
     */
    private array $permissions = [
        'madrasa_funds.view_any',
        'madrasa_funds.create',
        'madrasa_funds.edit',
        'madrasa_funds.delete',
        'madrasa_funds.print_receipt',
        'madrasa_funds.view_reports',
        'madrasa_funds.manage_funds',
        'madrasa_funds.manage_departments',
    ];

    public function up(): void
    {
        $guard = 'web';
        $now = now();

        foreach ($this->permissions as $name) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $name, 'guard_name' => $guard],
                ['name' => $name, 'guard_name' => $guard, 'created_at' => $now, 'updated_at' => $now]
            );
        }

        // Superadmin and Admin get every permission.
        foreach (['Superadmin', 'Admin'] as $roleName) {
            $this->assign($roleName, $this->permissions, $guard);
        }

        // Accountant: full operational access except destructive/management actions.
        $this->assign('accountant', [
            'madrasa_funds.view_any',
            'madrasa_funds.create',
            'madrasa_funds.edit',
            'madrasa_funds.print_receipt',
            'madrasa_funds.view_reports',
        ], $guard);
    }

    public function down(): void
    {
        $guard = 'web';

        $permissionIds = DB::table('permissions')
            ->whereIn('name', $this->permissions)
            ->where('guard_name', $guard)
            ->pluck('id');

        DB::table('role_has_permissions')->whereIn('permission_id', $permissionIds)->delete();
        DB::table('permissions')->whereIn('id', $permissionIds)->delete();
    }

    /**
     * @param  array<int, string>  $permissionNames
     */
    private function assign(string $roleName, array $permissionNames, string $guard): void
    {
        $role = DB::table('roles')->where('name', $roleName)->where('guard_name', $guard)->first();

        if (! $role) {
            return;
        }

        foreach ($permissionNames as $permName) {
            $perm = DB::table('permissions')->where('name', $permName)->where('guard_name', $guard)->first();

            if ($perm) {
                DB::table('role_has_permissions')->updateOrInsert([
                    'permission_id' => $perm->id,
                    'role_id' => $role->id,
                ]);
            }
        }
    }
};
