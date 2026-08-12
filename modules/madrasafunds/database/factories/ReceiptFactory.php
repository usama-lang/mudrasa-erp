<?php

declare(strict_types=1);

namespace Modules\MadrasaFunds\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\MadrasaFunds\Models\Department;
use Modules\MadrasaFunds\Models\Fund;
use Modules\MadrasaFunds\Models\Receipt;

/**
 * @extends Factory<Receipt>
 */
class ReceiptFactory extends Factory
{
    protected $model = Receipt::class;

    public function definition(): array
    {
        return [
            'receipt_date' => now()->toDateString(),
            'fund_id' => Fund::factory(),
            'department_id' => Department::factory(),
            'student_id' => null,
            'donor_name' => $this->faker->name(),
            'donor_phone' => $this->faker->phoneNumber(),
            'amount' => $this->faker->randomFloat(2, 100, 50000),
            'description' => $this->faker->sentence(),
            'mad' => $this->faker->word(),
            'given_to' => $this->faker->name(),
            'created_by' => User::factory(),
            'notes' => null,
            'is_cancelled' => false,
        ];
    }

    public function cancelled(): static
    {
        return $this->state(fn () => [
            'is_cancelled' => true,
            'cancelled_reason' => $this->faker->sentence(),
        ]);
    }
}
