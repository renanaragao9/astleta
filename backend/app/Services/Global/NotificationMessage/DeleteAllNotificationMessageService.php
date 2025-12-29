<?php

namespace App\Services\Global\NotificationMessage;

class DeleteAllNotificationMessageService
{
    public function run(): void
    {
        $user = auth()->user();
        $user->notificationMessages()->delete();
    }
}
