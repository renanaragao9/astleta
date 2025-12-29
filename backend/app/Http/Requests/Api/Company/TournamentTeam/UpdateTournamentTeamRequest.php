<?php

namespace App\Http\Requests\Api\Company\TournamentTeam;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class UpdateTournamentTeamRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'points' => 'sometimes|integer|min:0',
            'position' => 'nullable|integer|min:1',
            'wins' => 'sometimes|integer|min:0',
            'draws' => 'sometimes|integer|min:0',
            'losses' => 'sometimes|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'points.integer' => 'Os pontos devem ser um número inteiro.',
            'points.min' => 'Os pontos devem ser maiores ou iguais a 0.',
            'position.integer' => 'A posição deve ser um número inteiro.',
            'position.min' => 'A posição deve ser maior ou igual a 1.',
            'wins.integer' => 'As vitórias devem ser um número inteiro.',
            'wins.min' => 'As vitórias devem ser maiores ou iguais a 0.',
            'draws.integer' => 'Os empates devem ser um número inteiro.',
            'draws.min' => 'Os empates devem ser maiores ou iguais a 0.',
            'losses.integer' => 'As derrotas devem ser um número inteiro.',
            'losses.min' => 'As derrotas devem ser maiores ou iguais a 0.',
        ];
    }
}