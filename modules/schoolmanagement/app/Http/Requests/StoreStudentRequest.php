<?php

declare(strict_types=1);

namespace Modules\SchoolManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('school_student.create');
    }

    public function rules(): array
    {
        return [
            'student_name'       => ['required', 'string', 'max:255'],
            'campus_id'          => ['required', 'integer', 'exists:school_campuses,id'],
            'department_id'      => ['nullable', 'integer', 'exists:school_departments,id'],
            'class_id'           => ['nullable', 'integer', 'exists:school_classes,id'],
            'section_id'         => ['nullable', 'integer', 'exists:school_sections,id'],
            'admission_no'       => ['nullable', 'string', 'max:50', 'unique:school_students,admission_no'],
            'admission_date'     => ['nullable', 'date'],
            'father_name'        => ['nullable', 'string', 'max:255'],
            'b_form_no'          => ['nullable', 'string', 'max:50'],
            'father_cnic'        => ['nullable', 'string', 'max:20'],
            'date_of_birth'      => ['nullable', 'date'],
            'age'                => ['nullable', 'integer', 'min:1', 'max:100'],
            'education_level'    => ['nullable', 'string', 'max:255'],
            'father_profession'  => ['nullable', 'string', 'max:255'],
            'designation'        => ['nullable', 'string', 'max:255'],
            'monthly_fee'        => ['nullable', 'numeric', 'min:0'],
            'phone_no'           => ['nullable', 'string', 'max:30'],
            'whatsapp_no'        => ['nullable', 'string', 'max:30'],
            'current_address'    => ['nullable', 'string', 'max:2000'],
            'permanent_address'  => ['nullable', 'string', 'max:2000'],
            'residential_status'  => ['nullable', 'in:resident,non_resident'],
            'food_at_madrasa'     => ['nullable', 'boolean'],
            'food_charges'        => ['nullable', 'numeric', 'min:0'],
            'enrollment_status'   => ['nullable', 'in:enrolled,left_self,expelled,completed'],
            'leaving_date'        => ['required_unless:enrollment_status,enrolled', 'nullable', 'date'],
            'status_remarks'      => ['required_unless:enrollment_status,enrolled', 'nullable', 'string', 'max:2000'],
            'guardian1_name'      => ['nullable', 'string', 'max:255'],
            'guardian1_relation' => ['nullable', 'string', 'max:100'],
            'guardian1_phone'    => ['nullable', 'string', 'max:30'],
            'guardian2_name'     => ['nullable', 'string', 'max:255'],
            'guardian2_relation' => ['nullable', 'string', 'max:100'],
            'guardian2_phone'    => ['nullable', 'string', 'max:30'],
            'agreement_date'     => ['nullable', 'date'],
            'student_image'      => ['nullable', 'image', 'max:2048'],
            'document_file'      => ['nullable', 'file', 'max:5120'],
            'status'             => ['nullable', 'boolean'],
            'email'              => ['nullable', 'email', 'max:255', 'unique:users,email'],
            'password'           => ['nullable', 'string', 'min:8'],
        ];
    }
}
