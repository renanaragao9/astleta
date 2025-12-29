<?php

namespace App\Http\Controllers\Api\Athlete\Team;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Athlete\Team\StoreTeamRequest;
use App\Http\Requests\Api\Athlete\Team\UpdateTeamRequest;
use App\Http\Resources\Athlete\TeamDeparturesResource;
use App\Http\Resources\Athlete\TeamResource;
use App\Http\Resources\Athlete\TeamStatisticsResource;
use App\Models\Team;
use App\Services\Api\Athlete\Team\DeleteTeamService;
use App\Services\Api\Athlete\Team\IndexTeamService;
use App\Services\Api\Athlete\Team\StoreTeamService;
use App\Services\Api\Athlete\Team\TeamDeparturesService;
use App\Services\Api\Athlete\Team\TeamStatisticsService;
use App\Services\Api\Athlete\Team\UpdateTeamImageService;
use App\Services\Api\Athlete\Team\UpdateTeamService;
use App\Services\Api\Athlete\Team\ViewTeamService;
use Illuminate\Http\JsonResponse;

class TeamController extends BaseController
{
    public function index(
        IndexTeamService $indexTeamService
    ): JsonResponse {
        $teams = $indexTeamService->run();

        return $this->successResponse(
            TeamResource::collection($teams),
            'Times listados com sucesso.'
        );
    }

    public function show(
        ViewTeamService $viewTeamService
    ): JsonResponse {
        $team = $viewTeamService->run();

        return $this->successResponse(
            new TeamResource($team),
            'Time encontrado com sucesso.'
        );
    }

    public function store(
        StoreTeamRequest $storeTeamRequest,
        StoreTeamService $storeTeamService
    ): JsonResponse {
        $data = $storeTeamRequest->validated();
        $team = $storeTeamService->run($data);

        return $this->successResponse(
            new TeamResource($team),
            'Time criado com sucesso.'
        );
    }

    public function update(
        Team $team,
        UpdateTeamRequest $updateTeamRequest,
        UpdateTeamService $updateTeamService
    ): JsonResponse {
        $data = $updateTeamRequest->validated();
        $updatedTeam = $updateTeamService->run($team, $data);

        return $this->successResponse(
            new TeamResource($updatedTeam),
            'Time atualizado com sucesso.'
        );
    }

    public function destroy(
        Team $team,
        DeleteTeamService $deleteTeamService
    ): JsonResponse {
        $deleteTeamService->run($team);

        return $this->successResponse(
            null,
            'Time deletado com sucesso.'
        );
    }

    public function updateImage(
        Team $team,
        UpdateTeamImageService $updateTeamImageService
    ): JsonResponse {
        $result = $updateTeamImageService->run($team, request()->file('image'));

        return $this->successResponse(
            $result,
            'Imagem do time atualizada com sucesso.'
        );
    }

    public function statistics(
        Team $team,
        TeamStatisticsService $teamStatisticsService
    ): JsonResponse {
        $stats = $teamStatisticsService->run($team);

        return $this->successResponse(
            new TeamStatisticsResource($stats),
            'Estatísticas do time obtidas com sucesso.'
        );
    }

    public function departures(
        Team $team,
        TeamDeparturesService $teamDeparturesService
    ): JsonResponse {
        $departures = $teamDeparturesService->run($team);

        return $this->successResponse(
            TeamDeparturesResource::collection($departures),
            'Partidas do time obtidas com sucesso.'
        );
    }
}
