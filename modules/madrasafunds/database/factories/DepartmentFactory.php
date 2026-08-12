<?php

declare(strict_types=1);

namespace Modules\MadrasaFunds\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\MadrasaFunds\Models\Department;

/**
 * @extends Factory<Department>
 */
class DepartmentFactory extends Factory
{
    protected $model = Department::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->words(2, true),
            'name_urdu' => 'شعبہ ' . $this->faker->randomNumber(2),
            'description' => $this->faker->sentence(),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
