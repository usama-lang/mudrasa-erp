<?php

declare(strict_types=1);

namespace Modules\SchoolManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCampusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('school_campus.edit');
    }

    public function rules(): array
    {
        $campusId = $this->route('campus');

        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:20', 'unique:school_campuses,code,' . $campusId],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:1000'],
            'logo' => ['nullable', 'string', 'max:500'],
            'manager_id' => ['nullable', 'integer', 'exists:users,id'],
            'status' => ['required', 'in:active,inactive'],
        ];
    }
}
