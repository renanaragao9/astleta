<?php

namespace App\Services\Auth;

use App\Events\UserEmailVerified;
use App\Jobs\SendEmailVerificationJob;
use App\Models\EmailVerificationCode;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class EmailVerificationService
{
    public function verifyEmail(array $data): array
    {
        try {
            return DB::transaction(function () use ($data) {
                $user = User::where('email', $data['email'])->first();

                if (! $user) {
                    return [
                        'status' => 'error',
                        'message' => 'Usuário não encontrado.',
                        'data' => [],
                    ];
                }

                if ($user->email_verified_at) {
                    return [
                        'status' => 'error',
                        'message' => 'E-mail já verificado.',
                        'data' => [],
                    ];
                }

                $verificationCode = EmailVerificationCode::where('user_id', $user->id)
                    ->where('code', $data['code'])
                    ->first();

                if (! $verificationCode) {
                    return [
                        'status' => 'error',
                        'message' => 'Código de verificação inválido.',
                        'data' => [],
                    ];
                }

                if ($verificationCode->isExpired()) {
                    return [
                        'status' => 'error',
                        'message' => 'Código de verificação expirado.',
                        'data' => [],
                    ];
                }

                if ($verificationCode->isVerified()) {
                    return [
                        'status' => 'error',
                        'message' => 'Código já utilizado.',
                        'data' => [],
                    ];
                }

                $verificationCode->markAsVerified();

                $user->update(['email_verified_at' => now()]);

                event(new UserEmailVerified($user));

                return [
                    'status' => 'success',
                    'message' => 'E-mail verificado com sucesso!',
                    'data' => [
                        'user' => $user->fresh(['profile']),
                    ],
                ];
            });
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Erro ao verificar e-mail: '.$e->getMessage(),
                'data' => [],
            ];
        }
    }

    public function resendVerificationCode(array $data): array
    {
        try {
            $user = User::where('email', $data['email'])->first();

            if (! $user) {
                return [
                    'status' => 'error',
                    'message' => 'Usuário não encontrado.',
                    'data' => [],
                ];
            }

            if ($user->email_verified_at) {
                return [
                    'status' => 'error',
                    'message' => 'E-mail já verificado.',
                    'data' => [],
                ];
            }

            $verificationCode = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

            EmailVerificationCode::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'code' => $verificationCode,
                    'expires_at' => now()->addMinutes(15),
                    'verified_at' => null,
                ]
            );

            $job = new SendEmailVerificationJob($user, $verificationCode);
            $job->handle();

            return [
                'status' => 'success',
                'message' => 'Código de verificação reenviado com sucesso!',
                'data' => [],
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Erro ao reenviar código: '.$e->getMessage(),
                'data' => [],
            ];
        }
    }
}
