<?php

namespace App\Services\Api\Company\Field;

use App\Models\Field;
use App\Services\Api\Company\Global\BaseService;
use Illuminate\Support\Facades\DB;

class StoreFieldService extends BaseService
{
    public function __construct(
        protected SyncSchedulesService $scheduleService,
        protected CreateFieldImagesService $createFieldImagesService,
    ) {}

    public function run(array $data): Field
    {
        $company = $this->getCompany();

        $data['company_id'] = $company->id;
        $itemIds = $data['item_ids'] ?? [];
        $schedules = $data['schedules'] ?? [];

        unset($data['item_ids'], $data['schedules']);

        return DB::transaction(function () use ($data, $itemIds, $schedules) {

            $field = Field::create($data);

            if ($itemIds) {
                $field->fieldItems()->sync($itemIds);
            }

            if ($schedules) {
                $this->scheduleService->sync($field, $schedules);
            }

            return $field;
        });
    }
}
