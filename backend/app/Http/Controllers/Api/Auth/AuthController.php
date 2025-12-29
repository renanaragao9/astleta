<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResendVerificationCodeRequest;
use App\Http\Requests\Auth\VerifyEmailRequest;
use App\Http\Resources\Auth\AuthResource;
use App\Services\Auth\EmailVerificationService;
use App\Services\Auth\LoginService;
use App\Services\Auth\LogoutService;
use App\Services\Auth\RegisterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends BaseController
{
    public function me(Request $request): JsonResponse
    {
        return $this->successResponse(
            new AuthResource($request->user()),
            'Usuário autenticado.'
        );
    }

    public function login(
        LoginRequest $loginRequest,
        LoginService $registerService
    ): JsonResponse {
        $data = $loginRequest->validated();
        $response = $registerService->run($data);

        if ($response['status'] === 'error') {
            return $this->errorResponse([], $response['message']);
        }

        return $this->successResponse([
            'token' => $response['data']['token'],
            'me' => new AuthResource($response['data']['user']),
        ], $response['message']);
    }

    public function register(
        RegisterRequest $registerRequest,
        RegisterService $registerService,
    ): JsonResponse {
        $data = $registerRequest->validated();
        $response = $registerService->run($data);

        if ($response['status'] === 'error') {
            return $this->errorResponse([], $response['message']);
        }

        return $this->successResponse($response['data'], $response['message']);
    }

    public function verifyEmail(
        VerifyEmailRequest $request,
        EmailVerificationService $emailVerificationService
    ): JsonResponse {
        $data = $request->validated();
        $response = $emailVerificationService->verifyEmail($data);

        if ($response['status'] === 'error') {
            return $this->errorResponse([], $response['message']);
        }

        return $this->successResponse($response['data'], $response['message']);
    }

    public function resendVerificationCode(
        ResendVerificationCodeRequest $resendVerificationCodeRequest,
        EmailVerificationService $emailVerificationService
    ): JsonResponse {
        $data = $resendVerificationCodeRequest->validated();
        $response = $emailVerificationService->resendVerificationCode($data);

        if ($response['status'] === 'error') {
            return $this->errorResponse([], $response['message']);
        }

        return $this->successResponse($response['data'], $response['message']);
    }

    public function logout(
        Request $request,
        LogoutService $logoutService,
    ): JsonResponse {
        $logoutService->logout($request->user());

        return $this->successResponse(null, 'Logout realizado com sucesso.');
    }

    public function logoutFromAllDevices(
        Request $request,
        LogoutService $logoutService,
    ): JsonResponse {
        $logoutService->logoutFromAllDevices($request->user());

        return $this->successResponse(null, 'Logout realizado em todos os dispositivos.');
    }
}
