<?php

namespace App\Services\Api\Athlete\Select\Skill;

use App\Models\Skill;
use App\Services\Api\Athlete\Select\BaseService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class IndexSkillService extends BaseService
{
    public function run(Request $request): Collection
    {
        $query = Skill::query()
            ->select('id', 'name', 'sport_id')
            ->orderBy('name');

        if ($request->has('sport_id') && $request->sport_id) {
            $query->where('sport_id', $request->sport_id);
        }

        return $query->get();
    }
}
