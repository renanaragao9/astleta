<?php

namespace App\Http\Requests\Api\Athlete\Profile;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class UpdateProfileRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'dominant_side' => 'nullable|string|in:esquerdo,direito,ambos',
            'height' => 'nullable|numeric|min:0.5|max:3.0',
            'weight' => 'nullable|numeric|min:20|max:300',
            'bio' => 'nullable|string|max:1000',
            'sport_id' => 'nullable|integer|exists:sports,id',
            'position_id' => 'nullable|integer|exists:positions,id',
            'subposition_id' => 'nullable|integer|exists:positions,id',
            'feature_id' => 'nullable|integer|exists:features,id',
            'subfeature_id' => 'nullable|integer|exists:features,id',
            'skill_ids' => 'nullable|array|max:6',
            'skill_ids.*' => 'exists:skills,id',
        ];
    }

    public function messages(): array
    {
        return [
            'dominant_side.in' => 'O lado dominante deve ser esquerdo, direito ou ambos.',
            'height.numeric' => 'A altura deve ser um número válido.',
            'height.min' => 'A altura deve ser no mínimo 0.5 metros.',
            'height.max' => 'A altura deve ser no máximo 3.0 metros.',
            'weight.numeric' => 'O peso deve ser um número válido.',
            'weight.min' => 'O peso deve ser no mínimo 20 kg.',
            'weight.max' => 'O peso deve ser no máximo 300 kg.',
            'bio.string' => 'A biografia deve ser uma string válida.',
            'bio.max' => 'A biografia não pode ter mais de 1000 caracteres.',
            'sport_id.integer' => 'O ID do esporte deve ser um número inteiro.',
            'sport_id.exists' => 'O esporte selecionado não existe.',
            'position_id.integer' => 'O ID da posição deve ser um número inteiro.',
            'position_id.exists' => 'A posição selecionada não existe.',
            'subposition_id.integer' => 'O ID da subposição deve ser um número inteiro.',
            'subposition_id.exists' => 'A subposição selecionada não existe.',
            'feature_id.integer' => 'O ID da característica deve ser um número inteiro.',
            'feature_id.exists' => 'A característica selecionada não existe.',
            'subfeature_id.integer' => 'O ID da subcaracterística deve ser um número inteiro.',
            'subfeature_id.exists' => 'A subcaracterística selecionada não existe.',
            'skill_ids.array' => 'As habilidades devem ser um array.',
            'skill_ids.max' => 'Máximo 6 habilidades.',
            'skill_ids.*.exists' => 'Uma ou mais habilidades são inválidas.',
        ];
    }
}
