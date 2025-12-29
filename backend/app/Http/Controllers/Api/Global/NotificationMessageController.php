<?php

namespace App\Http\Controllers\Api\Global;

use App\Http\Controllers\Api\BaseController;
use App\Models\NotificationMessage;
use App\Services\Global\NotificationMessage\DeleteAllNotificationMessageService;
use App\Services\Global\NotificationMessage\DestroyNotificationMessageService;
use App\Services\Global\NotificationMessage\IndexNotificationMessageService;
use App\Services\Global\NotificationMessage\MarkAllAsReadNotificationMessageService;
use App\Services\Global\NotificationMessage\MarkAsReadNotificationMessageService;
use App\Services\Global\NotificationMessage\MarkAsUnreadNotificationMessageService;
use Illuminate\Http\JsonResponse;

class NotificationMessageController extends BaseController
{
    public function index(
        IndexNotificationMessageService $indexNotificationMessageService
    ): JsonResponse {
        $notifications = $indexNotificationMessageService->run();

        return $this->successResponse($notifications, 'Notificações recuperadas com sucesso');
    }

    public function markAsRead(
        NotificationMessage $notificationMessage,
        MarkAsReadNotificationMessageService $markAsReadNotificationMessageService
    ): JsonResponse {
        $notificationMessageData = $markAsReadNotificationMessageService->run($notificationMessage);

        return $this->successResponse($notificationMessageData, 'Notificação marcada como lida');
    }

    public function markAsUnread(
        NotificationMessage $notificationMessage,
        MarkAsUnreadNotificationMessageService $markAsUnreadNotificationMessageService
    ): JsonResponse {
        $notificationMessageData = $markAsUnreadNotificationMessageService->run($notificationMessage);

        return $this->successResponse($notificationMessageData, 'Notificação marcada como não lida');
    }

    public function markAllAsRead(
        MarkAllAsReadNotificationMessageService $markAllAsReadNotificationMessageService
    ): JsonResponse {
        $markAllAsReadNotificationMessageService->run();

        return $this->successResponse([], 'Todas as notificações foram marcadas como lidas');
    }

    public function destroy(
        NotificationMessage $notificationMessage,
        DestroyNotificationMessageService $destroyNotificationMessageService
    ): JsonResponse {
        $destroyNotificationMessageService->run($notificationMessage);

        return $this->successResponse([], 'Notificação apagada');
    }

    public function deleteAll(DeleteAllNotificationMessageService $deleteAllNotificationMessageService): JsonResponse
    {
        $deleteAllNotificationMessageService->run();

        return $this->successResponse([], 'Todas as notificações foram apagadas');
    }
}
