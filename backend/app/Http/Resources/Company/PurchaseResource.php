<?php

namespace App\Http\Resources\Company;

use App\Services\Api\Company\Purchase\CalculatePurchaseProductsService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'invoiceNumber' => $this->invoice_number,
            'purchaseDate' => $this->purchase_date?->format('d/m/Y'),
            'status' => $this->status,
            'totalAmount' => $this->total_amount,
            'supplierId' => $this->supplier_id,
            'created' => $this->created_at?->format('d/m/Y H:i:s'),
            'supplier' => $this->whenLoaded('supplier', function () {
                return [
                    'id' => $this->supplier->id,
                    'name' => $this->supplier->name,
                ];
            }),
            'products' => $this->whenLoaded('stockMovements', function () {
                $calculateProductsService = new CalculatePurchaseProductsService();
                return $calculateProductsService->run($this->resource);
            }, []),
        ];
    }
}