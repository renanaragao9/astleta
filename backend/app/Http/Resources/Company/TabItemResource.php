<?php

namespace App\Http\Resources\Company;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TabItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'quantity' => $this->quantity,
            'total' => $this->total,
            'observation' => $this->observation,
            'tabId' => $this->tab_id,
            'productId' => $this->product_id,
            'created' => $this->created_at?->format('Y-m-d H:i:s'),

            'tab' => $this->whenLoaded('tab', function () {
                return [
                    'id' => $this->tab->id,
                    'code' => $this->tab->code,
                    'customerName' => $this->tab->customer_name,
                    'status' => $this->tab->status,
                ];
            }),

            'product' => $this->whenLoaded('product', function () {
                return [
                    'id' => $this->product->id,
                    'name' => $this->product->name,
                    'description' => $this->product->description,
                    'price' => $this->product->price,
                ];
            }),
        ];
    }
}
