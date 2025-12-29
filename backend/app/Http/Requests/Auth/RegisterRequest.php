<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $minAgeDate = now()->subYears(14)->format('Y-m-d');

        return [
            'name' => ['required', 'string', 'max:255'],
            'cpf' => ['nullable', 'string', 'max:14', 'unique:users,cpf'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:25', 'unique:users,phone'],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
            'date' => ['required', 'date', 'after:1900-01-01', "before_or_equal:{$minAgeDate}"],
            'gender' => ['required'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome é obrigatório.',
            'name.string' => 'O nome deve ser um texto.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',

            'cpf.required' => 'O CPF é obrigatório.',
            'cpf.string' => 'O CPF deve ser um texto.',
            'cpf.max' => 'O CPF não pode ter mais de 14 caracteres.',
            'cpf.unique' => 'Este CPF já está cadastrado.',

            'email.required' => 'O e-mail é obrigatório.',
            'email.string' => 'O e-mail deve ser um texto.',
            'email.email' => 'Informe um e-mail válido.',
            'email.max' => 'O e-mail não pode ter mais de 255 caracteres.',
            'email.unique' => 'Este e-mail já está cadastrado.',

            'phone.required' => 'O telefone é obrigatório.',
            'phone.string' => 'O telefone deve ser um texto.',
            'phone.max' => 'O telefone não pode ter mais de 25 caracteres.',
            'phone.unique' => 'Este telefone já está cadastrado.',

            'password.required' => 'A senha é obrigatória.',
            'password.confirmed' => 'A confirmação da senha não confere.',

            'date.required' => 'A data de nascimento é obrigatória.',
            'date.date' => 'A data de nascimento deve ser uma data válida.',
            'date.after' => 'A data de nascimento deve ser posterior a 01/01/1900.',
            'date.before_or_equal' => 'Você precisa ter pelo menos 14 anos para se cadastrar.',

            'gender.required' => 'Precisa informar o gênero.',
        ];
    }
}
