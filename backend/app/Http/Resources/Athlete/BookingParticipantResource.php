<?php

namespace App\Http\Resources\Athlete;

use App\Helpers\GenerateTemporaryUrlSHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingParticipantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $generateTemporaryUrl = new GenerateTemporaryUrlSHelper;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'amountPaid' => $this->amount_paid,
            'isPaid' => $this->is_paid,
            'status' => $this->status,
            'featureName' => $this->user?->athleteProfile?->feature?->name,
            'positionName' => $this->user?->athleteProfile?->position?->name,
            'imagePath' => $this->user?->image_path ? $generateTemporaryUrl->run($this->user->image_path, 1) : null,
            'paidAt' => $this->paid_at?->format('d/m/Y H:i:s'),
            'confirmedAt' => $this->confirmed_at?->format('d/m/Y H:i:s'),
            'createdAt' => $this->created_at->format('d/m/Y H:i:s'),
            'updatedAt' => $this->updated_at->format('d/m/Y H:i:s'),
        ];
    }
}
