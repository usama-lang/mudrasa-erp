<?php

declare(strict_types=1);

namespace Modules\SchoolManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSchoolClassRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('school_class.edit');
    }

    public function rules(): array
    {
        return [
            'campus_id' => ['required', 'integer', 'exists:school_campuses,id'],
            'department_id' => ['required', 'integer', 'exists:school_departments,id'],
            'name' => ['required', 'string', 'max:255'],
            'numeric_name' => ['nullable', 'integer', 'min:1', 'max:20'],
            'status' => ['required', 'in:active,inactive'],
        ];
    }
}
