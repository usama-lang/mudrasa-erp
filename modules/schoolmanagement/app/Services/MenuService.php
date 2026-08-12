<?php

declare(strict_types=1);

namespace Modules\SchoolManagement\Services;

use App\Services\MenuService\AdminMenuItem;
use Illuminate\Support\Facades\Route;

class MenuService
{
    public function addMenu(array $groups): array
    {
        $groups[__('Main')][] = $this->getMenu();

        return $groups;
    }

    public function getMenu(): AdminMenuItem
    {
        $children = [
            (new AdminMenuItem())->setAttributes([
                'label'       => bi('Dashboard'),
                'route'       => route('school.dashboard'),
                'active'      => Route::is('school.dashboard'),
                'priority'    => 1,
                'permissions' => ['school_campus.view'],
            ]),
            (new AdminMenuItem())->setAttributes([
                'label'       => bi('Campuses'),
                'route'       => route('school.campuses.index'),
                'active'      => Route::is('school.campuses.*'),
                'priority'    => 10,
                'permissions' => ['school_campus.view'],
            ]),
            (new AdminMenuItem())->setAttributes([
                'label'       => bi('Departments'),
                'route'       => route('school.departments.index'),
                'active'      => Route::is('school.departments.*'),
                'priority'    => 20,
                'permissions' => ['school_department.view'],
            ]),
            (new AdminMenuItem())->setAttributes([
                'label'       => bi('Classes'),
                'route'       => route('school.classes.index'),
                'active'      => Route::is('school.classes.*'),
                'priority'    => 30,
                'permissions' => ['school_class.view'],
            ]),
            (new AdminMenuItem())->setAttributes([
                'label'       => bi('Sections'),
                'route'       => route('school.sections.index'),
                'active'      => Route::is('school.sections.*'),
                'priority'    => 40,
                'permissions' => ['school_section.view'],
            ]),
            (new AdminMenuItem())->setAttributes([
                'label'       => bi('Teachers'),
                'route'       => route('school.teachers.index'),
                'active'      => Route::is('school.teachers.*'),
                'priority'    => 50,
                'permissions' => ['school_teacher.view'],
            ]),
            (new AdminMenuItem())->setAttributes([
                'label'       => bi('Students'),
                'route'       => route('school.students.index'),
                'active'      => Route::is('school.students.*'),
                'priority'    => 60,
                'permissions' => ['school_student.view'],
            ]),
            (new AdminMenuItem())->setAttributes([
                'label'       => bi('Fees'),
                'route'       => route('school.fees.index'),
                'active'      => Route::is('school.fees.*') && ! Route::is('school.fees.reports.*'),
                'priority'    => 65,
                'permissions' => ['school_fee.view'],
            ]),
            (new AdminMenuItem())->setAttributes([
                'label'       => bi('Fee Reports'),
                'route'       => route('school.fees.reports.index'),
                'active'      => Route::is('school.fees.reports.*'),
                'priority'    => 66,
                'permissions' => ['school_fee.report'],
            ]),
            (new AdminMenuItem())->setAttributes([
                'label'       => bi('Staff'),
                'route'       => route('school.staff.index'),
                'active'      => Route::is('school.staff.*'),
                'priority'    => 70,
                'permissions' => ['school_manager.view'],
            ]),
        ];

        return (new AdminMenuItem())->setAttributes([
            'label'       => bi('School Management'),
            'icon'        => 'lucide:school',
            'id'          => 'school-management',
            'active'      => Route::is('school.*'),
            'priority'    => 2,
            'permissions' => ['school_campus.view', 'school_department.view', 'school_class.view', 'school_section.view', 'school_teacher.view', 'school_student.view'],
        ])->setChildren($children);
    }
}
