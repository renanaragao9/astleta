<?php

namespace App\Http\Resources\Company;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TabResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'customerName' => $this->customer_name,
            'status' => $this->status,
            'totalAmount' => $this->total_amount,
            'paymentFormId' => $this->payment_form_id,
            'openedAt' => $this->opened_at?->format('d/m/Y H:i:s'),
            'closedAt' => $this->closed_at?->format('d/m/Y H:i:s'),
            'created' => $this->created_at?->format('d/m/Y H:i:s'),

            'company' => $this->whenLoaded('company', function () {
                return [
                    'id' => $this->company->id,
                    'name' => $this->company->name,
                ];
            }),

            'paymentForm' => $this->whenLoaded('paymentForm', function () {
                return [
                    'id' => $this->paymentForm->id,
                    'name' => $this->paymentForm->name,
                ];
            }),

            'tabItems' => $this->whenLoaded('tabItems', function () {
                return $this->tabItems->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'quantity' => $item->quantity,
                        'total' => $item->total,
                        'observation' => $item->observation,
                        'productId' => $item->product_id,
                        'product' => $item->product ? [
                            'id' => $item->product->id,
                            'name' => $item->product->name,
                            'price' => $item->product->price,
                        ] : null,
                        'created' => $item->created_at?->format('d/m/Y H:i:s'),
                    ];
                });
            }),
        ];
    }
}
