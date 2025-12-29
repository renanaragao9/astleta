<?php

namespace App\Http\Requests\Api\Global\AthleteProfile;

use App\Http\Requests\ApiFormRequest;

class IndexPublicAthleteProfileRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'max:255'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.string' => 'O nome deve ser um texto válido.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',
            'per_page.integer' => 'O número de itens por página deve ser um número inteiro.',
            'per_page.min' => 'O número mínimo de itens por página é 1.',
            'per_page.max' => 'O número máximo de itens por página é 100.',
            'page.integer' => 'A página deve ser um número inteiro.',
            'page.min' => 'A página deve ser pelo menos 1.',
        ];
    }
}
