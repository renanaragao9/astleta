<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Services\Auth\ForgotPasswordService;
use App\Services\Auth\ResetPasswordService;
use Illuminate\Http\JsonResponse;

class PasswordResetController extends BaseController
{
    public function __construct(
        protected ResetPasswordService $resetPasswordService,
    ) {}

    public function forgotPassword(
        ForgotPasswordRequest $forgotPasswordRequest,
        ForgotPasswordService $forgotPasswordService
    ): JsonResponse {
        $validated = $forgotPasswordRequest->validated();
        $response = $forgotPasswordService->run($validated);

        if ($response['status'] === 'error') {
            return $this->errorResponse([], $response['message']);
        }

        return $this->successResponse($response['data'], $response['message']);
    }

    public function resetPassword(
        ResetPasswordRequest $resetPasswordRequest,
        ResetPasswordService $resetPasswordService
    ): JsonResponse {
        $data = $resetPasswordRequest->validated();
        $response = $resetPasswordService->run($data);

        if ($response['status'] === 'error') {
            return $this->errorResponse([], $response['message']);
        }

        return $this->successResponse($response['data'], $response['message']);
    }
}
