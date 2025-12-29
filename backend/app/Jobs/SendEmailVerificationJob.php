<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SendEmailVerificationJob implements ShouldQueue
{
    public function __construct(
        public User $user,
        public string $verificationCode
    ) {}

    public function handle(): void
    {
        try {
            $this->user->notify(new VerifyEmailNotification($this->verificationCode));
        } catch (\Exception $e) {
            Log::error('Erro ao enviar email de verificação', [
                'user_id' => $this->user->id,
                'email' => $this->user->email,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
