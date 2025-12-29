<?php

namespace App\Services\Api\Company\Global;

use App\Models\Company;
use Illuminate\Support\Facades\Auth;

class BaseService
{
    protected function getCompany(): Company
    {
        return Company::where('user_id', Auth::id())
            ->firstOrFail();
    }

    protected function getUserId(): int
    {
        return Auth::id();
    }
}
