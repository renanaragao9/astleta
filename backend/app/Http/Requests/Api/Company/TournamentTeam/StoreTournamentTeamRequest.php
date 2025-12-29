<?php

namespace App\Http\Requests\Api\Company\TournamentTeam;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class StoreTournamentTeamRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'tournament_id' => 'required|integer|exists:tournaments,id',
            'team_id' => 'required|integer|exists:teams,uuid',
        ];
    }

    public function messages(): array
    {
        return [
            'tournament_id.required' => 'O ID do torneio é obrigatório.',
            'tournament_id.integer' => 'O ID do torneio deve ser um número inteiro.',
            'tournament_id.exists' => 'O torneio selecionado não existe.',
            'team_id.required' => 'O ID público do time é obrigatório.',
            'team_id.integer' => 'O ID público do time deve ser um número inteiro.',
            'team_id.exists' => 'O time selecionado não existe.',
        ];
    }
}