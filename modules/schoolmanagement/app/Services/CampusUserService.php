<?php

declare(strict_types=1);

namespace Modules\SchoolManagement\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Modules\SchoolManagement\Models\CampusUser;

class CampusUserService
{
    public function create(array $data, User $creator): CampusUser
    {
        return DB::transaction(function () use ($data, $creator) {
            $campusId = $data['campus_id'] ?? SchoolScopeService::getUserCampusId($creator);

            $user = User::create([
                'first_name'        => $data['first_name'],
                'last_name'         => $data['last_name'] ?? '',
                'email'             => $data['email'],
                'password'          => bcrypt($data['password']),
                'email_verified_at' => now(),
            ]);

            $spatiRole = $this->getSpatieRole($data['role_type']);
            $user->assignRole($spatiRole);

            $permissions = $data['permissions'] ?? [];
            $grantable = $this->getGrantablePermissions($creator);
            $toGrant = array_intersect($permissions, $grantable);
            if (!empty($toGrant)) {
                $user->syncPermissions($toGrant);
            }

            return CampusUser::create([
                'campus_id'         => $campusId,
                'user_id'           => $user->id,
                'role_type'         => $data['role_type'],
                'extra_permissions' => $toGrant ?: null,
                'is_active'         => true,
                'created_by'        => $creator->id,
            ]);
        });
    }

    public function update(CampusUser $campusUser, array $data, User $actor): CampusUser
    {
        return DB::transaction(function () use ($campusUser, $data, $actor) {
            $campusUser->update([
                'role_type' => $data['role_type'] ?? $campusUser->role_type,
                'is_active' => isset($data['is_active']) ? (bool) $data['is_active'] : $campusUser->is_active,
            ]);

            $permissions = $data['permissions'] ?? [];
            $grantable = $this->getGrantablePermissions($actor);
            $toGrant = array_intersect($permissions, $grantable);
            $campusUser->update(['extra_permissions' => $toGrant ?: null]);

            $spatiRole = $this->getSpatieRole($campusUser->role_type);
            $campusUser->user->syncRoles([$spatiRole]);
            $campusUser->user->syncPermissions($toGrant);

            return $campusUser->fresh();
        });
    }

    public function deactivate(CampusUser $campusUser): void
    {
        DB::transaction(function () use ($campusUser) {
            $campusUser->update(['is_active' => false]);
            $campusUser->user->syncRoles([]);
            $campusUser->user->syncPermissions([]);
        });
    }

    public function getGrantablePermissions(User $manager): array
    {
        if ($manager->hasRole(['superadmin', 'admin'])) {
            return \Spatie\Permission\Models\Permission::where('name', 'like', 'school_%')
                ->pluck('name')
                ->toArray();
        }

        return $manager->getAllPermissions()
            ->where('name', 'like', 'school_%')
            ->pluck('name')
            ->toArray();
    }

    private function getSpatieRole(string $roleType): string
    {
        return match ($roleType) {
            'main_manager'              => 'campus_main_manager',
            'assistant_manager'         => 'campus_assistant_manager',
            'account_manager'           => 'campus_account_manager',
            'assistant_account_manager' => 'campus_assistant_account_manager',
            'exam_manager'              => 'exam_manager',
            'exam_assistant_manager'    => 'exam_assistant_manager',
            'teacher'                   => 'teacher',
            default                     => 'campus_assistant_manager',
        };
    }
}
