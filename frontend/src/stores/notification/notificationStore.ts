import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import type { Notification, ApiNotification, NotificationType } from '@/types/notificationType';
import { NotificationService, isAxiosError } from '@/services/notification/notificationService';

type ActionResult = { success: boolean; message: string };

export const useNotificationStore = defineStore('notification', () => {
    const notifications = ref<Notification[]>([]);
    const loading = ref(false);
    const error = ref<string | null>(null);

    const unreadCount = computed(() => {
        return notifications.value.filter((n) => n.readAt === null).length;
    });

    const hasUnreadNotifications = computed(() => unreadCount.value > 0);

    const getNotificationIcon = (type: NotificationType) => {
        const icons = {
            message: 'pi-envelope',
            system: 'pi-cog',
            reminder: 'pi-bell',
            warning: 'pi-exclamation-triangle',
            success: 'pi-check-circle'
        };
        return icons[type] || 'pi-info-circle';
    };

    const transformApiNotification = (n: ApiNotification): Notification => ({
        id: n.id,
        type: n.type,
        data: {
            message: n.data?.message ?? n.message ?? '',
            icon: n.data?.icon ?? getNotificationIcon(n.type as NotificationType),
            actionText: n.data?.actionText ?? n.action_text,
            actionUrl: n.data?.actionUrl ?? n.action_url,
            openInNewTab: n.data?.openInNewTab ?? n.open_in_new_tab,
            component: n.data?.component ?? (n.component || 'default')
        },
        readAt: n.read_at,
        createdAt: n.created_at
    });

    const fetchNotifications = async (): Promise<ActionResult> => {
        loading.value = true;
        error.value = null;
        try {
            const response = await NotificationService.fetchNotifications();
            notifications.value = response.data.map(transformApiNotification);
            return { success: true, message: 'Notificações carregadas com sucesso' };
        } catch (err: unknown) {
            let message = 'Erro ao carregar notificações';
            if (isAxiosError(err)) {
                const responseData = err.response?.data as { message?: string };
                message = responseData?.message ?? message;
            } else if (err instanceof Error) {
                message = err.message;
            }
            error.value = message;
            return { success: false, message };
        } finally {
            loading.value = false;
        }
    };

    const markAsRead = async (notification: Notification): Promise<ActionResult> => {
        try {
            await NotificationService.markAsRead(notification.id);
            notification.readAt = new Date().toISOString();
            return { success: true, message: 'Notificação marcada como lida' };
        } catch (err: unknown) {
            let message = 'Erro ao marcar notificação como lida';
            if (isAxiosError(err)) {
                const responseData = err.response?.data as { message?: string };
                message = responseData?.message ?? message;
            } else if (err instanceof Error) {
                message = err.message;
            }
            return { success: false, message };
        }
    };

    const markAllAsRead = async (): Promise<ActionResult> => {
        try {
            await NotificationService.markAllAsRead();
            notifications.value.forEach((n) => {
                n.readAt = new Date().toISOString();
            });
            return { success: true, message: 'Todas as notificações foram marcadas como lidas' };
        } catch (err: unknown) {
            let message = 'Erro ao marcar todas as notificações como lidas';
            if (isAxiosError(err)) {
                const responseData = err.response?.data as { message?: string };
                message = responseData?.message ?? message;
            } else if (err instanceof Error) {
                message = err.message;
            }
            return { success: false, message };
        }
    };

    const removeNotification = async (id: string): Promise<ActionResult> => {
        try {
            await NotificationService.removeNotification(id);
            const index = notifications.value.findIndex((n) => n.id === id);
            if (index > -1) {
                notifications.value.splice(index, 1);
            }
            return { success: true, message: 'Notificação removida com sucesso' };
        } catch (err: unknown) {
            let message = 'Erro ao remover notificação';
            if (isAxiosError(err)) {
                const responseData = err.response?.data as { message?: string };
                message = responseData?.message ?? message;
            } else if (err instanceof Error) {
                message = err.message;
            }
            return { success: false, message };
        }
    };

    const clearAll = async (): Promise<ActionResult> => {
        try {
            await NotificationService.clearAll();
            notifications.value.splice(0);
            return { success: true, message: 'Todas as notificações foram limpas' };
        } catch (err: unknown) {
            let message = 'Erro ao limpar todas as notificações';
            if (isAxiosError(err)) {
                const responseData = err.response?.data as { message?: string };
                message = responseData?.message ?? message;
            } else if (err instanceof Error) {
                message = err.message;
            }
            return { success: false, message };
        }
    };

    const clearError = (): void => {
        error.value = null;
    };

    return {
        notifications,
        loading,
        error,
        unreadCount,
        hasUnreadNotifications,
        fetchNotifications,
        markAsRead,
        markAllAsRead,
        removeNotification,
        clearAll,
        clearError
    };
});
