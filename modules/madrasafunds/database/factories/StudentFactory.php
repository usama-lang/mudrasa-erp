<?php

declare(strict_types=1);

namespace Modules\MadrasaFunds\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\MadrasaFunds\Models\Department;
use Modules\MadrasaFunds\Models\Student;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'father_name' => $this->faker->name('male'),
            'class' => 'درجہ ' . $this->faker->numberBetween(1, 8),
            'department_id' => Department::factory(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'is_active' => true,
        ];
    }
}
