<?php

namespace App\Http\Resources\Public;

use App\Helpers\GenerateTemporaryUrlSHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublicCompanyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'phone' => $this->phone,
            'imagePath' => $this->image_path ? GenerateTemporaryUrlSHelper::run($this->image_path, 1) : null,
            'isOpen' => $this->is_open,

            'addresses' => $this->whenLoaded('addresses', function () {
                return $this->addresses->map(function ($address) {
                    return [
                        'id' => $address->id,
                        'street' => $address->street,
                        'number' => $address->number,
                        'district' => $address->district,
                        'city' => $address->city,
                        'state' => $address->state,
                        'zipcode' => $address->zipcode,
                        'fullAddress' => $address->fullAddress,
                        'latitude' => $address->latitude,
                        'longitude' => $address->longitude,
                    ];
                });
            }),

            'contacts' => $this->whenLoaded('contacts', function () {
                return $this->contacts->map(function ($contact) {
                    return [
                        'type' => $contact->contactType->name,
                        'icon' => $contact->contactType->icon,
                        'value' => $contact->value,
                    ];
                });
            }),
        ];
    }
}
