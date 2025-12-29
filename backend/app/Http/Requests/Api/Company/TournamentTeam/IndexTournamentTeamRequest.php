<?php

namespace App\Http\Requests\Api\Company\TournamentTeam;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class IndexTournamentTeamRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'search' => 'nullable|string|max:255',
            'tournament_id' => 'nullable|integer|exists:tournaments,id',
        ]);
    }

    public function messages(): array
    {
        return [
            'search.max' => 'O termo de busca não pode ter mais de 255 caracteres.',
            'search.string' => 'O termo de busca deve ser uma string válida.',
            'tournament_id.integer' => 'O ID do torneio deve ser um número inteiro.',
            'tournament_id.exists' => 'O torneio selecionado não existe.',
        ];
    }
}