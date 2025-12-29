<?php

namespace App\Services\Api\Athlete\TeamPlayer;

use App\Models\Team;
use App\Models\TeamPlayer;
use App\Notifications\MessageNotification;
use App\Services\Api\Athlete\Global\BaseService;
use Illuminate\Validation\ValidationException;

class LeaveTeamPlayerService extends BaseService
{
    public function run(Team $team): void
    {
        $userId = $this->getUserId();

        if (! $userId) {
            throw ValidationException::withMessages(['error' => 'Usuário não autenticado.']);
        }

        $team = Team::where('id', $team->id)->first();

        if (! $team) {
            throw ValidationException::withMessages(['error' => 'Time não encontrado.']);
        }

        $teamPlayer = TeamPlayer::where('team_id', $team->id)
            ->where('user_id', $userId)
            ->first();

        if (! $teamPlayer) {
            throw ValidationException::withMessages(['error' => 'Você não é membro deste time.']);
        }

        if ($teamPlayer->user_id === $team->user_id) {
            throw ValidationException::withMessages(['error' => 'O criador do time não pode sair.']);
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
