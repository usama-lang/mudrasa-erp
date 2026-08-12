<?php

declare(strict_types=1);

namespace Modules\SchoolManagement\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\SchoolManagement\Database\Factories\SchoolClassFactory;

class SchoolClass extends Model
{
    use HasFactory, SoftDeletes;

    protected static function newFactory(): SchoolClassFactory
    {
        return SchoolClassFactory::new();
    }

    protected $table = 'school_classes';

    protected $fillable = [
        'campus_id',
        'department_id',
        'name',
        'numeric_name',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'numeric_name' => 'integer',
            'status' => 'string',
        ];
    }

    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class, 'campus_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class, 'class_id');
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'class_id');
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
