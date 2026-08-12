<?php

declare(strict_types=1);

namespace Modules\MadrasaFunds\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\MadrasaFunds\Database\Factories\StudentFactory;

class Student extends Model
{
    use HasFactory;

    protected $table = 'madrasa_students';

    protected $fillable = [
        'name',
        'father_name',
        'class',
        'department_id',
        'phone',
        'address',
        'is_active',
    ];

    protected static function newFactory(): StudentFactory
    {
        return StudentFactory::new();
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function receipts(): HasMany
    {
        return $this->hasMany(Receipt::class, 'student_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeForDepartment(Builder $query, int $departmentId): Builder
    {
        return $query->where('department_id', $departmentId);
    }
}
