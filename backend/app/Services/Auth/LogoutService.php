<?php

namespace App\Services\Auth;

use App\Models\User;

class LogoutService
{
    public function logout(User $user): void
    {
        // Revoga todos os tokens do usuário
        $user->tokens()->delete();
    }

    public function logoutFromDevice(User $user, string $tokenId): bool
    {
        // Revoga apenas o token específico do dispositivo
        return $user->tokens()->where('id', $tokenId)->delete() > 0;
    }

    public function logoutFromAllDevices(User $user): void
    {
        // Revoga todos os tokens do usuário (mesmo que logout normal)
        $this->logout($user);
    }
}
