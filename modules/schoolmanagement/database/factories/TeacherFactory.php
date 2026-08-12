<?php

declare(strict_types=1);

namespace Modules\SchoolManagement\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\SchoolManagement\Models\Campus;
use Modules\SchoolManagement\Models\Teacher;

class TeacherFactory extends Factory
{
    protected $model = Teacher::class;

    public function definition(): array
    {
        return [
            'campus_id' => Campus::factory(),
            'user_id' => User::factory(),
            'employee_id' => 'EMP-' . str_pad((string) $this->faker->unique()->numberBetween(1, 99999), 5, '0', STR_PAD_LEFT),
            'designation' => $this->faker->randomElement(['Teacher', 'Senior Teacher', 'Head Teacher', 'Lecturer']),
            'qualification' => $this->faker->randomElement(['B.Ed', 'M.Ed', 'M.Sc', 'B.Sc', 'Ph.D']),
            'salary' => $this->faker->randomFloat(2, 30000, 150000),
            'joining_date' => $this->faker->date(),
            'status' => 'active',
        ];
    }
}
