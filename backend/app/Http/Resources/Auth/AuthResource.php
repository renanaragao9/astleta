<?php

namespace App\Http\Resources\Auth;

use App\Helpers\GenerateTemporaryUrlSHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $generateTemporaryUrl = new GenerateTemporaryUrlSHelper;

        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'phone' => $this->phone,
            'cpf' => $this->cpf,
            'date' => $this->date?->format('Y-m-d'),
            'imagePath' => $this->image_path ? $generateTemporaryUrl->run($this->image_path, 1) : null,
            'email_verified_at' => $this->email_verified_at?->format('Y-m-d H:i:s'),
            'profile' => [
                'name' => $this->profile?->name,
            ],
        ];
    }
}
