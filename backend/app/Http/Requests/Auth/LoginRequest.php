<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use App\Services\Auth\EmailVerificationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Validator;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email',
                'exists:users,email',
            ],
            'password' => ['required'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Credenciais inválidas.',
            'email.email' => 'Credenciais inválidas.',
            'email.exists' => 'Credenciais inválidas.',
            'password.required' => 'Credenciais inválidas.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $email = $this->input('email');
            $password = $this->input('password');

            $user = User::where('email', $email)->first();

            if (! $user) {
                return;
            }

            if (! Hash::check($password, $user->password)) {
                $validator->errors()->add('password', 'Credenciais inválidas.');

                return;
            }

            if ($user->email_verified_at === null) {
                app(EmailVerificationService::class)->resendVerificationCode(['email' => $email]);
                $validator->errors()->add('email', 'É necessário verificar o e-mail antes de fazer login. Um código de verificação foi enviado para seu email.');

                return;
            }

            if (! $user->is_active) {
                $validator->errors()->add('email', 'Sua conta está inativa. Entre em contato com o suporte.');
            }
        });
    }
}
