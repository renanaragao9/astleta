<?php

namespace App\Http\Requests\Api\Athlete\Racha;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class JoinAthleteRachaRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'racha_number' => 'required|string|max:255|exists:bookings,booking_number',
        ];
    }

    public function messages(): array
    {
        return [
            'racha_number.required' => 'Número da reserva é obrigatório.',
            'racha_number.string' => 'Número da reserva deve ser uma string.',
            'racha_number.max' => 'Número da reserva não pode ter mais de 255 caracteres.',
            'racha_number.exists' => 'A reserva informada não existe.',
        ];
    }
}
