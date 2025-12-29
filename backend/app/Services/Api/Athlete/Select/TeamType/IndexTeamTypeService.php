<?php

namespace App\Services\Api\Athlete\Select\TeamType;

use App\Models\TeamType;
use App\Services\Api\Athlete\Select\BaseService;
use Illuminate\Database\Eloquent\Collection;

class IndexTeamTypeService extends BaseService
{
    public function run(): Collection
    {
        return TeamType::query()
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
    }
}
