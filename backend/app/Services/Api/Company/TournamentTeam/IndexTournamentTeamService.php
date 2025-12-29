<?php

namespace App\Services\Api\Company\TournamentTeam;

use App\Models\TournamentTeam;
use App\Services\Api\Company\Global\BaseService;
use App\Traits\Sortable;
use Illuminate\Pagination\LengthAwarePaginator;

class IndexTournamentTeamService extends BaseService
{
    use Sortable;

    public function run(array $data): LengthAwarePaginator
    {
        $company = $this->getCompany();
        $sort = $data['sort'] ?? 'position';
        $direction = $data['direction'] ?? 'asc';
        $perPage = $data['per_page'] ?? 15;
        $page = $data['page'] ?? 1;
        $tournamentId = $data['tournament_id'] ?? null;
        $search = $data['search'] ?? null;

        $query = TournamentTeam::with([
            'tournament',
            'team',
        ])
            ->whereHas('tournament', function ($query) use ($company) {
                $query->where('company_id', $company->id);
            })
            ->when($search, function ($query) use ($search) {
                $query->whereHas('team', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                });
            })
            ->when($tournamentId !== null, function ($query) use ($tournamentId) {
                $query->where('tournament_id', $tournamentId);
            });

        $query = $this->applySorting($query, $sort, $direction);

        return $query->paginate($perPage, ['*'], 'page', $page);
    }
}