<?php

namespace App\Services\Api\Company\TabItem;

use App\Models\TabItem;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class IndexTabItemService extends BaseService
{
    public function run(Request $request): LengthAwarePaginator
    {
        $company = $this->getCompany();

        if (! $company) {
            return new LengthAwarePaginator([], 0, 15);
        }

        $query = TabItem::whereHas('tab', function ($query) use ($company) {
            $query->where('company_id', $company->id);
        })
            ->with([
                'tab',
                'product',
            ]);

        // Filter by tab_id if provided
        if ($request->filled('tab_id')) {
            $query->where('tab_id', $request->tab_id);
        }

        // Filter by product_id if provided
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        // Sorting
        if ($request->filled('sort')) {
            $sortField = $request->sort;
            $sortDirection = $request->direction ?? 'asc';

            if (in_array($sortField, ['id', 'quantity', 'total', 'tab_id', 'product_id'])) {
                $query->orderBy($sortField, $sortDirection);
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $tabItems = $query->paginate($request->per_page ?? 15, ['*'], 'page', $request->page ?? 1);

        return $tabItems;
    }
}
