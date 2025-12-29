<?php

namespace App\Services\Api\Company\Product;

use App\Models\Product;
use App\Services\Api\Company\Global\BaseService;
use App\Traits\Sortable;
use Illuminate\Pagination\LengthAwarePaginator;

class IndexProductService extends BaseService
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
        $isActive = $data['is_active'] ?? null;
        $productTypeId = $data['product_type_id'] ?? null;

        $query = Product::where('company_id', $company->id)
            ->with([
                'productType',
                'company',
            ])
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->when($isActive !== null, function ($query) use ($isActive) {
                $query->where('is_active', $isActive);
            })
            ->when($productTypeId !== null, function ($query) use ($productTypeId) {
                $query->where('product_type_id', $productTypeId);
            });

        $query = $this->applySorting($query, $sort, $direction);

        return $query->paginate($perPage, ['*'], 'page', $page);
    }
}
