<?php

namespace App\Services\Api\Company\Stock;

use App\Models\Stock;
use App\Services\Api\Company\Global\BaseService;
use App\Traits\Sortable;
use Illuminate\Pagination\LengthAwarePaginator;

class IndexStockService extends BaseService
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
        $isAvailableUse = $data['is_available_use'] ?? null;
        $isSale = $data['is_sale'] ?? null;
        $productId = $data['product_id'] ?? null;
        $warehouseId = $data['warehouse_id'] ?? null;

        $query = Stock::where('company_id', $company->id)
            ->with([
                'product',
                'warehouse',
                'company',
            ])
            ->when($search, function ($query) use ($search) {
                $query->whereHas('product', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                });
            })
            ->when($isAvailableUse !== null, function ($query) use ($isAvailableUse) {
                $query->where('is_available_use', $isAvailableUse);
            })
            ->when($isSale !== null, function ($query) use ($isSale) {
                $query->where('is_sale', $isSale);
            })
            ->when($productId, function ($query) use ($productId) {
                $query->where('product_id', $productId);
            })
            ->when($warehouseId, function ($query) use ($warehouseId) {
                $query->where('warehouse_id', $warehouseId);
            });

        $this->applySorting($query, $sort, $direction);

        return $query->paginate($perPage, ['*'], 'page', $page);
    }
}