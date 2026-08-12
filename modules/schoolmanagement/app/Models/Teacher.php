<?php

declare(strict_types=1);

namespace Modules\SchoolManagement\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\SchoolManagement\Database\Factories\TeacherFactory;

class Teacher extends Model
{
    use HasFactory, SoftDeletes;

    protected static function newFactory(): TeacherFactory
    {
        return TeacherFactory::new();
    }

    protected $table = 'school_teachers';

    protected $fillable = [
        'campus_id',
        'user_id',
        'employee_id',
        'designation',
        'qualification',
        'salary',
        'joining_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'salary' => 'decimal:2',
            'joining_date' => 'date',
            'status' => 'string',
        ];
    }

    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class, 'campus_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
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
