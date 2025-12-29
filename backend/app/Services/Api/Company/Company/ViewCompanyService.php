<?php

namespace App\Services\Api\Company\Company;

use App\Models\Company;
use App\Services\Api\Company\Global\BaseService;

class ViewCompanyService extends BaseService
{
    public function run(): Company
    {
        $userId = $this->getUserId();

        $company = Company::where('user_id', $userId)
            ->with([
                'user',
                'contacts.contactType',
                'documents.documentType',
                'fields.fieldType',
                'fields.fieldSurface',
                'fields.fieldSize',
                'addresses',
                'products.productType',
                'expenses.expenseType',
            ])
            ->first();

        return $company;
    }
}
