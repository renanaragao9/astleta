<?php

namespace App\Http\Requests\Api\Company\Supplier;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class StoreSupplierRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:suppliers,email',
            'phone' => 'nullable|string|max:255|unique:suppliers,phone',
            'address' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome do fornecedor é obrigatório.',
            'name.string' => 'O nome deve ser uma string válida.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',
            'email.string' => 'O email deve ser uma string válida.',
            'email.email' => 'O email deve ser um endereço válido.',
            'email.max' => 'O email não pode ter mais de 255 caracteres.',
            'email.unique' => 'Este email já está em uso.',
            'phone.string' => 'O telefone deve ser uma string válida.',
            'phone.max' => 'O telefone não pode ter mais de 255 caracteres.',
            'phone.unique' => 'Este telefone já está em uso.',
            'address.string' => 'O endereço deve ser uma string válida.',
            'address.max' => 'O endereço não pode ter mais de 255 caracteres.',
        ];
    }
}