<?php

namespace App\Http\Controllers\Api\Company;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Company\Tournament\IndexTournamentRequest;
use App\Http\Requests\Api\Company\Tournament\StoreTournamentRequest;
use App\Http\Requests\Api\Company\Tournament\UpdateTournamentRequest;
use App\Http\Resources\Company\TournamentResource;
use App\Models\Tournament;
use App\Services\Api\Company\Tournament\DeleteTournamentService;
use App\Services\Api\Company\Tournament\IndexTournamentService;
use App\Services\Api\Company\Tournament\StoreTournamentService;
use App\Services\Api\Company\Tournament\UpdateTournamentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TournamentController extends BaseController
{
    public function index(
        IndexTournamentRequest $indexTournamentRequest,
        IndexTournamentService $indexTournamentService,
    ): AnonymousResourceCollection {
        $data = $indexTournamentRequest->validated();
        $tournaments = $indexTournamentService->run($data);

        return TournamentResource::collection($tournaments);
    }

    public function show(Tournament $tournament): JsonResponse
    {
        return $this->successResponse(
            new TournamentResource($tournament),
            'Torneio encontrado com sucesso.'
        );
    }

    public function store(
        StoreTournamentRequest $storeTournamentRequest,
        StoreTournamentService $storeTournamentService
    ): JsonResponse {
        $data = $storeTournamentRequest->validated();
        $tournament = $storeTournamentService->run($data);

        return $this->successResponse(
            new TournamentResource($tournament),
            'Torneio criado com sucesso.'
        );
    }

    public function update(
        UpdateTournamentRequest $updateTournamentRequest,
        UpdateTournamentService $updateTournamentService,
        Tournament $tournament
    ): JsonResponse {
        $data = $updateTournamentRequest->validated();
        $tournament = $updateTournamentService->run($tournament, $data);

        return $this->successResponse(
            new TournamentResource($tournament),
            'Torneio atualizado com sucesso.'
        );
    }

    public function destroy(Tournament $tournament, DeleteTournamentService $deleteTournamentService): JsonResponse
    {
        $deleteTournamentService->run($tournament);

        return $this->successResponse(
            null,
            'Torneio removido com sucesso.'
        );
    }
}