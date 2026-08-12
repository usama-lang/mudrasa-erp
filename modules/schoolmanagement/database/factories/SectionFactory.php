<?php

declare(strict_types=1);

namespace Modules\SchoolManagement\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\SchoolManagement\Models\Campus;
use Modules\SchoolManagement\Models\SchoolClass;
use Modules\SchoolManagement\Models\Section;

class SectionFactory extends Factory
{
    protected $model = Section::class;

    public function definition(): array
    {
        return [
            'campus_id' => Campus::factory(),
            'class_id' => SchoolClass::factory(),
            'name' => 'Section ' . $this->faker->randomElement(['A', 'B', 'C', 'D']),
            'capacity' => $this->faker->numberBetween(20, 40),
            'status' => 'active',
        ];
    }
}
