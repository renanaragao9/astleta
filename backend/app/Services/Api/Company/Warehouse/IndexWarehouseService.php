<?php

namespace App\Services\Api\Company\Warehouse;

use App\Models\Warehouse;
use App\Services\Api\Company\Global\BaseService;
use App\Traits\Sortable;
use Illuminate\Pagination\LengthAwarePaginator;

class IndexWarehouseService extends BaseService
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

        $query = Warehouse::where('company_id', $company->id)
            ->with([
                'company',
                'stocks.product',
                'stocks.stockMovements'
            ])
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('location', 'like', '%' . $search . '%');
            });

        $this->applySorting($query, $sort, $direction);

        return $query->paginate($perPage, ['*'], 'page', $page);
    }
}