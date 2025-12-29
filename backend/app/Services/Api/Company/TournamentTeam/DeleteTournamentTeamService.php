<?php

namespace App\Services\Api\Company\TournamentTeam;

use App\Models\TournamentTeam;
use App\Services\Api\Company\Global\BaseService;

class DeleteTournamentTeamService extends BaseService
{
    public function run(TournamentTeam $tournamentTeam): void
    {
        $company = $this->getCompany();

        // Verificar se o torneio pertence à empresa
        $tournamentTeam->load('tournament');
        if ($tournamentTeam->tournament->company_id !== $company->id) {
            throw new \Exception('Acesso negado.');
        }

        $tournamentTeam->delete();
    }
}