<?php

namespace App\Services\Api\Company\Tournament;

use App\Models\Tournament;
use App\Services\Api\Company\Global\BaseService;
use App\Traits\Sortable;
use Illuminate\Pagination\LengthAwarePaginator;

class IndexTournamentService extends BaseService
{
    use Sortable;

    public function run(array $data): LengthAwarePaginator
    {
        $company = $this->getCompany();
        $search = $data['search'] ?? null;
        $sort = $data['sort'] ?? null;
        $direction = $data['direction'] ?? 'asc';
        $perPage = $data['per_page'] ?? 15;
        $page = $data['page'] ?? 1;
        $status = $data['status'] ?? null;
        $isPublic = $data['is_public'] ?? null;

        $query = Tournament::where('company_id', $company->id)
            ->with([
                'company',
                'tournamentTeams.team',
            ])
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->when($status !== null, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($isPublic !== null, function ($query) use ($isPublic) {
                $query->where('is_public', $isPublic);
            });

        $query = $this->applySorting($query, $sort, $direction);

        return $query->paginate($perPage, ['*'], 'page', $page);
    }
}