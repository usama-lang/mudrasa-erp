<?php

declare(strict_types=1);

namespace Modules\MadrasaFunds\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CancelReceiptRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('madrasa_funds.edit');
    }

    public function rules(): array
    {
        return [
            'cancelled_reason' => ['required', 'string', 'max:255'],
        ];
    }
}
