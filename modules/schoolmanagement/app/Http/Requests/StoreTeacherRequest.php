<?php

declare(strict_types=1);

namespace Modules\SchoolManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTeacherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('school_teacher.create');
    }

    public function rules(): array
    {
        return [
            'campus_id' => ['required', 'integer', 'exists:school_campuses,id'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['nullable', 'string', 'min:8'],
            'employee_id' => ['required', 'string', 'max:50', 'unique:school_teachers,employee_id'],
            'designation' => ['nullable', 'string', 'max:255'],
            'qualification' => ['nullable', 'string', 'max:255'],
            'salary' => ['nullable', 'numeric', 'min:0'],
            'joining_date' => ['nullable', 'date'],
            'status' => ['required', 'in:active,inactive'],
        ];
    }
}
