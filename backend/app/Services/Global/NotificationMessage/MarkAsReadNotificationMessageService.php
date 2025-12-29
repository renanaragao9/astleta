<?php

namespace App\Services\Global\NotificationMessage;

use App\Models\NotificationMessage;

class MarkAsReadNotificationMessageService
{
    public function run(NotificationMessage $notificationMessage): NotificationMessage
    {
        $notificationMessage->update(['read_at' => now()]);

        return $notificationMessage;
    }
}
