<?php

declare(strict_types=1);

namespace Modules\SchoolManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('school_section.create');
    }

    public function rules(): array
    {
        return [
            'campus_id' => ['required', 'integer', 'exists:school_campuses,id'],
            'class_id' => ['required', 'integer', 'exists:school_classes,id'],
            'name' => ['required', 'string', 'max:10'],
            'capacity' => ['required', 'integer', 'min:1', 'max:500'],
            'status' => ['required', 'in:active,inactive'],
        ];
    }
}
