<?php

namespace App\Http\Resources\Company;

use App\Helpers\GenerateTemporaryUrlSHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FieldResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'pricePerHour' => $this->price_per_hour,
            'extraHourPrice' => $this->extra_hour_price,
            'fieldTypeId' => $this->field_type_id,
            'fieldSurfaceId' => $this->field_surface_id,
            'fieldSizeId' => $this->field_size_id,
            'isActive' => $this->is_active,
            'isAllowsExtraHour' => $this->is_allows_extra_hour,
            'created' => $this->created_at?->format('d/m/Y H:i:s'),
            'imagePath' => $this->image_path ? GenerateTemporaryUrlSHelper::run($this->image_path, 1) : null,

            'fieldType' => $this->whenLoaded('fieldType', fn (): array => [
                'id' => $this->fieldType->id,
                'name' => $this->fieldType->name,
            ]),

            'fieldSurface' => $this->whenLoaded('fieldSurface', fn (): array => [
                'id' => $this->fieldSurface->id,
                'name' => $this->fieldSurface->name,
            ]),

            'fieldSize' => $this->whenLoaded('fieldSize', fn (): array => [
                'id' => $this->fieldSize->id,
                'name' => $this->fieldSize->name,
            ]),

            'selectedItemIds' => $this->whenLoaded(
                'fieldItems',
                fn (): array => $this->fieldItems->pluck('id')->toArray()
            ),

            'schedules' => $this->whenLoaded(
                'fieldSchedules',
                fn (): array => $this->fieldSchedules->map(fn ($schedule) => [
                    'id' => $schedule->id,
                    'dayOfWeek' => $schedule->day_of_week,
                    'startTime' => substr($schedule->start_time, 0, 5),
                    'endTime' => substr($schedule->end_time, 0, 5),
                ])->toArray()
            ),

            'fieldImages' => $this->whenLoaded(
                'fieldImages',
                fn (): array => $this->fieldImages->map(fn ($image) => [
                    'id' => $image->id,
                    'file' => $image->file,
                    'caption' => $image->caption,
                ])->toArray()
            ),
        ];
    }
}
