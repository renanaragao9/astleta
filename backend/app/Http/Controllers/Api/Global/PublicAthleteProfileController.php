<?php

namespace App\Http\Controllers\Api\Global;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Global\AthleteProfile\IndexPublicAthleteProfileRequest;
use App\Http\Resources\Global\PublicAthleteProfileResource;
use App\Services\Api\Global\AthleteProfile\IndexPublicAthleteProfileService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PublicAthleteProfileController extends BaseController
{
    public function index(
        IndexPublicAthleteProfileRequest $indexPublicAthleteProfileRequest,
        IndexPublicAthleteProfileService $indexPublicAthleteProfileService
    ): AnonymousResourceCollection {
        $athletes = $indexPublicAthleteProfileService->run($indexPublicAthleteProfileRequest->validated());

        return PublicAthleteProfileResource::collection($athletes);
    }
}
