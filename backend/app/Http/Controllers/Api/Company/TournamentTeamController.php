<?php

namespace App\Http\Controllers\Api\Company;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Company\TournamentTeam\IndexTournamentTeamRequest;
use App\Http\Requests\Api\Company\TournamentTeam\StoreTournamentTeamRequest;
use App\Http\Requests\Api\Company\TournamentTeam\UpdateTournamentTeamRequest;
use App\Http\Resources\Company\TournamentTeamResource;
use App\Models\TournamentTeam;
use App\Services\Api\Company\TournamentTeam\DeleteTournamentTeamService;
use App\Services\Api\Company\TournamentTeam\IndexTournamentTeamService;
use App\Services\Api\Company\TournamentTeam\StoreTournamentTeamService;
use App\Services\Api\Company\TournamentTeam\UpdateTournamentTeamService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TournamentTeamController extends BaseController
{
    public function index(
        IndexTournamentTeamRequest $indexTournamentTeamRequest,
        IndexTournamentTeamService $indexTournamentTeamService,
    ): AnonymousResourceCollection {
        $data = $indexTournamentTeamRequest->validated();
        $tournamentTeams = $indexTournamentTeamService->run($data);

        return TournamentTeamResource::collection($tournamentTeams);
    }

    public function show(TournamentTeam $tournamentTeam): JsonResponse
    {
        return $this->successResponse(
            new TournamentTeamResource($tournamentTeam),
            'Participação do time no torneio encontrada com sucesso.'
        );
    }

    public function store(
        StoreTournamentTeamRequest $storeTournamentTeamRequest,
        StoreTournamentTeamService $storeTournamentTeamService
    ): JsonResponse {
        $data = $storeTournamentTeamRequest->validated();
        $tournamentTeam = $storeTournamentTeamService->run($data);

        return $this->successResponse(
            new TournamentTeamResource($tournamentTeam),
            'Time adicionado ao torneio com sucesso.'
        );
    }

    public function update(
        UpdateTournamentTeamRequest $updateTournamentTeamRequest,
        UpdateTournamentTeamService $updateTournamentTeamService,
        TournamentTeam $tournamentTeam
    ): JsonResponse {
        $data = $updateTournamentTeamRequest->validated();
        $tournamentTeam = $updateTournamentTeamService->run($tournamentTeam, $data);

        return $this->successResponse(
            new TournamentTeamResource($tournamentTeam),
            'Participação do time atualizada com sucesso.'
        );
    }

    public function destroy(TournamentTeam $tournamentTeam, DeleteTournamentTeamService $deleteTournamentTeamService): JsonResponse
    {
        $deleteTournamentTeamService->run($tournamentTeam);

        return $this->successResponse(
            null,
            'Time removido do torneio com sucesso.'
        );
    }
}