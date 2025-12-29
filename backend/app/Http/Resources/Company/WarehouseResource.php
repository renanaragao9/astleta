<?php

namespace App\Http\Resources\Company;

use App\Services\Api\Company\Warehouse\CalculateWarehouseProductsService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WarehouseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'location' => $this->location,
            'created' => $this->created_at?->format('d/m/Y H:i:s'),

            'totalStockValue' => $this->whenLoaded('stocks', function () {
                return $this->stocks
                    ->where('is_sale', false)
                    ->where('is_available_use', true)
                    ->where('status', 'concluido')
                    ->sum(function ($stock) {
                        return $stock->relationLoaded('product') && $stock->product ? (float) $stock->product->price : 0.0;
                    });
            }, 0.0),

            'totalStock' => $this->whenLoaded('stocks', function () {
                return $this->stocks
                    ->where('is_sale', false)
                    ->where('is_available_use', true)
                    ->where('status', 'concluido')
                    ->count();
            }, 0),

            'totalSold' => $this->whenLoaded('stocks', function () {
                return $this->stocks
                    ->where('is_available_use', true)
                    ->where('status', 'concluido')
                    ->where('is_sale', true)
                    ->count();
            }, 0),

            'totalUnavailable' => $this->whenLoaded('stocks', function () {
                return $this->stocks
                    ->where('is_available_use', false)
                    ->count();
            }, 0),

            'products' => $this->whenLoaded('stocks', function () {
                $calculateProductsService = new CalculateWarehouseProductsService();
                return $calculateProductsService->run($this->resource);
            }, []),
        ];
    }
}