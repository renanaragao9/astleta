<?php

namespace App\Services\Api\Athlete\TeamPlayer;

use App\Models\Team;
use App\Models\TeamPlayer;
use App\Notifications\MessageNotification;
use App\Services\Api\Athlete\Global\BaseService;
use Illuminate\Validation\ValidationException;

class DestroyTeamPlayerService extends BaseService
{
    public function run(Team $team, TeamPlayer $teamPlayer): void
    {
        $userId = $this->getUserId();

        $team = Team::where('id', $team->id)
            ->where('user_id', $userId)
            ->first();

        $teamPlayer = TeamPlayer::where('id', $teamPlayer->id)
            ->where('team_id', $team->id)
            ->first();

        if (! $userId) {
            throw ValidationException::withMessages(['error' => 'Usuário não autenticado.']);
        }

        if (! $team) {
            throw ValidationException::withMessages(['error' => 'Time não encontrado ou você não tem permissão para gerenciá-lo.']);
        }

        if (! $teamPlayer) {
            throw ValidationException::withMessages(['error' => 'Jogador não encontrado no time.']);
        }

        if ($teamPlayer->user_id === $team->user_id) {
            throw ValidationException::withMessages(['error' => 'O criador do time não pode ser removido.']);
        }

        $teamPlayer->load(['user:id,name,email', 'team:id,name']);
        $team->load('user:id,name,email');

        $teamPlayer->delete();

        $message = "O usuário {$teamPlayer->user->name} saiu do time {$teamPlayer->team->name}.";

        $team->user->createNotificationMessage(
            get_class($teamPlayer),
            (new MessageNotification)
                ->message($message)
                ->icon('pi-check-circle')
                ->toArray()
        );
    }
}
