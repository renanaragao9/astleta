<?php

namespace App\Http\Requests\Api\Athlete\TeamStatisticsBooking;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class UpdateTeamStatisticsBookingRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'count' => 'required|integer|min:0|max:999',
        ];
    }

    public function messages(): array
    {
        return [
            'count.required' => 'Quantidade é obrigatória.',
            'count.integer' => 'Quantidade deve ser um número inteiro.',
            'count.min' => 'Quantidade não pode ser negativa.',
            'count.max' => 'Quantidade não pode ser maior que 999.',
        ];
    }
}
