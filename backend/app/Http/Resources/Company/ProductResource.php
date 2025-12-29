<?php

namespace App\Http\Resources\Company;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'productTypeId' => $this->product_type_id,
            'isActive' => $this->is_active,
            'created' => $this->created_at?->format('d/m/Y H:i:s'),

            'productType' => $this->whenLoaded('productType', function () {
                return [
                    'id' => $this->productType->id,
                    'name' => $this->productType->name,
                ];
            }),
        ];
    }
}
