<?php

namespace App\Services\Api\Company\TournamentTeam;

use App\Models\TournamentTeam;
use App\Models\Team;
use App\Models\Tournament;
use App\Services\Api\Company\Global\BaseService;
use Illuminate\Validation\ValidationException;

class StoreTournamentTeamService extends BaseService
{
    public function run(array $data): TournamentTeam
    {
        $company = $this->getCompany();
        $publicTeamId = $data['team_id'];

        $currentParticipants = TournamentTeam::where('tournament_id', $data['tournament_id'])->count();

        $team = Team::where('uuid', $publicTeamId)
            ->first();

        $existing = TournamentTeam::where('tournament_id', $data['tournament_id'])
            ->where('team_id', $team->id)
            ->exists();

        $tournament = Tournament::where('id', $data['tournament_id'])
            ->where('company_id', $company->id)
            ->first();

        if (!$tournament) {
            throw ValidationException::withMessages(['error' => 'O torneio não pertence à companhia.']);
        }

        if ($currentParticipants >= $tournament->max_participants) {
            throw ValidationException::withMessages(['error' => 'O torneio atingiu o número máximo de participantes.']);
        }

        if ($existing) {
            throw new \Exception('O time já está inscrito neste torneio.');
        }

        $data['team_id'] = $team->id;

        $tournamentTeam = TournamentTeam::create($data);
        $tournamentTeam->load('tournament', 'team');

        return $tournamentTeam;
    }
}