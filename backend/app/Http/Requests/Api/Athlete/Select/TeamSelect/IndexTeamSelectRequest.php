<?php

namespace App\Http\Requests\Api\Athlete\Select\TeamSelect;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class IndexTeamSelectRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'sport_id' => 'nullable|integer|exists:sports,id',
        ];
    }

    public function messages(): array
    {
        return [
            'sport_id.integer' => 'O ID do esporte deve ser um número inteiro.',
            'sport_id.exists' => 'O esporte selecionado não existe.',
        ];
    }
}
