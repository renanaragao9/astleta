<?php

namespace App\Http\Resources\Public;

use App\Helpers\GenerateTemporaryUrlSHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublicFieldViewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'pricePerHour' => $this->price_per_hour,
            'extraHourPrice' => $this->extra_hour_price,
            'isAllowsExtraHour' => $this->is_allows_extra_hour,
            'imagePath' => $this->image_path ? GenerateTemporaryUrlSHelper::run($this->image_path, 1) : null,

            'fieldType' => $this->whenLoaded('fieldType', fn (): array => [
                'name' => $this->fieldType->name,
            ]),

            'fieldSurface' => $this->whenLoaded('fieldSurface', fn (): array => [
                'name' => $this->fieldSurface->name,
            ]),

            'fieldSize' => $this->whenLoaded('fieldSize', fn (): array => [
                'name' => $this->fieldSize->name,
            ]),

            'company' => $this->whenLoaded('company', fn (): array => [
                'id' => $this->company->id,
                'name' => $this->company->name,
                'phone' => $this->company->phone,
                'address' => $this->company->relationLoaded('addresses') ? $this->company->addresses->first()?->fullAddress : null,
                'imagePath' => $this->company->image_path ? GenerateTemporaryUrlSHelper::run($this->company->image_path, 1) : null,
            ]),

            'selectedItem' => $this->whenLoaded(
                'fieldItems',
                fn (): array => $this->fieldItems->map(fn ($item) => [
                    'name' => $item->name,
                    'icon' => $item->icon,
                ])->toArray()
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

            'fieldImage' => $this->whenLoaded('fieldImages', fn () => $this->fieldImages->map(fn ($image) => [
                'imagePath' => $image->image_path ? GenerateTemporaryUrlSHelper::run($image->image_path, 1) : null,
                'caption' => $image->caption,
            ])),
        ];
    }
}
