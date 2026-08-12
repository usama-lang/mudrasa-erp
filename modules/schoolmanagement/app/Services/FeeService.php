<?php

declare(strict_types=1);

namespace Modules\SchoolManagement\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Modules\SchoolManagement\Models\FeePayment;
use Modules\SchoolManagement\Models\Student;

class FeeService
{
    /**
     * Create a fee payment, calculating the balance and status.
     */
    public function collectFee(array $data): FeePayment
    {
        return DB::transaction(function () use ($data) {
            /** @var Student $student */
            $student = Student::findOrFail($data['student_id']);

            $monthlyFee = (float) $student->monthly_fee;
            $foodCharges = ($student->residential_status === 'non_resident' && $student->food_at_madrasa)
                ? (float) $student->food_charges
                : 0.0;

            $discount = (float) ($data['discount'] ?? 0);
            $amountPaid = (float) $data['amount_paid'];
            $totalDue = $monthlyFee + $foodCharges;
            $balance = round($totalDue - $discount - $amountPaid, 2);

            $status = $this->resolveStatus($amountPaid, $balance);

            // Normalise fee_month (Y-m) to the first day of the month.
            $feeMonth = Carbon::createFromFormat('Y-m', $data['fee_month'])->startOfMonth()->toDateString();

            return FeePayment::create([
                'student_id'     => $student->id,
                'campus_id'      => $student->campus_id,
                'department_id'  => $student->department_id,
                'class_id'       => $student->class_id,
                'section_id'     => $student->section_id,
                'fee_month'      => $feeMonth,
                'monthly_fee'    => $monthlyFee,
                'food_charges'   => $foodCharges,
                'amount_paid'    => $amountPaid,
                'discount'       => $discount,
                'balance'        => max($balance, 0),
                'payment_date'   => $data['payment_date'] ?? now()->toDateString(),
                'payment_method' => $data['payment_method'] ?? 'cash',
                'collected_by'   => auth()->id(),
                'notes'          => $data['notes'] ?? null,
                'status'         => $status,
            ]);
        });
    }

    private function resolveStatus(float $amountPaid, float $balance): string
    {
        if ($amountPaid <= 0) {
            return 'waived';
        }

        return $balance > 0 ? 'partial' : 'paid';
    }

    /**
     * Return the list of unpaid months for an enrolled student, from
     * admission_date until the current month.
     *
     * @return array<int, array{value: string, label: string}>
     */
    public function getStudentPendingMonths(Student $student): array
    {
        if (! $student->isEnrolled() || $student->admission_date === null) {
            return [];
        }

        $paidMonths = FeePayment::where('student_id', $student->id)
            ->pluck('fee_month')
            ->map(fn ($date) => Carbon::parse($date)->format('Y-m'))
            ->all();

        $pending = [];
        $cursor = $student->admission_date->copy()->startOfMonth();
        $end = now()->startOfMonth();

        while ($cursor->lessThanOrEqualTo($end)) {
            $key = $cursor->format('Y-m');
            if (! in_array($key, $paidMonths, true)) {
                $pending[] = [
                    'value' => $key,
                    'label' => $cursor->format('F Y'),
                ];
            }
            $cursor->addMonth();
        }

        return $pending;
    }

    /**
     * Build a filtered fee report query result.
     */
    public function getMonthlyReport(array $filters): Collection
    {
        return $this->reportQuery($filters)
            ->with(['student', 'campus', 'schoolClass', 'section', 'collectedBy'])
            ->orderByDesc('payment_date')
            ->orderByDesc('id')
            ->get();
    }

    /**
     * Shared query builder for report filters.
     */
    public function reportQuery(array $filters)
    {
        return FeePayment::query()
            ->when($filters['campus_id'] ?? null, fn ($q, $v) => $q->where('campus_id', $v))
            ->when($filters['department_id'] ?? null, fn ($q, $v) => $q->where('department_id', $v))
            ->when($filters['class_id'] ?? null, fn ($q, $v) => $q->where('class_id', $v))
            ->when($filters['status'] ?? null, fn ($q, $v) => $q->where('status', $v))
            ->when($filters['month_from'] ?? null, function ($q, $v) {
                $q->whereDate('fee_month', '>=', Carbon::createFromFormat('Y-m', $v)->startOfMonth());
            })
            ->when($filters['month_to'] ?? null, function ($q, $v) {
                $q->whereDate('fee_month', '<=', Carbon::createFromFormat('Y-m', $v)->endOfMonth());
            });
    }

    /**
     * Dashboard statistics for the school dashboard.
     *
     * @return array{this_month_collection: float, pending_dues: int, total_payments: int, non_resident_eating: int, resident_students: int}
     */
    public function getDashboardStats(?int $campusId = null): array
    {
        $monthStart = now()->startOfMonth();

        $payments = FeePayment::query()
            ->when($campusId, fn ($q) => $q->where('campus_id', $campusId));

        $thisMonthCollection = (clone $payments)
            ->whereDate('fee_month', $monthStart->toDateString())
            ->sum('amount_paid');

        $students = Student::query()
            ->where('enrollment_status', 'enrolled')
            ->when($campusId, fn ($q) => $q->where('campus_id', $campusId));

        $totalEnrolled = (clone $students)->count();

        $paidThisMonthStudentIds = FeePayment::query()
            ->when($campusId, fn ($q) => $q->where('campus_id', $campusId))
            ->whereDate('fee_month', $monthStart->toDateString())
            ->where('status', 'paid')
            ->pluck('student_id')
            ->unique();

        $pendingDues = max($totalEnrolled - $paidThisMonthStudentIds->count(), 0);

        $nonResidentEating = (clone $students)
            ->where('residential_status', 'non_resident')
            ->where('food_at_madrasa', true)
            ->count();

        $residentStudents = (clone $students)
            ->where('residential_status', 'resident')
            ->count();

        return [
            'this_month_collection' => (float) $thisMonthCollection,
            'pending_dues'          => $pendingDues,
            'total_payments'        => (clone $payments)->count(),
            'non_resident_eating'   => $nonResidentEating,
            'resident_students'     => $residentStudents,
        ];
    }
}
