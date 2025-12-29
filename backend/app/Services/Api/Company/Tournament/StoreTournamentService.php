<?php

namespace App\Services\Api\Company\Tournament;

use App\Models\Tournament;
use App\Services\Api\Company\Global\BaseService;

class StoreTournamentService extends BaseService
{
    public function run(array $data): Tournament
    {
        $company = $this->getCompany();

        $data['company_id'] = $company->id;

        return Tournament::create($data);
    }
}