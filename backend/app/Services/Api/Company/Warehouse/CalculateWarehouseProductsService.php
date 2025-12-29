<?php

namespace App\Services\Api\Company\Warehouse;

use App\Models\Warehouse;

class CalculateWarehouseProductsService
{
    public function run(Warehouse $warehouse): array
    {
        if (!$warehouse->relationLoaded('stocks')) {
            return [];
        }

        $availableStocks = $warehouse->stocks->where('is_available_use', true)->where('status', 'concluido')->where('is_sale', false);

        if ($availableStocks->isEmpty()) {
            return [];
        }

        $productGroups = $availableStocks
            ->filter(function ($stock) {
                return $stock->relationLoaded('product') && $stock->product;
            })
            ->groupBy('product_id');

        $soldStocks = $warehouse->stocks->where('is_available_use', true)->where('status', 'concluido')->where('is_sale', true);
        $soldProductGroups = $soldStocks
            ->filter(function ($stock) {
                return $stock->relationLoaded('product') && $stock->product;
            })
            ->groupBy('product_id');

        return $productGroups->map(function ($stocks) use ($soldProductGroups) {
            $product = $stocks->first()->product;
            $totalQuantity = $stocks->count();
            $totalValue = $stocks->sum(function ($stock) {
                return (float) $stock->product->price;
            });
            $averageCost = $totalQuantity > 0 ? round($totalValue / $totalQuantity, 2) : 0.0;

            $soldStocksForProduct = $soldProductGroups->get($product->id, collect());
            $totalSold = $soldStocksForProduct->count();

            return [
                'name' => $product->name,
                'total' => $totalQuantity,
                'totalSold' => $totalSold,
                'averageCost' => $averageCost
            ];
        })->values()->toArray();
    }
}