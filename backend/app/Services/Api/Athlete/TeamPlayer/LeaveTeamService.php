<?php

namespace App\Services\Api\Athlete\TeamPlayer;

use App\Models\Team;
use App\Models\TeamPlayer;
use App\Services\Api\Athlete\Global\BaseService;

class LeaveTeamService extends BaseService
{
    public function run(int $teamId): array
    {
        $userId = $this->getUserId();

        if (! $userId) {
            return [
                'success' => false,
                'message' => 'Usuário não autenticado.',
                'data' => null,
            ];
        }

        $team = Team::find($teamId);
        if (! $team) {
            return [
                'success' => false,
                'message' => 'Time não encontrado.',
                'data' => null,
            ];
        }

        if ($team->user_id == $userId) {
            return [
                'success' => false,
                'message' => 'O criador do time não pode sair do time.',
                'data' => null,
            ];
        }

        $teamPlayer = TeamPlayer::where('team_id', $teamId)
            ->where('user_id', $userId)
            ->where('status', '!=', 'rescindido')
            ->first();

        if (! $teamPlayer) {
            return [
                'success' => false,
                'message' => 'Você não é membro deste time.',
                'data' => null,
            ];
        }

        $teamPlayer->update([
            'status' => 'rescindido',
            'left_at' => now(),
        ]);

        return [
            'success' => true,
            'message' => 'Você saiu do time com sucesso.',
            'data' => null,
        ];
    }
}
