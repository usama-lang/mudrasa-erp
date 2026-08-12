<?php

declare(strict_types=1);

namespace Modules\SchoolManagement\Services;

use App\Models\User;
use App\Services\BaseService;
use Modules\SchoolManagement\Models\Campus;

class CampusService extends BaseService
{
    protected function getModelClass(): string
    {
        return Campus::class;
    }

    public function getCampusesForUser(User $user): \Illuminate\Database\Eloquent\Collection
    {
        $query = Campus::query()->with('manager');

        $campusId = SchoolScopeService::getUserCampusId($user);
        if ($campusId !== null) {
            $query->where('id', $campusId);
        }

        return $query->get();
    }

    public function getCampusesDropdown(?int $campusId = null): array
    {
        $query = Campus::query()->active()->orderBy('name');

        if ($campusId !== null) {
            $query->where('id', $campusId);
        }

        return $query->pluck('name', 'id')->toArray();
    }
}
