<?php

namespace App\Services\Global\NotificationMessage;

use App\Models\NotificationMessage;

class DestroyNotificationMessageService
{
    public function run(NotificationMessage $notificationMessage): void
    {
        $notificationMessage->delete();
    }
}
