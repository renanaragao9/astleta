<?php

namespace App\Services\Api\Athlete\Team;

use App\Models\Team;
use App\Models\TeamPlayer;
use App\Services\Api\Athlete\Global\BaseService;
use Illuminate\Validation\ValidationException;

class StoreTeamService extends BaseService
{
    public function run(array $data): Team
    {
        $userId = $this->getUserId();

        $ownsTeam = Team::where('user_id', $userId)->first() !== null;
        $isPlayerInTeam = TeamPlayer::where('user_id', $userId)->where('status', 'ativo')->first() !== null;

        if ($ownsTeam || $isPlayerInTeam) {
            throw ValidationException::withMessages(['error' => 'Você já possui um time ou participa de outro time ativo. Não é possível criar um novo time.']);
        }

        $data['uuid'] = now()->format('Ymd').rand(100, 999);
        $data['user_id'] = $userId;

        $data['is_public'] = $data['is_public'] ?? false;
        $data['max_members'] = $data['max_members'] ?? 22;

        $team = Team::create($data);

        TeamPlayer::create([
            'team_id' => $team->id,
            'user_id' => $userId,
            'role' => 'dono',
            'status' => 'ativo',
            'joined_at' => now(),
        ]);

        return $team->load([
            'sport:id,name',
            'teamType:id,name',
            'creator:uuid,name,email',
        ]);
    }
}
