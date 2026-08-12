<?php

declare(strict_types=1);

namespace Modules\SchoolManagement\Services;

use App\Enums\Hooks\AdminFilterHook;
use App\Enums\Hooks\AuthFilterHook;
use App\Enums\Hooks\PermissionFilterHook;
use App\Support\Facades\Hook;

class ModuleService
{
    public function __construct(
        private readonly MenuService $menuService
    ) {
    }

    /**
     * Bootstrap the module services.
     */
    public function bootstrap(): void
    {
        // Register sidebar menu
        Hook::addFilter(AdminFilterHook::ADMIN_MENU_GROUPS_BEFORE_SORTING, [$this->menuService, 'addMenu']);

        // Redirect to school dashboard after login
        Hook::addFilter(AuthFilterHook::LOGIN_REDIRECT_PATH, fn ($path) => route('school.dashboard'));

        // Register permissions
        Hook::addFilter(PermissionFilterHook::PERMISSION_GROUPS, [$this, 'addPermissions']);
    }

    public function addPermissions(array $groups): array
    {
        return array_merge($groups, self::getPermissions());
    }

    public static function getPermissions(): array
    {
        return [
            [
                'group_name' => 'school_campus',
                'permissions' => ['school_campus.view', 'school_campus.create', 'school_campus.edit', 'school_campus.delete'],
            ],
            [
                'group_name' => 'school_department',
                'permissions' => ['school_department.view', 'school_department.create', 'school_department.edit', 'school_department.delete'],
            ],
            [
                'group_name' => 'school_class',
                'permissions' => ['school_class.view', 'school_class.create', 'school_class.edit', 'school_class.delete'],
            ],
            [
                'group_name' => 'school_section',
                'permissions' => ['school_section.view', 'school_section.create', 'school_section.edit', 'school_section.delete'],
            ],
            [
                'group_name' => 'school_teacher',
                'permissions' => ['school_teacher.view', 'school_teacher.create', 'school_teacher.edit', 'school_teacher.delete'],
            ],
            [
                'group_name' => 'school_student',
                'permissions' => ['school_student.view', 'school_student.create', 'school_student.edit', 'school_student.delete'],
            ],
            [
                'group_name' => 'school_fee',
                'permissions' => ['school_fee.view', 'school_fee.collect', 'school_fee.report', 'school_fee.delete'],
            ],
            [
                'group_name' => 'school_report',
                'permissions' => ['school_report.view'],
            ],
        ];
    }
}
