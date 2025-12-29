<?php

namespace App\Services\Api\Public\Booking;

use Illuminate\Support\Facades\Auth;

class BaseService
{
    protected function getUserId(): int
    {
        $userId = Auth::id();
        if (! $userId) {
            throw new \Exception('Usuário não autenticado.');
        }

        return $userId;
    }
}
