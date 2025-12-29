<?php

namespace App\Services\Api\Company\Field;

use App\Models\Field;
use App\Services\Api\Company\Global\BaseService;
use App\Traits\Sortable;
use Illuminate\Pagination\LengthAwarePaginator;

class IndexFieldService extends BaseService
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

        $query = Field::where('company_id', $company->id)
            ->with([
                'fieldType',
                'fieldSurface',
                'fieldSize',
                'fieldItems',
                'fieldSchedules',
                'fieldImages',
                'company',
            ])
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            });

        $query = $this->applySorting($query, $sort, $direction);

        return $query->paginate($perPage, ['*'], 'page', $page);
    }
}
