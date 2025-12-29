<?php

namespace App\Services\Api\Global\AthleteProfile;

use App\Models\AthleteProfile;
use App\Models\Team;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class IndexPublicAthleteProfileService
{
    public function run(array $data): LengthAwarePaginator
    {
        $query = AthleteProfile::with([
            'user:id,name,email,image_path',
            'sport:id,name',
            'position:id,name',
            'subposition:id,name',
            'feature:id,name',
            'subfeature:id,name',
        ])
            ->whereHas('user', function ($query) {
                $query->where('is_public', true);
            });

        $this->applySearchFilter($query, $data);

        $perPage = $data['per_page'] ?? 12;
        $page = $data['page'] ?? 1;

        $paginated = $query->paginate($perPage, ['*'], 'page', $page);

        $this->loadTeamsForUsers($paginated);

        return $paginated;
    }

    private function applySearchFilter($query, array $data): void
    {
        if (! isset($data['name'])) {
            return;
        }

        $search = $data['name'];

        if ($this->isUuid($search)) {
            $query->where('uuid', $search);
        } else {
            $query->whereHas('user', function ($userQuery) use ($search) {
                $userQuery->where('name', 'ILIKE', '%'.$search.'%');
            });
        }
    }

    private function isUuid(string $value): bool
    {
        return preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{3}-[0-9a-f]{3}-[0-9a-f]{12}$/i', $value) === 1;
    }

    private function loadTeamsForUsers($paginated): void
    {
        $userIds = $paginated->getCollection()->pluck('user_id');

        if ($userIds->isEmpty()) {
            return;
        }

        $teams = Team::with([
            'sport:id,name',
            'teamType:id,name',
            'creator:id,name,phone,uuid',
            'teamPlayers:id,user_id,team_id,status',
        ])
            ->where(function ($query) use ($userIds) {
                $query->whereIn('user_id', $userIds)
                    ->orWhereHas('teamPlayers', function ($subQuery) use ($userIds) {
                        $subQuery->whereIn('user_id', $userIds)
                            ->where('status', 'ativo');
                    });
            })
            ->get();

        $teamMap = [];
        $userIdsArray = $userIds->toArray();

        foreach ($teams as $team) {
            if (in_array($team->user_id, $userIdsArray)) {
                $teamMap[$team->user_id] = $team;
            }

            foreach ($team->teamPlayers as $player) {
                if (in_array($player->user_id, $userIdsArray)) {
                    $teamMap[$player->user_id] = $team;
                }
            }
        }

        foreach ($paginated as $athlete) {
            $athlete->user->team = $teamMap[$athlete->user_id] ?? null;
        }
    }
}
