<?php

namespace App\Services\Api\Company\Purchase;

use App\Models\Purchase;
use App\Services\Api\Company\Global\BaseService;
use App\Traits\Sortable;
use Illuminate\Pagination\LengthAwarePaginator;

class IndexPurchaseService extends BaseService
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
        $supplierId = $data['supplier_id'] ?? null;
        $status = $data['status'] ?? null;
        $startPurchaseDate = $data['start_purchase_date'] ?? null;
        $endPurchaseDate = $data['end_purchase_date'] ?? null;

        $query = Purchase::where('company_id', $company->id)
            ->with(
                'supplier',
                'stockMovements',
                'stockMovements.stock.product',
                'stockMovements.stock.warehouse'
            )
            ->when($search, function ($query) use ($search) {
                $query->where('invoice_number', 'like', '%' . $search . '%')
                    ->orWhereHas('supplier', function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%');
                    });
            })
            ->when($supplierId, function ($query) use ($supplierId) {
                $query->where('supplier_id', $supplierId);
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($startPurchaseDate, function ($query) use ($startPurchaseDate) {
                $query->where('purchase_date', '>=', $startPurchaseDate);
            })
            ->when($endPurchaseDate, function ($query) use ($endPurchaseDate) {
                $query->where('purchase_date', '<=', $endPurchaseDate);
            });

        $this->applySorting($query, $sort, $direction);

        return $query->paginate($perPage, ['*'], 'page', $page);
    }
}