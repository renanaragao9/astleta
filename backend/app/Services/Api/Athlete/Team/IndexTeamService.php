<?php

namespace App\Services\Api\Athlete\Team;

use App\Models\Team;
use App\Services\Api\Athlete\Global\BaseService;

class IndexTeamService extends BaseService
{
    public function run()
    {
        $userId = $this->getUserId();

        return Team::with([
            'sport:id,name',
            'teamType:id,name',
            'creator:uuid,name,email',
        ])
            ->where('user_id', $userId)
            ->orderBy('name')
            ->get();
    }
}
