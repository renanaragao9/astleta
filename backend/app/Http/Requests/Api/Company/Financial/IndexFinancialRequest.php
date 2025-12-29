<?php

namespace App\Http\Requests\Api\Company\Financial;

use Illuminate\Foundation\Http\FormRequest;

class IndexFinancialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ];
    }
}
