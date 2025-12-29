<?php

namespace App\Http\Requests\Api\Athlete\BookingStatistic;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class StoreBookingStatisticRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'booking_participant_id' => 'required|integer|exists:booking_participants,id',
            'statistic_id' => 'required|integer|exists:statistics,id',
            'count' => 'required|integer|min:0|max:999',
        ];
    }

    public function messages(): array
    {
        return [
            'booking_participant_id.required' => 'ID do participante é obrigatório.',
            'booking_participant_id.integer' => 'ID do participante deve ser um número inteiro.',
            'booking_participant_id.exists' => 'Participante não encontrado.',
            'statistic_id.required' => 'ID da estatística é obrigatório.',
            'statistic_id.integer' => 'ID da estatística deve ser um número inteiro.',
            'statistic_id.exists' => 'Estatística não encontrada.',
            'count.required' => 'Quantidade é obrigatória.',
            'count.integer' => 'Quantidade deve ser um número inteiro.',
            'count.min' => 'Quantidade não pode ser negativa.',
            'count.max' => 'Quantidade não pode ser maior que 999.',
        ];
    }
}
