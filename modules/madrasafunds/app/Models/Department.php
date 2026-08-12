<?php

declare(strict_types=1);

namespace Modules\MadrasaFunds\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\MadrasaFunds\Database\Factories\DepartmentFactory;

class Department extends Model
{
    use HasFactory;

    protected $table = 'madrasa_departments';

    protected $fillable = [
        'name',
        'name_urdu',
        'description',
        'is_active',
    ];

    protected static function newFactory(): DepartmentFactory
    {
        return DepartmentFactory::new();
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'department_id');
    }

    public function receipts(): HasMany
    {
        return $this->hasMany(Receipt::class, 'department_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
