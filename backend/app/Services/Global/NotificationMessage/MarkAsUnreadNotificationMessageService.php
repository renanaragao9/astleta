<?php

namespace App\Services\Global\NotificationMessage;

use App\Models\NotificationMessage;

class MarkAsUnreadNotificationMessageService
{
    public function run(NotificationMessage $notificationMessage): NotificationMessage
    {
        $notificationMessage->update(['read_at' => null]);

        return $notificationMessage;
    }
}
