<?php

declare(strict_types=1);

namespace Modules\SchoolManagement\Services;

use App\Services\BaseService;
use Illuminate\Support\Facades\DB;
use Modules\SchoolManagement\Models\Teacher;

class TeacherService extends BaseService
{
    protected function getModelClass(): string
    {
        return Teacher::class;
    }

    public function createWithUser(array $data): Teacher
    {
        return DB::transaction(function () use ($data) {
            $userData = [
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'] ?? '',
                'email' => $data['email'],
                'password' => bcrypt($data['password'] ?? str()->random(12)),
                'email_verified_at' => now(),
            ];

            $user = \App\Models\User::create($userData);
            $user->assignRole('teacher');

            $teacher = Teacher::create([
                'campus_id' => $data['campus_id'],
                'user_id' => $user->id,
                'employee_id' => $data['employee_id'],
                'designation' => $data['designation'] ?? null,
                'qualification' => $data['qualification'] ?? null,
                'salary' => $data['salary'] ?? 0,
                'joining_date' => $data['joining_date'] ?? null,
                'status' => $data['status'] ?? 'active',
            ]);

            return $teacher;
        });
    }

    public function updateWithUser(int $teacherId, array $data): ?Teacher
    {
        return DB::transaction(function () use ($teacherId, $data) {
            $teacher = Teacher::with('user')->findOrFail($teacherId);

            $userData = array_filter([
                'first_name' => $data['first_name'] ?? null,
                'last_name' => $data['last_name'] ?? null,
                'email' => $data['email'] ?? null,
            ]);

            if (!empty($userData)) {
                $teacher->user->update($userData);
            }

            $teacher->update([
                'campus_id' => $data['campus_id'] ?? $teacher->campus_id,
                'employee_id' => $data['employee_id'] ?? $teacher->employee_id,
                'designation' => $data['designation'] ?? $teacher->designation,
                'qualification' => $data['qualification'] ?? $teacher->qualification,
                'salary' => $data['salary'] ?? $teacher->salary,
                'joining_date' => $data['joining_date'] ?? $teacher->joining_date,
                'status' => $data['status'] ?? $teacher->status,
            ]);

            return $teacher->fresh();
        });
    }

    public function generateEmployeeId(): string
    {
        $last = Teacher::withTrashed()->orderByDesc('id')->first();
        $nextId = $last ? ($last->id + 1) : 1;

        return 'EMP-' . str_pad((string) $nextId, 5, '0', STR_PAD_LEFT);
    }
}
