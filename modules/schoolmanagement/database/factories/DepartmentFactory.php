<?php

declare(strict_types=1);

namespace Modules\SchoolManagement\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\SchoolManagement\Models\Campus;
use Modules\SchoolManagement\Models\Department;

class DepartmentFactory extends Factory
{
    protected $model = Department::class;

    public function definition(): array
    {
        return [
            'campus_id' => Campus::factory(),
            'name' => $this->faker->randomElement([
                'Computer Science', 'Mathematics', 'Physics', 'Chemistry',
                'Biology', 'English', 'Urdu', 'Social Studies', 'Arts',
            ]) . ' (' . $this->faker->unique()->word() . ')',
            'description' => $this->faker->sentence(),
            'status' => 'active',
        ];
    }
}
