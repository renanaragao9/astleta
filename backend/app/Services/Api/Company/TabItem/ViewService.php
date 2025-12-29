<?php

namespace App\Services\Api\Company\TabItem;

use App\Models\TabItem;

class ViewService extends BaseService
{
    public function run(int $id): ?TabItem
    {
        $company = $this->getCompany();

        if (! $company) {
            return null;
        }

        return TabItem::whereHas('tab', function ($query) use ($company) {
            $query->where('company_id', $company->id);
        })
            ->with([
                'tab',
                'product',
            ])
            ->find($id);
    }
}
