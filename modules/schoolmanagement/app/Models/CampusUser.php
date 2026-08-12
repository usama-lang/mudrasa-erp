<?php

declare(strict_types=1);

namespace Modules\SchoolManagement\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampusUser extends Model
{
    protected $table = 'school_campus_users';

    protected $fillable = [
        'campus_id',
        'user_id',
        'role_type',
        'extra_permissions',
        'is_active',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'extra_permissions' => 'array',
            'is_active'         => 'boolean',
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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
