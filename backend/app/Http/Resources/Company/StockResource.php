<?php

namespace App\Http\Resources\Company;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'isAvailableUse' => $this->is_available_use,
            'isSale' => $this->is_sale,
            'history' => $this->history,
            'productId' => $this->product_id,
            'warehouseId' => $this->warehouse_id,
            'created' => $this->created_at?->format('Y-m-d H:i:s'),

            'product' => $this->whenLoaded('product', function () {
                return [
                    'id' => $this->product->id,
                    'name' => $this->product->name,
                ];
            }),

            'warehouse' => $this->whenLoaded('warehouse', function () {
                return [
                    'id' => $this->warehouse->id,
                    'name' => $this->warehouse->name,
                ];
            }),
        ];
    }
}