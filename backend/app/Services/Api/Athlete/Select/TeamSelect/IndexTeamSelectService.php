<?php

namespace App\Services\Api\Athlete\Select\TeamSelect;

use App\Models\Team;
use App\Services\Api\Athlete\Select\BaseService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class IndexTeamSelectService extends BaseService
{
    public function run(Request $request): Collection
    {
        $sportId = $request->query('sport_id');
        $userId = $this->getUserId();

        $query = Team::where('user_id', $userId);

        if ($sportId) {
            $query->where('sport_id', $sportId);
        }

        return $query->select('id', 'name', 'sport_id')
            ->get();
    }
}
