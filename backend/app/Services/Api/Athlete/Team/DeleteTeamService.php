<?php

namespace App\Services\Api\Athlete\Team;

use App\Models\Team;
use App\Services\Api\Athlete\Global\BaseService;
use Illuminate\Validation\ValidationException;

class DeleteTeamService extends BaseService
{
    public function run(Team $team): bool
    {
        $userId = $this->getUserId();

        if ($team->user_id !== $userId) {
            return false;
        }

        if ($team->teamPlayers()->where('user_id', '!=', $userId)->exists()) {
            throw ValidationException::withMessages(['error' => 'Não é possível excluir o time pois existem jogadores associados. Remova os jogadores antes de excluir o time.']);
        }

        $team->teamPlayers()->where('user_id', $userId)->delete();

        return $team->delete();
    }
}
