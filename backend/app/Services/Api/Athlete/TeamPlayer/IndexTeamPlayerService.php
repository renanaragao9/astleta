<?php

namespace App\Services\Api\Athlete\TeamPlayer;

use App\Models\Team;
use App\Models\TeamPlayer;
use App\Services\Api\Athlete\Global\BaseService;

class IndexTeamPlayerService extends BaseService
{
    public function run(int $teamId)
    {
        $userId = $this->getUserId();

        if (!$userId) {
            return null;
        }

        $team = Team::where('id', $teamId)
            ->where(function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->orWhere('is_public', true)
                    ->orWhereHas('teamPlayers', function ($subQuery) use ($userId) {
                        $subQuery->where('user_id', $userId)
                            ->where('status', 'ativo');
                    });
            })
            ->first();

        if (!$team) {
            return null;
        }

        return TeamPlayer::with([
            'user' => function ($query) {
                $query->select('id', 'name', 'phone', 'image_path')->with([
                    'athleteProfile' => function ($query) {
                        $query->with('position:id,name');
                    },
                ]);
            },
            'team:id,name',
        ])
            ->where('team_id', $teamId)
            ->orderBy('number')
            ->get();
    }
}
