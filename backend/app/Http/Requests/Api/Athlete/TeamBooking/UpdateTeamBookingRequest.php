<?php

namespace App\Http\Requests\Api\Athlete\TeamBooking;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class UpdateTeamBookingRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'goals_home' => ['required', 'integer', 'min:0'],
            'goals_away' => ['required', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'goals_home.required' => 'Os gols do time da casa são obrigatórios',
            'goals_home.integer' => 'Os gols do time da casa devem ser um número inteiro',
            'goals_home.min' => 'Os gols do time da casa não podem ser negativos',
            'goals_away.required' => 'Os gols do time visitante são obrigatórios',
            'goals_away.integer' => 'Os gols do time visitante devem ser um número inteiro',
            'goals_away.min' => 'Os gols do time visitante não podem ser negativos',
        ];
    }
}
