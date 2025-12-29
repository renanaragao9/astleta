<?php

namespace App\Http\Requests\Api\Company\Product;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class StoreProductRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0.01',
            'product_type_id' => 'required|integer|exists:product_types,id',
            'is_active' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome do produto é obrigatório.',
            'name.string' => 'O nome deve ser uma string válida.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',
            'description.string' => 'A descrição deve ser uma string válida.',
            'description.max' => 'A descrição não pode ter mais de 1000 caracteres.',
            'price.required' => 'O preço é obrigatório.',
            'price.numeric' => 'O preço deve ser um valor numérico.',
            'price.min' => 'O preço deve ser maior que zero.',
            'product_type_id.required' => 'O tipo do produto é obrigatório.',
            'product_type_id.integer' => 'O tipo do produto deve ser um número inteiro.',
            'product_type_id.exists' => 'O tipo do produto selecionado não existe.',
            'is_active.boolean' => 'O status ativo deve ser verdadeiro ou falso.',
        ];
    }
}
