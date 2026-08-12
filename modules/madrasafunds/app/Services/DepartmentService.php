<?php

declare(strict_types=1);

namespace Modules\MadrasaFunds\Services;

use App\Services\BaseService;
use Modules\MadrasaFunds\Models\Department;

class DepartmentService extends BaseService
{
    protected function getModelClass(): string
    {
        return Department::class;
    }

    /**
     * @return array<int, string>
     */
    public function getDepartmentsDropdown(): array
    {
        return Department::query()->active()->orderBy('name')->pluck('name', 'id')->toArray();
    }
}
