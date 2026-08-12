<?php

declare(strict_types=1);

namespace Modules\MadrasaFunds\Services;

use App\Services\BaseService;
use Modules\MadrasaFunds\Models\Student;

class StudentService extends BaseService
{
    protected function getModelClass(): string
    {
        return Student::class;
    }
}
