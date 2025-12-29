<?php

namespace App\Http\Resources\Athlete;

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
            'openedAt' => $this->opened_at?->format('d/m/Y H:i:s'),
            'closedAt' => $this->closed_at?->format('d/m/Y H:i:s'),
            'userId' => $this->user_id,
            'paymentFormId' => $this->payment_form_id,
            'created' => $this->created_at?->format('d/m/Y H:i:s'),

            'user' => $this->whenLoaded('user', fn () => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ]),

            'paymentForm' => $this->whenLoaded('paymentForm', fn () => [
                'id' => $this->paymentForm->id,
                'name' => $this->paymentForm->name,
            ]),

            'tabItems' => $this->whenLoaded('tabItems', fn () => $this->tabItems->map(fn ($item) => [
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
            ])),
        ];
    }
}
