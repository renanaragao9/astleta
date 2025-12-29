<?php

namespace App\Http\Resources\Athlete;

use App\Helpers\GenerateTemporaryUrlSHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeamPlayerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $generateTemporaryUrl = new GenerateTemporaryUrlSHelper;

        return [
            'id' => $this->id,
            'number' => $this->number,
            'role' => $this->role,
            'status' => $this->status,
            'joinedAt' => $this->joined_at?->format('d/m/Y'),
            'leftAt' => $this->left_at?->format('d/m/Y'),
            'createdAt' => $this->created_at?->format('d/m/Y H:i:s'),
            'updatedAt' => $this->updated_at?->format('d/m/Y H:i:s'),

            'user' => $this->whenLoaded('user', fn() => [
                'name' => $this->user->name,
                'phone' => $this->user->phone,
                'imagePath' => $this->user?->image_path ? $generateTemporaryUrl->run($this->user->image_path, 1) : null,
                
                'athleteProfile' => [
                    'position' => $this->user->athleteProfile?->position?->name,
                    'dominantSide' => $this->user->athleteProfile?->dominant_side,
                ],
            ]),

            'displayRole' => $this->getDisplayRole(),
            'displayStatus' => $this->getDisplayStatus(),
            'isActive' => $this->status === 'ativo',
        ];
    }

    private function getDisplayRole(): string
    {
        return match ($this->role) {
            'jogador' => 'Jogador(a)',
            'capitao' => 'Capitão(ã)',
            'treinador' => 'Treinador(a)',
            default => 'Jogador(a)'
        };
    }

    private function getDisplayStatus(): string
    {
        return match ($this->status) {
            'pendente' => 'Pendente',
            'ativo' => 'Ativo',
            'rescindido' => 'Rescindido',
            default => 'Ativo'
        };
    }
}
