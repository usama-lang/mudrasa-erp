<?php

declare(strict_types=1);

namespace Modules\SchoolManagement\Services;

use App\Services\BaseService;
use Modules\SchoolManagement\Models\Section;

class SectionService extends BaseService
{
    protected function getModelClass(): string
    {
        return Section::class;
    }

    public function getSectionsDropdown(?int $campusId = null, ?int $classId = null): array
    {
        $query = Section::query()->active()->orderBy('name');

        if ($campusId !== null) {
            $query->forCampus($campusId);
        }

        if ($classId !== null) {
            $query->where('class_id', $classId);
        }

        return $query->pluck('name', 'id')->toArray();
    }
}
