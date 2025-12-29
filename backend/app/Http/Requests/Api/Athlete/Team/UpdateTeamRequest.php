<?php

namespace App\Http\Requests\Api\Athlete\Team;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class UpdateTeamRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255|unique:teams,name,' . $this->route('team')->id,
            'nickname' => 'nullable|string|max:100',
            'stadium_name' => 'nullable|string|max:255',
            'primary_color' => 'nullable|string|max:7|regex:/^#[A-Fa-f0-9]{6}$/',
            'secondary_color' => 'nullable|string|max:7|regex:/^#[A-Fa-f0-9]{6}$/',
            'shield_path' => 'nullable|string|max:500',
            'website' => 'nullable|url|max:500',
            'founded_date' => 'nullable|date|before_or_equal:today',
            'description' => 'nullable|string|max:1000',
            'welcome_email' => 'nullable|string|max:1000',
            'max_members' => 'nullable|integer|min:1|max:50',
            'is_public' => 'nullable|boolean',
            'sport_id' => 'sometimes|required|integer|exists:sports,id',
            'team_type_id' => 'sometimes|required|integer|exists:team_types,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome do time é obrigatório.',
            'name.string' => 'O nome do time deve ser uma string válida.',
            'name.max' => 'O nome do time não pode ter mais de 255 caracteres.',
            'name.unique' => 'Já existe um time cadastrado com esse nome.',
            'nickname.string' => 'O apelido deve ser uma string válida.',
            'nickname.max' => 'O apelido não pode ter mais de 100 caracteres.',
            'stadium_name.string' => 'O nome do estádio deve ser uma string válida.',
            'stadium_name.max' => 'O nome do estádio não pode ter mais de 255 caracteres.',
            'primary_color.string' => 'A cor primária deve ser uma string válida.',
            'primary_color.max' => 'A cor primária não pode ter mais de 7 caracteres.',
            'primary_color.regex' => 'A cor primária deve estar no formato hexadecimal (#RRGGBB).',
            'secondary_color.string' => 'A cor secundária deve ser uma string válida.',
            'secondary_color.max' => 'A cor secundária não pode ter mais de 7 caracteres.',
            'secondary_color.regex' => 'A cor secundária deve estar no formato hexadecimal (#RRGGBB).',
            'shield_path.string' => 'O caminho do escudo deve ser uma string válida.',
            'shield_path.max' => 'O caminho do escudo não pode ter mais de 500 caracteres.',
            'website.url' => 'O website deve ser uma URL válida.',
            'website.max' => 'O website não pode ter mais de 500 caracteres.',
            'founded_date.date' => 'A data de fundação deve ser uma data válida.',
            'founded_date.before_or_equal' => 'A data de fundação não pode ser no futuro.',
            'description.string' => 'A descrição deve ser uma string válida.',
            'description.max' => 'A descrição não pode ter mais de 1000 caracteres.',
            'welcome_email.string' => 'A mensagem de boas vindas deve ser uma string válida.',
            'welcome_email.max' => 'A mensagem de boas vindas não pode ter mais de 1000 caracteres.',
            'max_members.integer' => 'O número máximo de membros deve ser um número inteiro.',
            'max_members.min' => 'O número máximo de membros deve ser no mínimo 1.',
            'max_members.max' => 'O número máximo de membros deve ser no máximo 50.',
            'is_public.boolean' => 'O campo público deve ser verdadeiro ou falso.',
            'sport_id.required' => 'O esporte é obrigatório.',
            'sport_id.integer' => 'O ID do esporte deve ser um número inteiro.',
            'sport_id.exists' => 'O esporte selecionado não existe.',
            'team_type_id.required' => 'O tipo de time é obrigatório.',
            'team_type_id.integer' => 'O ID do tipo de time deve ser um número inteiro.',
            'team_type_id.exists' => 'O tipo de time selecionado não existe.',
        ];
    }
}
