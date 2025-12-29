<?php

namespace App\Http\Requests\Api\Athlete\Tab;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class IndexTabAthleteRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'search' => 'nullable|string|max:255',
            'status' => 'nullable|string|in:aberto,pago,cancelado',
            'customer_name' => 'nullable|string|max:255',
            'payment_form_id' => 'nullable|integer|exists:payment_forms,id',
            'start_created_date' => 'nullable|date|date_format:Y-m-d',
            'end_created_date' => 'nullable|date|date_format:Y-m-d|after_or_equal:start_created_date',
        ]);
    }

    public function messages(): array
    {
        return [
            'search.string' => 'O termo de busca deve ser uma string válida.',
            'search.max' => 'O termo de busca não pode ter mais de 255 caracteres.',
            'status.string' => 'O status deve ser uma string válida.',
            'status.in' => 'O status deve ser: aberta, fechada ou cancelada.',
            'customer_name.string' => 'O nome do cliente deve ser uma string válida.',
            'customer_name.max' => 'O nome do cliente não pode ter mais de 255 caracteres.',
            'payment_form_id.integer' => 'A forma de pagamento deve ser um número inteiro.',
            'payment_form_id.exists' => 'A forma de pagamento selecionada não existe.',
            'start_created_date.date' => 'A data de início deve ser uma data válida.',
            'start_created_date.date_format' => 'A data de início deve estar no formato YYYY-MM-DD.',
            'end_created_date.date' => 'A data de fim deve ser uma data válida.',
            'end_created_date.date_format' => 'A data de fim deve estar no formato YYYY-MM-DD.',
            'end_created_date.after_or_equal' => 'A data de fim deve ser igual ou posterior à data de início.',
        ];
    }
}
