<?php

namespace App\Services\Global\NotificationMessage;

use Illuminate\Database\Eloquent\Collection;

class IndexNotificationMessageService
{
    public function run(): Collection
    {
        $user = auth()->user();

        return $user->notificationMessages()
            ->orderBy('created_at', 'desc')
            ->limit(30)
            ->get();
    }
}
