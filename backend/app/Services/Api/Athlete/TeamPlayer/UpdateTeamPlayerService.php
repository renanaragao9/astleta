<?php

namespace App\Services\Api\Athlete\TeamPlayer;

use App\Models\Team;
use App\Models\TeamPlayer;
use App\Services\Api\Athlete\Global\BaseService;
use Illuminate\Validation\ValidationException;

class UpdateTeamPlayerService extends BaseService
{
    public function run(Team $team, TeamPlayer $teamPlayer, array $data): TeamPlayer
    {
        $userId = $this->getUserId();

        $team = Team::where('id', $team->id)
            ->where('user_id', $userId)
            ->first();

        $teamPlayer = TeamPlayer::where('id', $teamPlayer->id)
            ->where('team_id', $team->id)
            ->first();

        if (! $team) {
            throw ValidationException::withMessages(['error' => 'Time não encontrado ou você não tem permissão para gerenciá-lo.']);
        }

        if (! $teamPlayer) {
            throw ValidationException::withMessages(['error' => 'Jogador não encontrado no time.']);
        }

        $newRole = $data['role'] ?? $teamPlayer->role;
        if (in_array($newRole, ['capitao', 'treinador']) && $newRole !== $teamPlayer->role) {
            $existingRole = TeamPlayer::where('team_id', $team->id)
                ->where('role', $newRole)
                ->where('status', 'ativo')
                ->where('id', '!=', $teamPlayer->id)
                ->first();

            if ($existingRole) {
                $roleName = $newRole === 'capitao' ? 'capitão' : 'treinador';
                throw ValidationException::withMessages(['error' => "Já existe um {$roleName} ativo no time."]);
            }
        }

        if (isset($data['number']) && $data['number'] && $data['number'] != $teamPlayer->number) {
            $existingNumber = TeamPlayer::where('team_id', $team->id)
                ->where('number', $data['number'])
                ->where('status', 'ativo')
                ->where('id', '!=', $teamPlayer->id)
                ->first();

            if ($existingNumber) {
                throw ValidationException::withMessages(['error' => 'Número já está sendo usado por outro jogador.']);
            }
        }

        if (isset($data['status']) && $data['status'] === 'ativo' && $teamPlayer->status !== 'ativo') {
            $hasOtherTeam = TeamPlayer::where('user_id', $teamPlayer->user_id)
                ->where('team_id', '!=', $team->id)
                ->first();

            if ($hasOtherTeam) {
                throw ValidationException::withMessages(['error' => 'Jogador já está associado a outro time.']);
            }

            $hasCreatedTeam = Team::where('user_id', $teamPlayer->user_id)->exists();

            if ($hasCreatedTeam) {
                throw ValidationException::withMessages(['error' => 'Jogador já está associado a outro time.']);
            }

            $currentActivePlayers = TeamPlayer::query()
                ->where('team_id', $team->id)
                ->where('status', 'ativo')
                ->where('id', '!=', $teamPlayer->id)
                ->count();

            if ($currentActivePlayers >= $team->max_members) {
                throw ValidationException::withMessages(['error' => 'Time já atingiu o limite máximo de membros.']);
            }
        }

        $teamPlayer->update($data);

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

        return $teamPlayer;
    }
}
