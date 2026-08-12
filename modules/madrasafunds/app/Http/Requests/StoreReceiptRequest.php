<?php

declare(strict_types=1);

namespace Modules\MadrasaFunds\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReceiptRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('madrasa_funds.create');
    }

    public function rules(): array
    {
        return [
            'receipt_date' => ['required', 'date'],
            'fund_id' => ['required', 'integer', 'exists:madrasa_funds,id'],
            'department_id' => ['nullable', 'integer', 'exists:madrasa_departments,id'],
            'student_id' => ['nullable', 'integer', 'exists:madrasa_students,id'],
            'donor_name' => ['nullable', 'string', 'max:255'],
            'donor_phone' => ['nullable', 'string', 'max:50'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:999999999.99'],
            'description' => ['nullable', 'string', 'max:2000'],
            'mad' => ['nullable', 'string', 'max:255'],
            'given_to' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
