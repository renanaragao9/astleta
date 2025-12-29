<?php

namespace App\Http\Requests\Api\Public\Marketing;

use Illuminate\Foundation\Http\FormRequest;

class IndexPublicMarketingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'age' => 'nullable|integer|min:0',
        ];
    }
}
