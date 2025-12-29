<?php

namespace App\Http\Controllers\Api\Athlete;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Athlete\Profile\StoreProfileRequest;
use App\Http\Requests\Api\Athlete\Profile\UpdateProfileRequest;
use App\Http\Requests\Api\Athlete\Profile\UpdateUserImageRequest;
use App\Http\Requests\Api\Athlete\Profile\UpdateUserRequest;
use App\Http\Resources\Athlete\AthleteProfileResource;
use App\Services\Api\Athlete\Profile\StoreProfileService;
use App\Services\Api\Athlete\Profile\UpdateProfileService;
use App\Services\Api\Athlete\Profile\UpdateUserImageService;
use App\Services\Api\Athlete\Profile\UpdateUserService;
use App\Services\Api\Athlete\Profile\ViewProfileService;
use Illuminate\Http\JsonResponse;

class AthleteProfileController extends BaseController
{
    public function show(
        ViewProfileService $viewProfileService
    ): JsonResponse {
        $profile = $viewProfileService->run();

        return $this->successResponse(
            new AthleteProfileResource($profile),
            'Perfil encontrado com sucesso.'
        );
    }

    public function store(
        StoreProfileRequest $storeProfileRequest,
        StoreProfileService $storeProfileService
    ): JsonResponse {
        $data = $storeProfileRequest->validated();
        $profile = $storeProfileService->run($data);

        return $this->successResponse(
            new AthleteProfileResource($profile),
            'Perfil criado com sucesso.'
        );
    }

    public function update(
        UpdateProfileRequest $updateProfileRequest,
        UpdateProfileService $updateProfileService
    ): JsonResponse {
        $data = $updateProfileRequest->validated();
        $profile = $updateProfileService->run($data);

        return $this->successResponse(
            new AthleteProfileResource($profile),
            'Perfil atualizado com sucesso.'
        );
    }

    public function updateUser(
        UpdateUserRequest $updateUserRequest,
        UpdateUserService $updateUserService
    ): JsonResponse {
        $data = $updateUserRequest->validated();
        $profile = $updateUserService->run($data);

        return $this->successResponse(
            new AthleteProfileResource($profile),
            'Informações pessoais atualizadas com sucesso.'
        );
    }

    public function updateImage(
        UpdateUserImageRequest $updateUserImageRequest,
        UpdateUserImageService $updateUserImageService
    ): JsonResponse {
        $result = $updateUserImageService->run(
            $updateUserImageRequest->user(),
            $updateUserImageRequest->file('image')
        );

        return $this->successResponse(
            $result['data'],
            $result['message']
        );
    }
}
