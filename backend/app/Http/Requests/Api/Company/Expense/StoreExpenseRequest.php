<?php

namespace App\Http\Requests\Api\Company\Expense;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class StoreExpenseRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:entrada,saida',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:1000',
            'expense_type_id' => 'required|integer|exists:expense_types,id',
            'due_date' => 'required|date',
            'is_paid' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome da despesa é obrigatório.',
            'name.string' => 'O nome deve ser uma string válida.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',
            'type.required' => 'O tipo da despesa é obrigatório.',
            'type.string' => 'O tipo deve ser uma string válida.',
            'type.in' => 'O tipo deve ser entrada ou saida.',
            'amount.required' => 'O valor é obrigatório.',
            'amount.numeric' => 'O valor deve ser um número.',
            'amount.min' => 'O valor deve ser maior que zero.',
            'description.string' => 'A descrição deve ser uma string válida.',
            'description.max' => 'A descrição não pode ter mais de 1000 caracteres.',
            'expense_type_id.required' => 'O tipo de despesa é obrigatório.',
            'expense_type_id.integer' => 'O tipo de despesa deve ser um número inteiro.',
            'expense_type_id.exists' => 'O tipo de despesa selecionado não existe.',
            'due_date.required' => 'A data de vencimento é obrigatória.',
            'due_date.date' => 'A data de vencimento deve ser uma data válida.',
            'is_paid.boolean' => 'O status de pagamento deve ser verdadeiro ou falso.',
        ];
    }
}
