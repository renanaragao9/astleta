<?php

namespace App\Listeners;

use App\Events\PasswordResetRequested;
use App\Jobs\SendPasswordResetEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendPasswordResetEmailListener implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(PasswordResetRequested $event): void
    {
        SendPasswordResetEmail::dispatch($event->user, $event->token);
    }
}
