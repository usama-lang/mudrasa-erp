<?php

declare(strict_types=1);

namespace Modules\SchoolManagement\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\SchoolManagement\Database\Factories\CampusFactory;

class Campus extends Model
{
    use HasFactory, SoftDeletes;

    protected static function newFactory(): CampusFactory
    {
        return CampusFactory::new();
    }

    protected $table = 'school_campuses';

    protected $fillable = [
        'name',
        'code',
        'email',
        'phone',
        'address',
        'logo',
        'manager_id',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'string',
        ];
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class, 'campus_id');
    }

    public function classes(): HasMany
    {
        return $this->hasMany(SchoolClass::class, 'campus_id');
    }

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class, 'campus_id');
    }

    public function teachers(): HasMany
    {
        return $this->hasMany(Teacher::class, 'campus_id');
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'campus_id');
    }

    public function campusUsers(): HasMany
    {
        return $this->hasMany(CampusUser::class, 'campus_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
