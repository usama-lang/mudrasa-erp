<?php

declare(strict_types=1);

namespace Modules\MadrasaFunds\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\MadrasaFunds\Enums\FundType;

class UpdateFundRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('madrasa_funds.manage_funds');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'name_urdu' => ['nullable', 'string', 'max:255'],
            'type' => ['required', Rule::in(FundType::values())],
            'description' => ['nullable', 'string', 'max:1000'],
            'color' => ['nullable', 'string', 'max:20'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['is_active' => $this->boolean('is_active')]);
    }
}
