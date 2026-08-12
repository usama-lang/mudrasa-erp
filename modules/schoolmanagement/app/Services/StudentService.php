<?php

declare(strict_types=1);

namespace Modules\SchoolManagement\Services;

use App\Services\BaseService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\SchoolManagement\Models\Student;

class StudentService extends BaseService
{
    protected function getModelClass(): string
    {
        return Student::class;
    }

    public function create(array $data): Student
    {
        return DB::transaction(function () use ($data) {
            $userId = null;

            if (! empty($data['email'])) {
                $user = \App\Models\User::create([
                    'first_name' => explode(' ', $data['student_name'])[0],
                    'last_name' => implode(' ', array_slice(explode(' ', $data['student_name']), 1)) ?: '',
                    'email' => $data['email'],
                    'password' => bcrypt($data['password'] ?? str()->random(12)),
                    'email_verified_at' => now(),
                ]);
                $user->assignRole('student');
                $userId = $user->id;
            }

            $data['admission_no'] = $data['admission_no'] ?? $this->generateAdmissionNo();
            $data['user_id'] = $userId;
            $data['status'] = isset($data['status']) ? (bool) $data['status'] : true;
            $data['monthly_fee'] = $data['monthly_fee'] ?? 0;
            $data['food_at_madrasa'] = isset($data['food_at_madrasa']) ? (bool) $data['food_at_madrasa'] : false;
            $data['enrollment_status'] = $data['enrollment_status'] ?? 'enrolled';

            $studentData = $this->mapStudentFields($data);
            $studentData = $this->handleFileUploads($studentData, $data);

            return Student::create($studentData);
        });
    }

    public function update(int $studentId, array $data): Student
    {
        return DB::transaction(function () use ($studentId, $data) {
            /** @var Student $student */
            $student = Student::findOrFail($studentId);

            if (! empty($data['email']) && $student->user_id) {
                $student->user?->update(['email' => $data['email']]);
            }

            $studentData = $this->mapStudentFields($data);
            $studentData['status'] = isset($data['status']) ? (bool) $data['status'] : $student->status;
            $studentData['food_at_madrasa'] = isset($data['food_at_madrasa']) ? (bool) $data['food_at_madrasa'] : false;
            $studentData = $this->handleFileUploads($studentData, $data, $student);

            $student->update($studentData);

            return $student->fresh();
        });
    }

    public function updateParaQuantity(int $studentId, float $paraQuantity): Student
    {
        /** @var Student $student */
        $student = Student::findOrFail($studentId);
        $student->update(['para_quantity' => $paraQuantity]);

        return $student->fresh();
    }

    private function mapStudentFields(array $data): array
    {
        $fields = [
            'campus_id', 'department_id', 'class_id', 'section_id', 'user_id',
            'admission_no', 'admission_date', 'student_name', 'father_name',
            'b_form_no', 'father_cnic', 'current_address', 'permanent_address',
            'date_of_birth', 'age', 'education_level', 'father_profession',
            'designation', 'monthly_fee', 'phone_no', 'whatsapp_no',
            'residential_status', 'food_at_madrasa', 'food_charges',
            'guardian1_name', 'guardian1_relation', 'guardian1_phone',
            'guardian2_name', 'guardian2_relation', 'guardian2_phone',
            'agreement_date', 'status', 'enrollment_status', 'leaving_date', 'status_remarks',
        ];

        $mapped = [];
        foreach ($fields as $field) {
            if (array_key_exists($field, $data)) {
                $mapped[$field] = $data[$field] === '' ? null : $data[$field];
            }
        }

        return $mapped;
    }

    private function handleFileUploads(array $studentData, array $data, ?Student $existing = null): array
    {
        if (! empty($data['student_image']) && $data['student_image'] instanceof UploadedFile) {
            if ($existing?->student_image) {
                Storage::disk('public')->delete($existing->student_image);
            }
            $studentData['student_image'] = $data['student_image']->store('school/students/photos', 'public');
        }

        if (! empty($data['document_file']) && $data['document_file'] instanceof UploadedFile) {
            if ($existing?->document_file) {
                Storage::disk('public')->delete($existing->document_file);
            }
            $studentData['document_file'] = $data['document_file']->store('school/students/documents', 'public');
        }

        return $studentData;
    }

    public function generateAdmissionNo(): string
    {
        $year = date('Y');
        $last = Student::withTrashed()->whereYear('created_at', $year)->orderByDesc('id')->first();
        $seq = $last ? ($last->id + 1) : 1;

        return 'ADM-' . $year . '-' . str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }
}
