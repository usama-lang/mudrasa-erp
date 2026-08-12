<?php

declare(strict_types=1);

namespace Modules\SchoolManagement\Services;

use App\Services\BaseService;
use Modules\SchoolManagement\Models\Department;

class DepartmentService extends BaseService
{
    protected function getModelClass(): string
    {
        return Department::class;
    }

    public function getDepartmentsDropdown(?int $campusId = null): array
    {
        $query = Department::query()->active()->orderBy('name');

        if ($campusId !== null) {
            $query->forCampus($campusId);
        }

        return $query->pluck('name', 'id')->toArray();
    }
}
