<?php

namespace App\Http\Requests\Api\Company\Purchase;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;
use Illuminate\Validation\Rule;

class UpdatePurchaseRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'invoice_number' => [
                'required',
                'string',
                Rule::unique('purchases', 'invoice_number')->ignore($this->route('purchase')->id)
            ],
            'purchase_date' => ['required', 'date'],
            'total_amount' => ['required', 'numeric', 'min:0'],
            'supplier_id' => ['required', 'exists:suppliers,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'invoice_number.required' => 'O número da fatura é obrigatório.',
            'invoice_number.string' => 'O número da fatura deve ser um texto.',
            'invoice_number.unique' => 'Este número de fatura já existe.',
            'purchase_date.required' => 'A data da compra é obrigatória.',
            'purchase_date.date' => 'A data da compra deve ser uma data válida.',
            'total_amount.required' => 'O valor total é obrigatório.',
            'total_amount.numeric' => 'O valor total deve ser um número.',
            'total_amount.min' => 'O valor total deve ser maior ou igual a 0.',
            'supplier_id.required' => 'O fornecedor é obrigatório.',
            'supplier_id.exists' => 'O fornecedor selecionado não existe.',
        ];
    }
}