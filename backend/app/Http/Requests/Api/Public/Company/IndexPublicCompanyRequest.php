<?php

namespace App\Http\Requests\Api\Public\Company;

use Illuminate\Foundation\Http\FormRequest;

class IndexPublicCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }

    public function attributes(): array
    {
        return [];
    }
}
