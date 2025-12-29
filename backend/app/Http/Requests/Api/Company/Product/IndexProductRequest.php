<?php

namespace App\Http\Requests\Api\Company\Product;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class IndexProductRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'search' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
            'product_type_id' => 'nullable|integer|exists:product_types,id',
        ]);
    }

    public function messages(): array
    {
        return [
            'search.max' => 'O termo de busca não pode ter mais de 255 caracteres.',
            'search.string' => 'O termo de busca deve ser uma string válida.',
            'is_active.boolean' => 'O status ativo deve ser verdadeiro ou falso.',
            'product_type_id.integer' => 'O tipo de produto deve ser um número inteiro.',
            'product_type_id.exists' => 'O tipo de produto selecionado não existe.',
        ];
    }
}
