<?php

namespace App\Http\Requests\Api\Company\Tab;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class StoreTabRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'code' => 'required|string|max:50',
            'customer_name' => 'required|string|max:255',
            'status' => 'nullable|string|in:aberto,pago,cancelado',
            'total_amount' => 'nullable|numeric|min:0',
            'opened_at' => 'nullable|date',
            'closed_at' => 'nullable|date|after:opened_at',
            'payment_form_id' => 'nullable|integer|exists:payment_forms,id',
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'O código da comanda é obrigatório.',
            'code.string' => 'O código deve ser uma string válida.',
            'code.max' => 'O código não pode ter mais de 50 caracteres.',
            'customer_name.required' => 'O nome do cliente é obrigatório.',
            'customer_name.string' => 'O nome do cliente deve ser uma string válida.',
            'customer_name.max' => 'O nome do cliente não pode ter mais de 255 caracteres.',
            'status.string' => 'O status deve ser uma string válida.',
            'status.in' => 'O status deve ser: aberto, pago ou cancelado.',
            'total_amount.numeric' => 'O valor total deve ser numérico.',
            'total_amount.min' => 'O valor total deve ser maior ou igual a zero.',
            'opened_at.date' => 'A data de abertura deve ser uma data válida.',
            'closed_at.date' => 'A data de fechamento deve ser uma data válida.',
            'closed_at.after' => 'A data de fechamento deve ser posterior à data de abertura.',
            'payment_form_id.integer' => 'A forma de pagamento deve ser um número inteiro.',
            'payment_form_id.exists' => 'A forma de pagamento selecionada não existe.',
        ];
    }
}
