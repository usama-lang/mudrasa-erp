<?php

declare(strict_types=1);

namespace Modules\SchoolManagement\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\SchoolManagement\Database\Factories\DepartmentFactory;

class Department extends Model
{
    use HasFactory, SoftDeletes;

    protected static function newFactory(): DepartmentFactory
    {
        return DepartmentFactory::new();
    }

    protected $table = 'school_departments';

    protected $fillable = [
        'campus_id',
        'name',
        'description',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'string',
        ];
    }

    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class, 'campus_id');
    }

    public function classes(): HasMany
    {
        return $this->hasMany(SchoolClass::class, 'department_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForCampus($query, int $campusId)
    {
        return $query->where('campus_id', $campusId);
    }
}
