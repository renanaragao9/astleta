<?php

namespace App\Services\Api\Company\Tournament;

use App\Models\Tournament;
use App\Services\Api\Company\Global\BaseService;

class UpdateTournamentService extends BaseService
{
    public function run(Tournament $tournament, array $data): Tournament
    {
        $company = $this->getCompany();

        $data['company_id'] = $company->id;

        $tournament->update($data);

        return $tournament->refresh();
    }
}