<?php

declare(strict_types=1);

namespace Modules\SchoolManagement\Services;

use App\Models\User;
use Modules\SchoolManagement\Models\Campus;
use Modules\SchoolManagement\Models\CampusUser;

class SchoolScopeService
{
    public static function getUserCampusId(User $user): ?int
    {
        if ($user->hasRole(['superadmin', 'admin'])) {
            return null;
        }

        $cu = CampusUser::where('user_id', $user->id)->where('is_active', true)->first();

        if ($cu !== null) {
            return $cu->campus_id;
        }

        return Campus::where('manager_id', $user->id)->value('id');
    }

    public static function getUserRoleType(User $user): ?string
    {
        if ($user->hasRole(['superadmin', 'admin'])) {
            return null;
        }

        $cu = CampusUser::where('user_id', $user->id)->where('is_active', true)->first();

        if ($cu !== null) {
            return $cu->role_type;
        }

        return Campus::where('manager_id', $user->id)->exists() ? 'main_manager' : null;
    }

    public static function isSuperAdmin(User $user): bool
    {
        return $user->hasRole(['superadmin', 'admin']);
    }

    public static function canManageStaff(User $user): bool
    {
        if (self::isSuperAdmin($user)) {
            return true;
        }

        return $user->can('school_manager.view');
    }

    public static function canManageAccounts(User $user): bool
    {
        if (self::isSuperAdmin($user)) {
            return true;
        }

        return $user->can('school_account.view');
    }

    public static function canManageExams(User $user): bool
    {
        if (self::isSuperAdmin($user)) {
            return true;
        }

        return $user->can('school_exam.view');
    }
}
