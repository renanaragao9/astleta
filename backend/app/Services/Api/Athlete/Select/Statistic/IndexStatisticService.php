<?php

namespace App\Services\Api\Athlete\Select\Statistic;

use App\Models\Statistics;
use App\Services\Api\Athlete\Select\BaseService;
use Illuminate\Support\Collection;

class IndexStatisticService extends BaseService
{
    public function run(): Collection
    {
        $statistics = Statistics::select('id', 'name', 'sport_id', 'position_id')
            ->with([
                'sport:id,name',
                'position:id,name',
            ])
            ->orderBy('name')
            ->get();

        return $statistics->map(fn ($statistic) => [
            'id' => $statistic->id,
            'name' => $statistic->name,
            'sport' => $statistic->sport ? [
                'id' => $statistic->sport->id,
                'name' => $statistic->sport->name,
            ] : null,
            'position' => $statistic->position ? [
                'id' => $statistic->position->id,
                'name' => $statistic->position->name,
            ] : null,
        ]);
    }
}

class GetStatisticBySportService extends BaseService
{
    public function run(int $sportId): Collection
    {
        $statistics = Statistics::where('sport_id', $sportId)
            ->select('id', 'name', 'sport_id', 'position_id')
            ->with([
                'sport:id,name',
                'position:id,name',
            ])
            ->orderBy('name')
            ->get();

        return $statistics->map(fn ($statistic) => [
            'id' => $statistic->id,
            'name' => $statistic->name,
            'sport' => $statistic->sport ? [
                'id' => $statistic->sport->id,
                'name' => $statistic->sport->name,
            ] : null,
            'position' => $statistic->position ? [
                'id' => $statistic->position->id,
                'name' => $statistic->position->name,
            ] : null,
        ]);
    }
}
