<?php

namespace App\Providers;

use App\Events\PasswordResetRequested;
use App\Listeners\SendPasswordResetEmailListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        UserRegistered::class => [
            SendVerificationEmail::class,
        ],
        UserEmailVerified::class => [
            // Aqui você pode adicionar listeners para quando o email for verificado
            // Por exemplo: enviar email de boas-vindas, atribuir benefícios, etc.
        ],
        PasswordResetRequested::class => [
            SendPasswordResetEmailListener::class,
        ],
    ];

    public function boot(): void
    {
        //
    }

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
