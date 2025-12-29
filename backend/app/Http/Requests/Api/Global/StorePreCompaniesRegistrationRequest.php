<?php

namespace App\Http\Requests\Api\Global;

use App\Http\Requests\ApiFormRequest;

class StorePreCompaniesRegistrationRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:pre_companies_registrations,email'],
            'phone' => ['required', 'string', 'unique:pre_companies_registrations,phone'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome é obrigatório.',
            'name.string' => 'O nome deve ser um texto válido.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',

            'email.required' => 'O email é obrigatório.',
            'email.email' => 'O email deve ter um formato válido.',
            'email.unique' => 'Este email já está cadastrado.',

            'phone.required' => 'O telefone é obrigatório.',
            'phone.string' => 'O telefone deve ser um texto válido.',
            'phone.unique' => 'Este telefone já está cadastrado.',

            'description.string' => 'A descrição deve ser um texto válido.',
            'description.max' => 'A descrição não pode ter mais de 1000 caracteres.',
        ];
    }
}
