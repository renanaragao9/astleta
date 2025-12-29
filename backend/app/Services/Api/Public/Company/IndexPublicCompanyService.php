<?php

namespace App\Services\Api\Public\Company;

use App\Models\Company;

class IndexPublicCompanyService
{
    public function run(Company $company): array
    {
        $company->load([
            'addresses',
            'contacts.contactType',
        ]);

        $fields = $company->fields()
            ->with([
                'fieldType',
                'fieldSurface',
                'fieldSize',
                'fieldItems',
                'fieldSchedules',
                'fieldImages',
            ])
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get();

        return [
            'company' => $company,
            'fields' => $fields,
        ];
    }
}
