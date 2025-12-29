<?php

namespace App\Services\Api\Athlete\Profile;

use App\Models\Team;
use App\Models\User;
use App\Services\Api\Athlete\Global\BaseService;

class ViewProfileService extends BaseService
{
    public function run(): User
    {
        $userId = $this->getUserId();

        $user = User::with(relations: [
            'athleteProfile.sport:id,name',
            'athleteProfile.position:id,name',
            'athleteProfile.subposition:id,name',
            'athleteProfile.feature:id,name',
            'athleteProfile.subfeature:id,name',
            'profile:id,name',
            'skills:id,name',
        ])->find($userId);

        if ($user) {
            $user->team = Team::with([
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
        }

        return $user;
    }
}
