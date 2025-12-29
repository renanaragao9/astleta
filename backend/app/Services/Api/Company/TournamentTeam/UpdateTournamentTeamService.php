<?php

namespace App\Services\Api\Company\TournamentTeam;

use App\Models\TournamentTeam;
use App\Services\Api\Company\Global\BaseService;

class UpdateTournamentTeamService extends BaseService
{
    public function run(TournamentTeam $tournamentTeam, array $data): TournamentTeam
    {
        $company = $this->getCompany();

        $tournamentTeam->load('tournament');
        if ($tournamentTeam->tournament->company_id !== $company->id) {
            throw new \Exception('Acesso negado.');
        }

        $tournamentTeam->update($data);

        return $tournamentTeam->refresh()->load('tournament', 'team');
    }
}