<?php

declare(strict_types=1);

namespace Modules\MadrasaFunds\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\MadrasaFunds\Enums\FundType;
use Modules\MadrasaFunds\Models\Fund;

/**
 * @extends Factory<Fund>
 */
class FundFactory extends Factory
{
    protected $model = Fund::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->words(2, true) . ' Fund',
            'name_urdu' => 'فنڈ ' . $this->faker->randomNumber(2),
            'type' => $this->faker->randomElement(FundType::values()),
            'description' => $this->faker->sentence(),
            'is_active' => true,
            'color' => $this->faker->hexColor(),
        ];
    }

    public function fees(): static
    {
        return $this->state(fn () => ['type' => FundType::FEES->value]);
    }

    public function donation(): static
    {
        return $this->state(fn () => ['type' => FundType::DONATION->value]);
    }
}
