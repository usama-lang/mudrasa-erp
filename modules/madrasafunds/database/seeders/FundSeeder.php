<?php

declare(strict_types=1);

namespace Modules\MadrasaFunds\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\MadrasaFunds\Enums\FundType;
use Modules\MadrasaFunds\Models\Fund;

class FundSeeder extends Seeder
{
    public function run(): void
    {
        $funds = [
            [
                'name' => 'Amumi Chanda (General Mosque Donation)',
                'name_urdu' => 'عمومی چندہ',
                'type' => FundType::DONATION->value,
                'color' => '#16a34a',
            ],
            [
                'name' => 'Tameer Fund (Mosque Construction Fund)',
                'name_urdu' => 'مسجد تعمیر فنڈ',
                'type' => FundType::DONATION->value,
                'color' => '#2563eb',
            ],
            [
                'name' => 'Trust Fund (Trust Donation)',
                'name_urdu' => 'ٹرسٹ فنڈ',
                'type' => FundType::TRUST->value,
                'color' => '#9333ea',
            ],
            [
                'name' => 'Talaba Fees (Student Fees)',
                'name_urdu' => 'طلبہ فیس',
                'type' => FundType::FEES->value,
                'color' => '#ea580c',
            ],
            [
                'name' => 'Khana Fees (Food/Meal Fees)',
                'name_urdu' => 'کھانا فیس',
                'type' => FundType::FEES->value,
                'color' => '#dc2626',
            ],
        ];

        foreach ($funds as $fund) {
            Fund::query()->updateOrCreate(
                ['name' => $fund['name']],
                array_merge($fund, ['is_active' => true]),
            );
        }
    }
}
