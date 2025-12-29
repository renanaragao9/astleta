<?php

namespace App\Http\Requests\Api\Company\Warehouse;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class StoreWarehouseRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome do armazém é obrigatório.',
            'name.string' => 'O nome deve ser uma string válida.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',
            'location.string' => 'A localização deve ser uma string válida.',
            'location.max' => 'A localização não pode ter mais de 255 caracteres.',
        ];
    }
}