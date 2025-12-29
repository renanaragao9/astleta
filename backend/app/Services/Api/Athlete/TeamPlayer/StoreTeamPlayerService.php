<?php

namespace App\Services\Api\Athlete\TeamPlayer;

use App\Models\Team;
use App\Models\TeamPlayer;
use App\Models\User;
use App\Notifications\MessageNotification;
use App\Services\Api\Athlete\Global\BaseService;
use Illuminate\Validation\ValidationException;

class StoreTeamPlayerService extends BaseService
{
    public function run(Team $team, array $data): TeamPlayer
    {
        $userId = $this->getUserId();

        $team = Team::where('id', $team->id)
            ->where('user_id', $userId)
            ->first();

        if (! $team) {
            throw ValidationException::withMessages(['error' => 'Time não encontrado ou você não tem permissão para gerenciá-lo.']);
        }

        $playerUser = null;
        if (isset($data['user_phone']) && $data['user_phone']) {
            $playerUser = User::where('phone', $data['user_phone'])->first();
        } elseif (isset($data['public_id']) && $data['public_id']) {
            $playerUser = User::where('uuid', $data['public_id'])->first();
        }

        if (! $playerUser) {
            throw ValidationException::withMessages(['error' => 'Usuário não encontrado.']);
        }

        $isTeamBoss = Team::where('user_id', $playerUser->id)
            ->first();

        if ($isTeamBoss) {
            throw ValidationException::withMessages(['error' => 'Usuário já é dono de um time.']);
        }

        $activePlayerInOtherTeam = TeamPlayer::where('user_id', $playerUser->id)
            ->where('status', 'ativo')
            ->where('team_id', '!=', $team->id)
            ->first();

        if ($activePlayerInOtherTeam) {
            throw ValidationException::withMessages(['error' => 'Jogador já está ativo em outro time.']);
        }

        $existingPlayer = TeamPlayer::where('team_id', $team->id)
            ->where('user_id', $playerUser->id)
            ->where('status', 'ativo')
            ->first();

        if ($existingPlayer) {
            throw ValidationException::withMessages(['error' => 'Jogador já está no time.']);
        }

        $role = $data['role'] ?? 'jogador';
        if (in_array($role, ['capitao', 'treinador'])) {
            $existingRole = TeamPlayer::where('team_id', $team->id)
                ->where('role', $role)
                ->where('status', 'ativo')
                ->first();

            if ($existingRole) {
                $roleName = $role === 'capitao' ? 'capitão' : 'treinador';
                throw ValidationException::withMessages(['error' => "Já existe um {$roleName} ativo no time."]);
            }
        }

        if (isset($data['number']) && $data['number']) {
            $existingNumber = TeamPlayer::where('team_id', $team->id)
                ->where('number', $data['number'])
                ->where('status', 'ativo')
                ->first();

            if ($existingNumber) {
                throw ValidationException::withMessages(['error' => 'Número já está sendo usado por outro jogador.']);
            }
        }

        $currentPlayersCount = TeamPlayer::where('team_id', $team->id)
            ->where('status', 'ativo')
            ->count();

        if ($currentPlayersCount >= $team->max_members) {
            throw ValidationException::withMessages(['error' => 'Time já atingiu o limite máximo de membros.']);
        }

        $data['team_id'] = $team->id;
        $data['user_id'] = $playerUser->id;
        $data['status'] = $data['status'] ?? 'ativo';
        $data['role'] = $data['role'] ?? 'jogador';
        $data['joined_at'] = $data['joined_at'] ?? now();

        unset($data['user_phone'], $data['public_id']);

        $teamPlayer = TeamPlayer::create($data);

        $teamPlayer->load([
            'user' => function ($query) {
                $query->select('id', 'name', 'email')->with([
                    'athleteProfile' => function ($query) {
                        $query->with('position:id,name');
                    },
                ]);
            },
            'team:id,name',
        ]);

        $message = "Você foi adicionado ao time {$teamPlayer->team->name} como {$teamPlayer->role}";
        if ($teamPlayer->number) {
            $message .= " com o número {$teamPlayer->number}";
        }
        $message .= '.';

        $teamPlayer->user->createNotificationMessage(
            get_class($teamPlayer),
            (new MessageNotification)
                ->message($message)
                ->icon('pi-check-circle')
                ->toArray()
        );

        return $teamPlayer;
    }
}
