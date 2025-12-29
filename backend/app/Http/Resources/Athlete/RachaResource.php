<?php

namespace App\Http\Resources\Athlete;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RachaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource['id'],
            'bookingNumber' => $this->resource['booking_number'],
            'bookingDate' => $this->resource['booking_date'],
            'startTime' => $this->resource['start_time'],
            'endTime' => $this->resource['end_time'],
            'bookingStatus' => $this->resource['booking_status'],
            'fieldName' => $this->resource['field_name'],
            'statistics' => $this->resource['statistics'],
            'rating' => $this->resource['rating'],
            'participationId' => $this->resource['participation_id'],
        ];
    }
}
