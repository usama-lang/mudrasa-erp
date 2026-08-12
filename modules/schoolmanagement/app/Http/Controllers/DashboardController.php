<?php

declare(strict_types=1);

namespace Modules\SchoolManagement\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Modules\SchoolManagement\Models\Campus;
use Modules\SchoolManagement\Models\Department;
use Modules\SchoolManagement\Models\SchoolClass;
use Modules\SchoolManagement\Models\Section;
use Modules\SchoolManagement\Models\Student;
use Modules\SchoolManagement\Models\Teacher;
use Modules\SchoolManagement\Services\FeeService;
use Modules\SchoolManagement\Services\SchoolScopeService;

class DashboardController extends SchoolManagementController
{
    public function __construct(
        private readonly FeeService $feeService
    ) {
    }

    public function __invoke(): Renderable
    {
        abort_unless(auth()->user()->can('school_campus.view'), 403);

        $user = auth()->user();
        $campusId = SchoolScopeService::getUserCampusId($user);

        if ($campusId !== null) {
            $stats = [
                'students' => Student::where('campus_id', $campusId)->count(),
                'teachers' => Teacher::where('campus_id', $campusId)->count(),
                'departments' => Department::where('campus_id', $campusId)->count(),
                'classes' => SchoolClass::where('campus_id', $campusId)->count(),
                'sections' => Section::where('campus_id', $campusId)->count(),
            ];

            $campus = Campus::with('departments')->find($campusId);
        } else {
            $stats = [
                'campuses' => Campus::count(),
                'students' => Student::count(),
                'teachers' => Teacher::count(),
                'departments' => Department::count(),
                'classes' => SchoolClass::count(),
                'sections' => Section::count(),
            ];

            $campus = null;
        }

        $this->setBreadcrumbTitle(bi('School Dashboard'))
            ->setBreadcrumbIcon('lucide:school');

        return $this->renderViewWithBreadcrumbs('schoolmanagement::pages.dashboard.index', [
            'stats' => $stats,
            'campus' => $campus,
            'isSuperAdmin' => SchoolScopeService::isSuperAdmin($user),
            'feeStats' => $this->feeService->getDashboardStats($campusId),
        ]);
    }
}
