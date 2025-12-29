<?php

namespace App\Http\Requests\Api\Company\Purchase;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class IndexPurchaseRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'search' => 'nullable|string|max:255',
            'supplier_id' => 'nullable|integer|exists:suppliers,id',
            'status' => 'nullable|string|in:concluido,cancelado',
            'start_purchase_date' => 'nullable|date',
            'end_purchase_date' => 'nullable|date|after_or_equal:start_purchase_date',
        ]);
    }

    public function messages(): array
    {
        return [
            'search.max' => 'O termo de busca não pode ter mais de 255 caracteres.',
            'search.string' => 'O termo de busca deve ser uma string válida.',
            'supplier_id.integer' => 'O fornecedor deve ser um número inteiro.',
            'supplier_id.exists' => 'O fornecedor selecionado não existe.',
            'status.in' => 'O status deve ser concluida ou cancelada.',
            'start_purchase_date.date' => 'A data inicial de compra deve ser uma data válida.',
            'end_purchase_date.date' => 'A data final de compra deve ser uma data válida.',
            'end_purchase_date.after_or_equal' => 'A data final deve ser igual ou posterior à data inicial.',
        ];
    }
}