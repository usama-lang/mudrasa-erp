<?php

declare(strict_types=1);

namespace Modules\SchoolManagement\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\SchoolManagement\Database\Factories\StudentFactory;

class Student extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected static function newFactory(): StudentFactory
    {
        return StudentFactory::new();
    }

    protected $table = 'school_students';

    protected $fillable = [
        'campus_id',
        'department_id',
        'class_id',
        'section_id',
        'user_id',
        'admission_no',
        'admission_date',
        'student_name',
        'father_name',
        'b_form_no',
        'father_cnic',
        'current_address',
        'permanent_address',
        'date_of_birth',
        'age',
        'education_level',
        'para_quantity',
        'father_profession',
        'designation',
        'monthly_fee',
        'phone_no',
        'whatsapp_no',
        'residential_status',
        'food_at_madrasa',
        'food_charges',
        'guardian1_name',
        'guardian1_relation',
        'guardian1_phone',
        'guardian2_name',
        'guardian2_relation',
        'guardian2_phone',
        'agreement_date',
        'student_image',
        'document_file',
        'status',
        'enrollment_status',
        'leaving_date',
        'status_remarks',
    ];

    protected function casts(): array
    {
        return [
            'admission_date' => 'date',
            'date_of_birth' => 'date',
            'agreement_date' => 'date',
            'leaving_date' => 'date',
            'monthly_fee' => 'decimal:2',
            'food_charges' => 'decimal:2',
            'para_quantity' => 'decimal:1',
            'status' => 'boolean',
            'food_at_madrasa' => 'boolean',
        ];
    }

    public function isEnrolled(): bool
    {
        return $this->enrollment_status === 'enrolled';
    }

    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class, 'campus_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeForCampus($query, int $campusId)
    {
        return $query->where('campus_id', $campusId);
    }
}
