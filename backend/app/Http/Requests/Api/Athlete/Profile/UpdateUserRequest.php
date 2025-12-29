<?php

namespace App\Http\Requests\Api\Athlete\Profile;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class UpdateUserRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|min:2|max:255',
            'date' => 'required|date|after:1900-01-01|before:today',
            'phone' => 'required|string|unique:users,phone,'.auth()->id(),
            'gender' => 'nullable|string',
            'is_public' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome é obrigatório.',
            'name.string' => 'O nome deve ser uma string.',
            'name.min' => 'O nome deve ter pelo menos 2 caracteres.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',
            'date.required' => 'A data de nascimento é obrigatória.',
            'date.date' => 'A data de nascimento deve ser uma data válida.',
            'date.before' => 'A data de nascimento deve ser anterior a hoje.',
            'date.after' => 'A data de nascimento deve ser posterior a 01/01/1900.',
            'phone.required' => 'O telefone é obrigatório.',
            'phone.string' => 'O telefone deve ser uma string.',
            'phone.regex' => 'O telefone deve estar no formato XX XXXXX-XXXX ou XX XXXX-XXXX.',
            'phone.unique' => 'Este telefone já está em uso por outro usuário.',
            'gender.string' => 'O gênero deve ser uma string.',
            'gender.in' => 'O gênero deve ser masculino, feminino ou outro.',
            'is_public.boolean' => 'A visibilidade do perfil deve ser verdadeiro ou falso.',
        ];
    }
}
