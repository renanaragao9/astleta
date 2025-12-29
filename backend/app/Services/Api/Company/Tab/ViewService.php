<?php

namespace App\Services\Api\Company\Tab;

use App\Models\Tab;
use App\Services\Api\Company\Global\BaseService;

class ViewService extends BaseService
{
    public function run(int $id): ?Tab
    {
        $company = $this->getCompany();

        if (! $company) {
            return null;
        }

        return Tab::where('company_id', $company->id)
            ->with([
                'company',
                'paymentForm',
                'tabItems.product',
            ])
            ->find($id);
    }
}
