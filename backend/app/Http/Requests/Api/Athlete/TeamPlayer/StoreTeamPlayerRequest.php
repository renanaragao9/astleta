<?php

namespace App\Http\Requests\Api\Athlete\TeamPlayer;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class StoreTeamPlayerRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'user_phone' => 'required_without:public_id|string|regex:/^\d{10,11}$/|exists:users,phone',
            'public_id' => 'required_without:user_phone|string|exists:users,uuid',
            'number' => 'nullable|integer|min:1|max:99',
            'role' => 'nullable|string|in:jogador,capitao,treinador',
            'status' => 'nullable|string|in:pendente,ativo,rescindido',
            'joined_at' => 'nullable|date|before_or_equal:today',
        ];
    }

    public function messages(): array
    {
        return [
            'user_phone.required_without' => 'O telefone do usuário é obrigatório quando o ID público não for informado.',
            'user_phone.string' => 'O telefone do usuário deve ser uma string válida.',
            'user_phone.regex' => 'O telefone do usuário deve conter apenas dígitos (10 ou 11 números).',
            'user_phone.exists' => 'Usuário não encontrado com este telefone.',
            'public_id.required_without' => 'O ID público é obrigatório quando o telefone do usuário não for informado.',
            'public_id.string' => 'O ID público deve ser uma string válida.',
            'public_id.exists' => 'Usuário não encontrado com este ID público.',
            'number.integer' => 'O número deve ser um número inteiro.',
            'number.min' => 'O número deve ser no mínimo 1.',
            'number.max' => 'O número deve ser no máximo 99.',
            'role.string' => 'A função deve ser uma string válida.',
            'role.in' => 'A função deve ser: jogador, capitao ou treinador.',
            'status.string' => 'O status deve ser uma string válida.',
            'status.in' => 'O status deve ser: pendente, ativo ou rescindido.',
            'joined_at.date' => 'A data de entrada deve ser uma data válida.',
            'joined_at.before_or_equal' => 'A data de entrada não pode ser no futuro.',
        ];
    }
}
