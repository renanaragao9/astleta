<?php

namespace App\Services\Api\Company\Financial;

use App\Models\Company;
use Illuminate\Support\Facades\Auth;

class BaseService
{
    protected function getCompany()
    {
        $userId = Auth::id();

        return Company::where('user_id', $userId)->first();
    }
}
