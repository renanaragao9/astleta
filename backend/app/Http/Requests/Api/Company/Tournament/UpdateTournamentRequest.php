<?php

namespace App\Http\Requests\Api\Company\Tournament;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class UpdateTournamentRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'status' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'awards' => 'nullable|string|max:1000',
            'welcome_email' => 'nullable|string|max:1000',
            'start_date' => 'sometimes|required|date',
            'end_date' => 'sometimes|required|date|after:start_date',
            'max_participants' => 'sometimes|required|integer|min:1',
            'is_public' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome do torneio é obrigatório.',
            'name.string' => 'O nome deve ser uma string válida.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',
            'status.required' => 'O status é obrigatório.',
            'status.string' => 'O status deve ser uma string válida.',
            'status.max' => 'O status não pode ter mais de 255 caracteres.',
            'description.string' => 'A descrição deve ser uma string válida.',
            'description.max' => 'A descrição não pode ter mais de 1000 caracteres.',
            'awards.string' => 'Os prêmios devem ser uma string válida.',
            'awards.max' => 'Os prêmios não podem ter mais de 1000 caracteres.',
            'welcome_email.string' => 'O email de boas-vindas deve ser uma string válida.',
            'welcome_email.max' => 'O email de boas-vindas não pode ter mais de 1000 caracteres.',
            'start_date.required' => 'A data de início é obrigatória.',
            'start_date.date' => 'A data de início deve ser uma data válida.',
            'end_date.required' => 'A data de fim é obrigatória.',
            'end_date.date' => 'A data de fim deve ser uma data válida.',
            'end_date.after' => 'A data de fim deve ser após a data de início.',
            'max_participants.required' => 'O número máximo de participantes é obrigatório.',
            'max_participants.integer' => 'O número máximo de participantes deve ser um número inteiro.',
            'max_participants.min' => 'O número máximo de participantes deve ser pelo menos 1.',
            'is_public.boolean' => 'O campo público deve ser verdadeiro ou falso.',
        ];
    }
}