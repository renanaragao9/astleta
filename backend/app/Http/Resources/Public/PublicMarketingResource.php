<?php

namespace App\Http\Resources\Public;

use App\Helpers\GenerateTemporaryUrlSHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublicMarketingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'title' => $this->title,
            'content' => $this->content,
            'link' => $this->link,
            'age' => $this->age,
            'startDate' => $this->start_date,
            'endDate' => $this->end_date,
            'imagePath' => $this->image_path ? GenerateTemporaryUrlSHelper::run($this->image_path, 1) : null,

            'marketingType' => $this->whenLoaded('marketingType', fn (): array => [
                'name' => $this->marketingType->name,
            ]),
        ];
    }
}
