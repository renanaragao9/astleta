<?php

namespace App\Http\Resources\Public;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublicBookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        if (! $this->resource) {
            return [];
        }

        return [
            'id' => $this->id,
            'booking_number' => $this->booking_number,
            'booking_date' => $this->booking_date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'duration_minutes' => $this->duration_minutes,
            'total_amount' => $this->total_amount,
            'payment_type' => $this->payment_type,
            'booking_status' => $this->booking_status,
            'customer_name' => $this->customer_name,
            'customer_email' => $this->customer_email,
            'customer_phone' => $this->customer_phone,
            'customer_cpf' => $this->customer_cpf,
            'notes' => $this->notes,
            'field' => $this->field ? [
                'id' => $this->field->id,
                'name' => $this->field->name,
                'price_per_hour' => $this->field->price_per_hour,
            ] : null,
            'company' => $this->company ? [
                'id' => $this->company->id,
                'name' => $this->company->name,
                'address' => $this->company->address,
                'city' => $this->company->city,
                'state' => $this->company->state,
                'phone' => $this->company->phone,
                'email' => $this->company->email,
            ] : null,
            'payment_form' => $this->paymentForm ? [
                'id' => $this->paymentForm->id,
                'name' => $this->paymentForm->name,
            ] : null,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
