<?php

namespace App\Http\Requests\Api\Athlete\TeamStatisticsBooking;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class StoreTeamStatisticsBookingRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'team_id' => 'required|integer|exists:teams,id',
            'statistic_id' => 'required|integer|exists:statistics_teams,id',
            'count' => 'required|integer|min:0|max:999',
        ];
    }

    public function messages(): array
    {
        return [
            'team_id.required' => 'ID do time é obrigatório.',
            'team_id.integer' => 'ID do time deve ser um número inteiro.',
            'team_id.exists' => 'Time não encontrado.',
            'statistic_id.required' => 'ID da estatística de time é obrigatório.',
            'statistic_id.integer' => 'ID da estatística de time deve ser um número inteiro.',
            'statistic_id.exists' => 'Estatística de time não encontrada.',
            'count.required' => 'Quantidade é obrigatória.',
            'count.integer' => 'Quantidade deve ser um número inteiro.',
            'count.min' => 'Quantidade não pode ser negativa.',
            'count.max' => 'Quantidade não pode ser maior que 999.',
        ];
    }
}
