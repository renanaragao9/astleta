<?php

namespace App\Services\Api\Athlete\Team;

use App\Models\Team;
use App\Models\TeamPlayer;
use App\Services\Api\Athlete\Global\BaseService;
use Illuminate\Validation\ValidationException;

class UpdateTeamService extends BaseService
{
    public function run(Team $team, array $data): Team
    {
        $userId = $this->getUserId();

        $ownsOtherTeam = Team::where('user_id', $userId)->where('id', '!=', $team->id)->first() !== null;
        $isPlayerInOtherTeam = TeamPlayer::where('user_id', $userId)->where('status', 'ativo')->where('team_id', '!=', $team->id)->first() !== null;

        if ($ownsOtherTeam || $isPlayerInOtherTeam) {
            throw ValidationException::withMessages(['error' => 'Você já possui outro time ou participa de outro time ativo. Não é possível atualizar este time.']);
        }

        $team->update($data);

        return $team->load([
            'sport:id,name',
            'teamType:id,name',
            'creator:uuid,name,email',
        ]);
    }
}
