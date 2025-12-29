<?php

namespace App\Services\Api\Athlete\Global;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class BaseService
{
    protected function getUserId(): int|string
    {
        return Auth::id();
    }

    protected function getUser(): User
    {
        return Auth::user();
    }
}
