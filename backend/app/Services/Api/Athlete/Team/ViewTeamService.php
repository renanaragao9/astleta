<?php

namespace App\Services\Api\Athlete\Team;

use App\Models\Team;
use App\Services\Api\Athlete\Global\BaseService;
use Illuminate\Validation\ValidationException;

class ViewTeamService extends BaseService
{
    public function run(): Team
    {
        $userId = $this->getUserId();

        $team = Team::with([
            'sport:id,name',
            'teamType:id,name',
            'creator:id,name,phone,uuid',
        ])
            ->where(function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->orWhereHas('teamPlayers', function ($subQuery) use ($userId) {
                        $subQuery->where('user_id', $userId)
                            ->where('status', 'ativo');
                    });
            })
            ->first();

        if (!$team) {
            throw ValidationException::withMessages(['error' => 'Time não encontrado.']);
        }

        return $team;
    }
}
