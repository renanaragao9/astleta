<?php

namespace App\Http\Controllers\Api\Athlete\Team;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Athlete\TeamPlayer\StoreTeamPlayerRequest;
use App\Http\Requests\Api\Athlete\TeamPlayer\UpdateTeamPlayerRequest;
use App\Http\Resources\Athlete\TeamPlayerResource;
use App\Models\Team;
use App\Models\TeamPlayer;
use App\Services\Api\Athlete\TeamPlayer\DestroyTeamPlayerService;
use App\Services\Api\Athlete\TeamPlayer\IndexTeamPlayerService;
use App\Services\Api\Athlete\TeamPlayer\LeaveTeamPlayerService;
use App\Services\Api\Athlete\TeamPlayer\StoreTeamPlayerService;
use App\Services\Api\Athlete\TeamPlayer\UpdateTeamPlayerService;
use Illuminate\Http\JsonResponse;

class TeamPlayerController extends BaseController
{
    public function index(
        Team $team,
        IndexTeamPlayerService $indexTeamPlayerService
    ): JsonResponse {
        $teamPlayers = $indexTeamPlayerService->run($team->id);

        return $this->successResponse(
            TeamPlayerResource::collection($teamPlayers),
            'Jogadores do time listados com sucesso.'
        );
    }

    public function store(
        Team $team,
        StoreTeamPlayerRequest $storeTeamPlayerRequest,
        StoreTeamPlayerService $storeTeamPlayerService
    ): JsonResponse {
        $data = $storeTeamPlayerRequest->validated();
        $teamPlayer = $storeTeamPlayerService->run($team, $data);

        return $this->successResponse(
            new TeamPlayerResource($teamPlayer),
            'Jogador adicionado ao time com sucesso.'
        );
    }

    public function update(
        Team $team,
        TeamPlayer $teamPlayer,
        UpdateTeamPlayerRequest $updateTeamPlayerRequest,
        UpdateTeamPlayerService $updateTeamPlayerService
    ): JsonResponse {
        $data = $updateTeamPlayerRequest->validated();
        $updatedTeamPlayer = $updateTeamPlayerService->run($team, $teamPlayer, $data);

        return $this->successResponse(
            new TeamPlayerResource($updatedTeamPlayer),
            'Dados do jogador atualizados com sucesso.'
        );
    }

    public function destroy(
        Team $team,
        TeamPlayer $teamPlayer,
        DestroyTeamPlayerService $destroyTeamPlayerService
    ): JsonResponse {
        $destroyTeamPlayerService->run($team, $teamPlayer);

        return $this->successResponse(
            null,
            'Jogador removido do time com sucesso.'
        );
    }

    public function leave(
        Team $team,
        LeaveTeamPlayerService $leaveTeamPlayerService
    ): JsonResponse {
        $leaveTeamPlayerService->run($team);

        return $this->successResponse(
            null,
            'Você saiu do time com sucesso.'
        );
    }
}
