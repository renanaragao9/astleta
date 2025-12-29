<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Jobs\SendEmailVerificationJob;
use App\Models\EmailVerificationCode;
use Illuminate\Support\Facades\Log;

class SendVerificationEmail
{
    public function handle(UserRegistered $event): void
    {
        try {
            $verificationCode = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

            EmailVerificationCode::updateOrCreate(
                ['user_id' => $event->user->id],
                [
                    'code' => $verificationCode,
                    'expires_at' => now()->addMinutes(15),
                ]
            );

            $job = new SendEmailVerificationJob($event->user, $verificationCode);
            $job->handle();
        } catch (\Exception $e) {
            Log::error('Erro ao processar verificação de email', [
                'user_id' => $event->user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
