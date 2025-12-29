<?php

namespace App\Services\Auth;

use App\Models\User;

class LoginService
{
    public function run(array $data): array
    {
        try {
            $user = User::where('email', $data['email'])->first();

            $user->update([
                'qtd_login' => $user->qtd_login + 1,
                'last_login' => now(),
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return [
                'status' => 'success',
                'message' => 'Login realizado com sucesso.',
                'data' => [
                    'user' => $user->fresh(['profile']),
                    'token' => $token,
                ],
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Erro interno do servidor.',
                'data' => [],
            ];
        }
    }
}
