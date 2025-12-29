<?php

namespace App\Services\Api\Company\Tournament;

use App\Models\Tournament;
use App\Services\Api\Company\Global\BaseService;

class DeleteTournamentService extends BaseService
{
    public function run(Tournament $tournament): void
    {
        $this->getCompany();

        $tournament->delete();
    }
}