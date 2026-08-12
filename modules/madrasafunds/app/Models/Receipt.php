<?php

declare(strict_types=1);

namespace Modules\MadrasaFunds\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\MadrasaFunds\Database\Factories\ReceiptFactory;

class Receipt extends Model
{
    use HasFactory;

    protected $table = 'madrasa_receipts';

    protected $fillable = [
        'receipt_number',
        'receipt_date',
        'fund_id',
        'department_id',
        'student_id',
        'donor_name',
        'donor_phone',
        'amount',
        'description',
        'mad',
        'given_to',
        'created_by',
        'notes',
        'is_cancelled',
        'cancelled_reason',
    ];

    protected static function newFactory(): ReceiptFactory
    {
        return ReceiptFactory::new();
    }

    protected function casts(): array
    {
        return [
            'receipt_date' => 'date',
            'amount' => 'decimal:2',
            'is_cancelled' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Receipt $receipt): void {
            if (empty($receipt->receipt_number)) {
                $receipt->receipt_number = static::generateReceiptNumber();
            }
        });
    }

    /**
     * Generate the next sequential receipt number, e.g. RCP-2026-0001.
     */
    public static function generateReceiptNumber(): string
    {
        $year = (string) now()->year;
        $prefix = "RCP-{$year}-";

        $last = static::query()
            ->where('receipt_number', 'like', $prefix . '%')
            ->orderByDesc('receipt_number')
            ->value('receipt_number');

        $sequence = $last ? ((int) substr($last, strlen($prefix))) + 1 : 1;

        return $prefix . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }

    public function fund(): BelongsTo
    {
        return $this->belongsTo(Fund::class, 'fund_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_cancelled', false);
    }

    public function scopeForFund(Builder $query, int $fundId): Builder
    {
        return $query->where('fund_id', $fundId);
    }
}
