<?php

declare(strict_types=1);

namespace Modules\SchoolManagement\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\SchoolManagement\Database\Factories\SectionFactory;

class Section extends Model
{
    use HasFactory, SoftDeletes;

    protected static function newFactory(): SectionFactory
    {
        return SectionFactory::new();
    }

    protected $table = 'school_sections';

    protected $fillable = [
        'campus_id',
        'class_id',
        'name',
        'capacity',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'capacity' => 'integer',
            'status' => 'string',
        ];
    }

    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class, 'campus_id');
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'section_id');
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
