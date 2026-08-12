<?php

declare(strict_types=1);

namespace Modules\SchoolManagement\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeePayment extends Model
{
    protected $table = 'school_fee_payments';

    protected $fillable = [
        'student_id',
        'campus_id',
        'department_id',
        'class_id',
        'section_id',
        'fee_month',
        'monthly_fee',
        'food_charges',
        'amount_paid',
        'discount',
        'balance',
        'payment_date',
        'payment_method',
        'receipt_no',
        'collected_by',
        'notes',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'fee_month'    => 'date',
            'payment_date' => 'date',
            'monthly_fee'  => 'decimal:2',
            'food_charges' => 'decimal:2',
            'amount_paid'  => 'decimal:2',
            'discount'     => 'decimal:2',
            'balance'      => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (FeePayment $payment): void {
            if (empty($payment->receipt_no)) {
                $payment->receipt_no = 'RCP-' . date('Ym') . '-' . str_pad((string) (static::max('id') + 1), 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function getTotalDueAttribute(): float
    {
        return (float) $this->monthly_fee + (float) $this->food_charges;
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
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

    public function collectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'collected_by');
    }

    public function scopePaid(Builder $query): Builder
    {
        return $query->where('status', 'paid');
    }

    public function scopePartial(Builder $query): Builder
    {
        return $query->where('status', 'partial');
    }

    public function scopeForMonth(Builder $query, string $month): Builder
    {
        return $query->whereDate('fee_month', $month);
    }
}
