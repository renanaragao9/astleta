<?php

namespace App\Services\Api\Global\PreCompaniesRegistration;

use App\Models\PreCompaniesRegistration;
use App\Services\BaseService;

class StorePreCompaniesRegistrationService extends BaseService
{
    public function run(array $data): PreCompaniesRegistration
    {
        return PreCompaniesRegistration::create($data);
    }
}
