<?php

namespace App\Services\Api\Company\Field;

use App\Models\Field;
use App\Services\Api\Company\Global\BaseService;
use Illuminate\Support\Facades\DB;

class UpdateFieldService extends BaseService
{
    public function __construct(
        protected SyncSchedulesService $scheduleService,
    ) {}

    public function run(Field $field, array $data): Field
    {
        $company = $this->getCompany();

        $data['company_id'] = $company->id;
        $itemIds = $data['item_ids'] ?? [];
        $schedules = $data['schedules'] ?? [];

        unset($data['item_ids'], $data['schedules'], $data['images']);

        return DB::transaction(function () use ($field, $data, $itemIds, $schedules) {
            $field->update($data);

            if ($itemIds) {
                $field->fieldItems()->sync($itemIds);
            }

            if ($schedules) {
                $this->scheduleService->sync($field, $schedules);
            }

            return $field->refresh()->load([
                'fieldType',
                'fieldSurface',
                'fieldSize',
                'company',
                'fieldItems',
                'fieldSchedules',
            ]);
        });
    }
}
