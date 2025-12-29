<?php

namespace App\Services\Api\Athlete\Profile;

use App\Models\PlayerStatistic;
use App\Models\User;
use App\Services\Api\Athlete\Global\BaseService;

class CalculatePlayerStatisticsService extends BaseService
{
    public function run(User $user): array
    {
        $playerStatistics = PlayerStatistic::where('user_id', $user->id)
            ->with([
                'statistic' => function ($query) {
                    $query->select('id', 'name', 'icon', 'color');
                },
            ])
            ->get();

        $groupedStats = $playerStatistics->groupBy('statistic.name')
            ->map(function ($group) {
                $firstStat = $group->first();

                return [
                    'value' => $group->sum('count'),
                    'icon' => $firstStat->statistic->icon,
                    'color' => $firstStat->statistic->color,
                ];
            });

        $totalGames = $this->calculateTotalGames($user->id);
        $statistics = $groupedStats->toArray();

        if ($totalGames > 0) {
            $statistics['Jogos'] = [
                'value' => $totalGames,
                'icon' => 'pi pi-users',
                'color' => 'text-primary',
            ];
        }

        return $statistics;
    }

    private function calculateTotalGames(int $userId): int
    {
        return PlayerStatistic::query()
            ->where('user_id', $userId)
            ->distinct()
            ->count('booking_id');
    }
}
