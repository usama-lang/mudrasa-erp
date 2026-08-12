<?php

declare(strict_types=1);

namespace Modules\SchoolManagement\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\SchoolManagement\Models\Campus;
use Modules\SchoolManagement\Models\Department;
use Modules\SchoolManagement\Models\SchoolClass;

class SchoolClassFactory extends Factory
{
    protected $model = SchoolClass::class;

    public function definition(): array
    {
        $level = $this->faker->numberBetween(1, 12);
        $campus = Campus::factory()->create();

        return [
            'campus_id' => $campus->id,
            'department_id' => Department::factory()->create(['campus_id' => $campus->id])->id,
            'name' => 'Class ' . $level,
            'numeric_name' => $level,
            'status' => 'active',
        ];
    }
}
