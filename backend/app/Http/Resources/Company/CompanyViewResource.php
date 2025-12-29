<?php

namespace App\Http\Resources\Company;

use App\Helpers\GenerateTemporaryUrlSHelper;
use App\Services\Api\Company\Resource\CompanyFinancialService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyViewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $generateTemporaryUrl = new GenerateTemporaryUrlSHelper;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'cnpj' => $this->cnpj,
            'cpf' => $this->cpf,
            'phone' => $this->phone,
            'description' => $this->description,
            'createdAt' => $this->created_at?->locale('pt_BR')->translatedFormat('d/m/Y H:i'),
            'updatedAt' => $this->updated_at?->locale('pt_BR')->translatedFormat('d/m/Y H:i'),
            'imagePath' => $this->image_path ? $generateTemporaryUrl->run($this->image_path, 1) : null,
            'status' => $this->status,

            'user' => $this->whenLoaded('user', fn (): array => [
                'name' => $this->user->name,
                'email' => $this->user->email,
            ]),

            'companyStatus' => $this->whenLoaded('companyStatus', fn () => [
                'name' => $this->companyStatus->name,
            ]),

            'contacts' => $this->when(
                $this->resource->relationLoaded('contacts'),
                fn () => $this->contacts->map(fn ($contact) => [
                    'type' => $this->when(
                        $contact->relationLoaded('contactType'),
                        fn () => [
                            'name' => $contact->contactType->name,
                            'icon' => $contact->contactType->icon,
                        ],
                        ['id' => null, 'name' => null, 'icon' => null]
                    ),
                    'value' => $contact->value,
                ]),
                []
            ),

            'documents' => $this->when(
                $this->resource->relationLoaded('documents'),
                fn () => $this->documents->map(fn ($document) => [
                    'type' => $this->when(
                        $document->relationLoaded('documentType'),
                        fn () => [
                            'id' => $document->documentType->id,
                            'name' => $document->documentType->name,
                        ],
                        ['id' => null, 'name' => null]
                    ),
                    'number' => $document->number,
                    'filePath' => $document->file_path ? $generateTemporaryUrl->run($document->file_path, 1) : null,
                    'status' => $document->status,
                    'description' => $document->description,
                ]),
                []
            ),

            'address' => $this->when(
                $this->resource->relationLoaded('addresses'),
                function () {
                    $address = $this->addresses->first();

                    return $address ? [
                        'zipcode' => $address->zipcode,
                        'country' => $address->country,
                        'state' => $address->state,
                        'city' => $address->city,
                        'district' => $address->district,
                        'street' => $address->street,
                        'number' => $address->number,
                        'complement' => $address->complement,
                        'latitude' => $address->latitude,
                        'longitude' => $address->longitude,
                    ] : null;
                },
                null
            ),

            'monthlyRealizedTotals' => CompanyFinancialService::run($this->resource->id),
        ];
    }
}
