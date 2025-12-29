<?php

namespace App\Http\Requests\Api\Athlete\TeamBooking;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class StoreTeamBookingRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'home_team_uuid' => ['required', 'string', 'exists:teams,uuid'],
            'away_team_uuid' => ['required', 'string', 'exists:teams,uuid', 'different:home_team_uuid'],
            'sport_id' => ['required', 'integer', 'exists:sports,id'],
            'winner_id' => ['nullable', 'integer', 'exists:teams,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'home_team_uuid.required' => 'O ID Público do time da casa é obrigatório',
            'home_team_uuid.exists' => 'O time da casa com este ID Público não existe',
            'away_team_uuid.required' => 'O ID Público do time visitante é obrigatório',
            'away_team_uuid.exists' => 'O time visitante com este ID Público não existe',
            'away_team_uuid.different' => 'O time visitante deve ser diferente do time da casa',
            'sport_id.exists' => 'O esporte informado não existe',
            'winner_id.exists' => 'O time vencedor informado não existe',
            'sport_id.required' => 'O campo esporte é obrigatório',
        ];
    }
}
