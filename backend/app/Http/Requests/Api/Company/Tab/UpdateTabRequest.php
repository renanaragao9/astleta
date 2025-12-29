<?php

namespace App\Http\Requests\Api\Company\Tab;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class UpdateTabRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'code' => 'sometimes|required|string|max:50',
            'customer_name' => 'sometimes|required|string|max:255',
            'status' => 'sometimes|string|in:aberto,pago,cancelado',
            'total_amount' => 'sometimes|numeric|min:0',
            'opened_at' => 'sometimes|date',
            'closed_at' => 'sometimes|date|after:opened_at',
            'payment_form_id' => 'sometimes|integer|exists:payment_forms,id',
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
