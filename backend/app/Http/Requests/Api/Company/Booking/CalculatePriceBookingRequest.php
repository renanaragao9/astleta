<?php

namespace App\Http\Requests\Api\Company\Booking;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class CalculatePriceBookingRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'field_id' => 'required|integer|exists:fields,id',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'include_extra_hour' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'field_id.required' => 'Campo é obrigatório.',
            'field_id.integer' => 'Campo deve ser um número inteiro.',
            'field_id.exists' => 'Campo selecionado não existe.',
            'start_time.required' => 'Horário de início é obrigatório.',
            'start_time.date_format' => 'Horário de início deve estar no formato HH:MM.',
            'end_time.required' => 'Horário de fim é obrigatório.',
            'end_time.date_format' => 'Horário de fim deve estar no formato HH:MM.',
            'end_time.after' => 'Horário de fim deve ser posterior ao horário de início.',
            'include_extra_hour.boolean' => 'Incluir hora extra deve ser verdadeiro ou falso.',
        ];
    }
}
