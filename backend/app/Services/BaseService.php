<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

class BaseService
{
    protected function getUserId()
    {
        return Auth::id();
    }

    protected function getUser()
    {
        return Auth::user();
    }
}
