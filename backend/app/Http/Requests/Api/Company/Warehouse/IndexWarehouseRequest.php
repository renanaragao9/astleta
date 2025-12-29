<?php

namespace App\Http\Requests\Api\Company\Warehouse;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class IndexWarehouseRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'search' => 'nullable|string|max:255',
        ]);
    }

    public function messages(): array
    {
        return [
            'search.max' => 'O termo de busca não pode ter mais de 255 caracteres.',
            'search.string' => 'O termo de busca deve ser uma string válida.',
        ];
    }
}