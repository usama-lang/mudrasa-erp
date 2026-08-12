<?php

declare(strict_types=1);

namespace Modules\SchoolManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateParaQuantityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('school_student.edit');
    }

    public function rules(): array
    {
        return [
            'para_quantity' => ['required', 'numeric', 'min:0', 'max:30'],
        ];
    }
}
