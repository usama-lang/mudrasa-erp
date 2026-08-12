<?php

declare(strict_types=1);

namespace Modules\SchoolManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('school_department.create');
    }

    public function rules(): array
    {
        return [
            'campus_id' => ['required', 'integer', 'exists:school_campuses,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'in:active,inactive'],
        ];
    }
}
