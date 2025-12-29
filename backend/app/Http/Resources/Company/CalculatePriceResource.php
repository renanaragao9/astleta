<?php

namespace App\Http\Resources\Company;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CalculatePriceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'durationMinutes' => $this->resource['duration_minutes'],
            'durationHours' => $this->resource['duration_hours'],
            'basePrice' => $this->resource['base_price'],
            'extraHourPrice' => $this->resource['extra_hour_price'],
            'totalPrice' => $this->resource['total_price'],
        ];
    }
}
