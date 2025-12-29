<?php

namespace App\Services\Global\NotificationMessage;

class MarkAllAsReadNotificationMessageService
{
    public function run(): void
    {
        $user = auth()->user();
        $user->notificationMessages()->whereNull('read_at')->update(['read_at' => now()]);
    }
}
