<?php

namespace App\Http\Resources\Company;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SimpleBookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'bookingDate' => $this->booking_date->format('Y-m-d'),
            'startTime' => $this->formatTime($this->start_time),
            'endTime' => $this->formatTime($this->end_time),
            'bookingStatus' => $this->booking_status,
            'userName' => $this->whenLoaded('user', fn () => $this->user->name),
            'fieldName' => $this->whenLoaded('field', fn () => $this->field->name),
            'totalAmount' => $this->total_amount,
        ];
    }

    private function formatTime($time): string
    {
        if ($time instanceof \Carbon\Carbon) {
            return $time->format('H:i');
        }

        return is_string($time) ? substr($time, 0, 5) : $time;
    }
}
