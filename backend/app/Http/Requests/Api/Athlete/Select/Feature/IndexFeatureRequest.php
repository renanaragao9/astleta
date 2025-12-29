<?php

namespace App\Http\Requests\Api\Athlete\Select\Feature;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class IndexFeatureRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'position_id' => 'nullable|integer|exists:positions,id',
        ];
    }

    public function messages(): array
    {
        return [
            'position_id.integer' => 'O ID da posição deve ser um número inteiro.',
            'position_id.exists' => 'A posição selecionada não existe.',
        ];
    }
}
