<?php

declare(strict_types=1);

namespace Modules\SchoolManagement\Services;

use App\Services\BaseService;
use Modules\SchoolManagement\Models\SchoolClass;

class SchoolClassService extends BaseService
{
    protected function getModelClass(): string
    {
        return SchoolClass::class;
    }

    public function getClassesDropdown(?int $campusId = null, ?int $departmentId = null): array
    {
        $query = SchoolClass::query()->active()->orderBy('name');

        if ($campusId !== null) {
            $query->forCampus($campusId);
        }

        if ($departmentId !== null) {
            $query->where('department_id', $departmentId);
        }

        return $query->pluck('name', 'id')->toArray();
    }
}
