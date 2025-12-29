<?php

namespace App\Http\Requests\Api\Company\Stock;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class StoreStockRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'is_available_use' => 'boolean',
            'is_sale' => 'boolean',
            'history' => 'nullable|json',
            'product_id' => 'required|integer|exists:products,id',
            'warehouse_id' => 'nullable|integer|exists:warehouses,id',
        ];
    }

    public function messages(): array
    {
        return [
            'is_available_use.boolean' => 'O status de uso disponível deve ser verdadeiro ou falso.',
            'is_sale.boolean' => 'O status de venda deve ser verdadeiro ou falso.',
            'history.json' => 'O histórico deve ser um JSON válido.',
            'product_id.required' => 'O produto é obrigatório.',
            'product_id.integer' => 'O produto deve ser um número inteiro.',
            'product_id.exists' => 'O produto selecionado não existe.',
            'warehouse_id.integer' => 'O armazém deve ser um número inteiro.',
            'warehouse_id.exists' => 'O armazém selecionado não existe.',
        ];
    }
}