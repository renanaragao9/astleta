<?php

namespace App\Services\Api\Company\Tab;

use App\Models\Tab;
use App\Services\Api\Company\Global\BaseService;
use App\Traits\Sortable;
use Illuminate\Pagination\LengthAwarePaginator;

class IndexTabService extends BaseService
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
        $customerName = $data['customer_name'] ?? null;
        $status = $data['status'] ?? null;
        $paymentFormId = $data['payment_form_id'] ?? null;
        $startCreatedDate = $data['start_created_date'] ?? null;
        $endCreatedDate = $data['end_created_date'] ?? null;

        $query = Tab::where('company_id', $company->id)
            ->with([
                'company',
                'paymentForm',
                'tabItems.product',
            ])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('code', 'like', '%'.$search.'%')
                        ->orWhere('customer_name', 'like', '%'.$search.'%');
                });
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($customerName, function ($query) use ($customerName) {
                $query->where('customer_name', 'like', '%'.$customerName.'%');
            })
            ->when($paymentFormId, function ($query) use ($paymentFormId) {
                $query->where('payment_form_id', $paymentFormId);
            })
            ->when($startCreatedDate, function ($query) use ($startCreatedDate) {
                $query->whereDate('created_at', '>=', $startCreatedDate);
            })
            ->when($endCreatedDate, function ($query) use ($endCreatedDate) {
                $query->whereDate('created_at', '<=', $endCreatedDate);
            });

        $query = $this->applySorting($query, $sort, $direction);

        return $query->paginate($perPage, ['*'], 'page', $page);
    }
}
