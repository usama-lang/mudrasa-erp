<?php

declare(strict_types=1);

namespace Modules\SchoolManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('school_fee.collect');
    }

    public function rules(): array
    {
        return [
            'student_id'     => ['required', 'exists:school_students,id'],
            'fee_month'      => ['required', 'date_format:Y-m'],
            'amount_paid'    => ['required', 'numeric', 'min:0'],
            'discount'       => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['required', 'in:cash,bank_transfer,cheque'],
            'payment_date'   => ['nullable', 'date'],
            'notes'          => ['nullable', 'string', 'max:500'],
        ];
    }
}
