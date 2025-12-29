<?php

namespace App\Http\Requests\Api\Athlete\TeamPlayer;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class UpdateTeamPlayerRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'number' => 'nullable|integer|min:1|max:99',
            'role' => 'nullable|string|in:jogador,capitao,treinador',
            'status' => 'nullable|string|in:pendente,ativo,rescindido',
            'joined_at' => 'nullable|date|before_or_equal:today',
            'left_at' => 'nullable|date|after_or_equal:joined_at',
        ];
    }

    public function messages(): array
    {
        return [
            'number.integer' => 'O número deve ser um número inteiro.',
            'number.min' => 'O número deve ser no mínimo 1.',
            'number.max' => 'O número deve ser no máximo 99.',
            'role.string' => 'A função deve ser uma string válida.',
            'role.in' => 'A função deve ser: jogador, capitao ou treinador.',
            'status.string' => 'O status deve ser uma string válida.',
            'status.in' => 'O status deve ser: pendente, ativo ou rescindido.',
            'joined_at.date' => 'A data de entrada deve ser uma data válida.',
            'joined_at.before_or_equal' => 'A data de entrada não pode ser no futuro.',
            'left_at.date' => 'A data de saída deve ser uma data válida.',
            'left_at.after_or_equal' => 'A data de saída deve ser posterior ou igual à data de entrada.',
        ];
    }
}
