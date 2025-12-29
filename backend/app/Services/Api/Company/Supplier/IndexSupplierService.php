<?php

namespace App\Services\Api\Company\Supplier;

use App\Models\Supplier;
use App\Services\Api\Company\Global\BaseService;
use App\Traits\Sortable;
use Illuminate\Pagination\LengthAwarePaginator;

class IndexSupplierService extends BaseService
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

        $query = Supplier::where('company_id', $company->id)
            ->with([
                'company',
            ])
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%');
            });

        $this->applySorting($query, $sort, $direction);

        return $query->paginate($perPage, ['*'], 'page', $page);
    }
}