<?php

declare(strict_types=1);

namespace Modules\MadrasaFunds\Enums;

enum FundType: string
{
    case DONATION = 'donation';
    case FEES = 'fees';
    case TRUST = 'trust';

    /**
     * Human readable label (bilingual where the helper is available).
     */
    public function label(): string
    {
        return match ($this) {
            self::DONATION => function_exists('mf_bi') ? mf_bi('Donation') : 'Donation',
            self::FEES => function_exists('mf_bi') ? mf_bi('Fees') : 'Fees',
            self::TRUST => function_exists('mf_bi') ? mf_bi('Trust') : 'Trust',
        };
    }

    /**
     * @return array<string, string> value => label map for select inputs.
     */
    public static function options(): array
    {
        return [
            self::DONATION->value => self::DONATION->label(),
            self::FEES->value => self::FEES->label(),
            self::TRUST->value => self::TRUST->label(),
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(fn (self $case) => $case->value, self::cases());
    }
}
