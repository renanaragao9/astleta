<?php

namespace App\Http\Resources\Athlete;

use App\Helpers\GenerateTemporaryUrlSHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeamResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $generateTemporaryUrl = new GenerateTemporaryUrlSHelper;

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'nickname' => $this->nickname,
            'stadiumName' => $this->stadium_name,
            'primaryColor' => $this->primary_color,
            'secondaryColor' => $this->secondary_color,
            'shieldPath' => $this->shield_path ? $generateTemporaryUrl->run($this->shield_path, 1) : null,
            'website' => $this->website,
            'foundedDate' => $this->founded_date?->format('d/m/Y'),
            'description' => $this->description,
            'welcomeEmail' => $this->welcome_email,
            'maxMembers' => $this->max_members,
            'isPublic' => $this->is_public,
            'createdAt' => $this->created_at?->format('d/m/Y H:i:s'),
            'updatedAt' => $this->updated_at?->format('d/m/Y H:i:s'),

            'sport' => $this->whenLoaded('sport', fn() => [
                'id' => $this->sport->id,
                'name' => $this->sport->name,
            ]),

            'teamType' => $this->whenLoaded('teamType', fn() => [
                'id' => $this->teamType->id,
                'name' => $this->teamType->name,
            ]),

            'creator' => $this->whenLoaded('creator', fn() => [
                'name' => $this->creator->name,
                'phone' => $this->creator->phone,
                'uuid' => $this->creator->uuid,
            ]),
        ];
    }
}
