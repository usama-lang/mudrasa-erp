<?php

declare(strict_types=1);

namespace Modules\SchoolManagement\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\SchoolManagement\Models\Campus;

class CampusFactory extends Factory
{
    protected $model = Campus::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->city() . ' Campus',
            'code' => strtoupper($this->faker->unique()->lexify('???')),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'logo' => null,
            'manager_id' => null,
            'status' => 'active',
        ];
    }

    public function inactive(): static
    {
        return $this->state(['status' => 'inactive']);
    }
}
