<?php

namespace App\Http\Requests\Api\Company\Stock;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class IndexStockRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'search' => 'nullable|string|max:255',
            'is_available_use' => 'nullable|boolean',
            'is_sale' => 'nullable|boolean',
            'product_id' => 'nullable|integer|exists:products,id',
            'warehouse_id' => 'nullable|integer|exists:warehouses,id',
        ]);
    }

    public function messages(): array
    {
        return [
            'search.max' => 'O termo de busca não pode ter mais de 255 caracteres.',
            'search.string' => 'O termo de busca deve ser uma string válida.',
            'is_available_use.boolean' => 'O status de uso disponível deve ser verdadeiro ou falso.',
            'is_sale.boolean' => 'O status de venda deve ser verdadeiro ou falso.',
            'product_id.integer' => 'O produto deve ser um número inteiro.',
            'product_id.exists' => 'O produto selecionado não existe.',
            'warehouse_id.integer' => 'O armazém deve ser um número inteiro.',
            'warehouse_id.exists' => 'O armazém selecionado não existe.',
        ];
    }
}