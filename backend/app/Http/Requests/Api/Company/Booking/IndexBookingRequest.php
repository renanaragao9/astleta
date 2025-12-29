<?php

namespace App\Http\Requests\Api\Company\Booking;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class IndexBookingRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'search' => 'nullable|string|max:255',
            'booking_status' => 'nullable|string|in:pendente,confirmado,cancelado,concluido',
            'booking_date' => 'nullable|date',
            'field_id' => 'nullable|integer|exists:fields,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'payment_type' => 'nullable|string|in:online,presencial',
        ]);
    }

    public function messages(): array
    {
        return [
            'search.max' => 'Busca não pode ter mais de 255 caracteres.',
            'booking_status.in' => 'Status inválido. Use: pendente, confirmado, cancelado ou concluido.',
            'booking_date.date' => 'Data da reserva deve ser uma data válida.',
            'field_id.exists' => 'Campo selecionado não existe.',
            'start_date.date' => 'Data inicial deve ser uma data válida.',
            'end_date.date' => 'Data final deve ser uma data válida.',
            'end_date.after_or_equal' => 'Data final deve ser igual ou posterior à data inicial.',
            'payment_type.in' => 'Tipo de pagamento inválido. Use: online ou presencial.',
        ];
    }
}
