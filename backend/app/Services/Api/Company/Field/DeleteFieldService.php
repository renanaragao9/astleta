<?php

namespace App\Services\Api\Company\Field;

use App\Models\Field;
use App\Services\Api\Company\Global\BaseService;

class DeleteFieldService extends BaseService
{
    public function run(Field $field): void
    {
        $this->getCompany();

        $field->delete();
    }
}
