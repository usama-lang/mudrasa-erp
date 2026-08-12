<?php

declare(strict_types=1);

namespace Modules\MadrasaFunds\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\MadrasaFunds\Database\Factories\FundFactory;
use Modules\MadrasaFunds\Enums\FundType;

class Fund extends Model
{
    use HasFactory;

    protected $table = 'madrasa_funds';

    protected $fillable = [
        'name',
        'name_urdu',
        'type',
        'description',
        'is_active',
        'color',
    ];

    protected static function newFactory(): FundFactory
    {
        return FundFactory::new();
    }

    protected function casts(): array
    {
        return [
            'type' => FundType::class,
            'is_active' => 'boolean',
        ];
    }

    public function receipts(): HasMany
    {
        return $this->hasMany(Receipt::class, 'fund_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOfType(Builder $query, FundType|string $type): Builder
    {
        return $query->where('type', $type instanceof FundType ? $type->value : $type);
    }
}
