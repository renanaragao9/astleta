<?php

namespace App\Http\Requests\Api\Company\Expense;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class IndexExpenseRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'search' => 'nullable|string|max:255',
            'is_paid' => 'nullable|in:true,false',
            'type' => 'nullable|string|in:entrada,saida',
            'expense_type_id' => 'nullable|integer|exists:expense_types,id',
            'start_due_date' => 'nullable|date',
            'end_due_date' => 'nullable|date|after_or_equal:start_due_date',
        ]);
    }

    public function messages(): array
    {
        return [
            'search.max' => 'O termo de busca não pode ter mais de 255 caracteres.',
            'search.string' => 'O termo de busca deve ser uma string válida.',
            'is_paid.in' => 'O status de pagamento deve ser verdadeiro ou falso.',
            'type.in' => 'O tipo deve ser entrada ou saida.',
            'expense_type_id.integer' => 'O tipo de despesa deve ser um número inteiro.',
            'expense_type_id.exists' => 'O tipo de despesa selecionado não existe.',
            'start_due_date.date' => 'A data inicial de vencimento deve ser uma data válida.',
            'end_due_date.date' => 'A data final de vencimento deve ser uma data válida.',
            'end_due_date.after_or_equal' => 'A data final deve ser igual ou posterior à data inicial.',
        ];
    }
}
