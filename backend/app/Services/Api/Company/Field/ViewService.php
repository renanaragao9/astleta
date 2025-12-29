<?php

namespace App\Services\Api\Company\Field;

use App\Models\Field;
use App\Services\Api\Company\Global\BaseService;

class ViewService extends BaseService
{
    public function run(int $id): ?Field
    {
        $company = $this->getCompany();

        if (! $company) {
            return null;
        }

        $field = Field::where('id', $id)
            ->where('company_id', $company->id)
            ->with([
                'fieldType',
                'fieldSurface',
                'fieldSize',
                'fieldItems',
                'fieldSchedules',
                'fieldImages',
                'company',
            ])
            ->first();

        return $field;
    }
}
