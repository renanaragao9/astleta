<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { useNotificationStore } from '@/stores/notification/notificationStore';
import { useAuthStore } from '@/stores/auth/loginStore';
import type { Notification } from '@/types/notificationType';
import Popover from 'primevue/popover';
import Badge from 'primevue/badge';
import Button from 'primevue/button';
import Divider from 'primevue/divider';

const notificationSound = new URL('@/assets/notification/start-playing.mp3', import.meta.url).href;

const store = useNotificationStore();
const authStore = useAuthStore();

const audioRef = ref<HTMLAudioElement>();
const intervalRef = ref<ReturnType<typeof setInterval>>();
const userInteracted = ref(false);

onMounted(() => {
    if (authStore.isAuthenticated) {
        store.fetchNotifications();
        intervalRef.value = setInterval(() => {
            store.fetchNotifications();
        }, 10000);
    }
    const handleUserInteraction = () => {
        userInteracted.value = true;
        document.removeEventListener('click', handleUserInteraction);
    };
    document.addEventListener('click', handleUserInteraction);
});

onUnmounted(() => {
    if (intervalRef.value) {
        clearInterval(intervalRef.value);
    }
});

watch(
    () => store.notifications,
    (newVal, oldVal) => {
        const newNotifications = newVal.filter((n) => !oldVal.some((o) => o.id === n.id));
        newNotifications.forEach(() => {
            if (userInteracted.value) {
                audioRef.value?.play();
            }
        });
    }
);

const notificationPopover = ref<InstanceType<typeof Popover>>();

const toggleNotificationMenu = (event: Event) => {
    notificationPopover.value?.toggle(event);
};

const unreadCount = computed(() => store.unreadCount);

const hasUnreadNotifications = computed(() => store.hasUnreadNotifications);

const getNotificationColor = (icon: string) => {
    const colors: Record<string, string> = {
        'pi-envelope': 'text-blue-600',
        'pi-cog': 'text-gray-600',
        'pi-bell': 'text-orange-600',
        'pi-exclamation-triangle': 'text-red-600',
        'pi-check-circle': 'text-green-600',
        success: 'text-green-600',
        warning: 'text-red-600',
        error: 'text-red-600',
        info: 'text-blue-600'
    };
    return colors[icon] || 'text-gray-600';
};

const markAsRead = async (notification: Notification) => {
    await store.markAsRead(notification);
};

const markAllAsRead = async () => {
    await store.markAllAsRead();
};

const removeNotification = async (id: string) => {
    await store.removeNotification(id);
};

const clearAll = async () => {
    await store.clearAll();
};

const formatDate = (date: Date) => {
    const now = new Date();
    const diff = now.getTime() - date.getTime();
    const minutes = Math.floor(diff / 60000);
    const hours = Math.floor(diff / 3600000);
    const days = Math.floor(diff / 86400000);

    if (minutes < 1) return 'Agora';
    if (minutes < 60) return `${minutes}m`;
    if (hours < 24) return `${hours}h`;
    if (days < 7) return `${days}d`;
    return date.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit' });
};
</script>

<template>
    <div v-if="authStore.isAuthenticated" class="relative">
        <button type="button" class="layout-topbar-action notification-bell" @click="toggleNotificationMenu($event)" :class="{ 'layout-topbar-action-highlight': hasUnreadNotifications, 'animate-pulse': hasUnreadNotifications }">
            <i class="pi pi-bell"></i>
            <Badge v-if="unreadCount > 0" :value="unreadCount" severity="danger" class="notification-badge" />
        </button>

        <Popover ref="notificationPopover" class="notification-panel" :style="{ width: '380px', maxHeight: '500px' }" position="top">
            <div class="notification-container">
                <div class="notification-header">
                    <div class="flex items-center justify-between">
                        <h5 class="notification-title">
                            <i class="pi pi-bell mr-2"></i>
                            Notificações
                        </h5>
                        <div class="flex items-center gap-2">
                            <Badge v-if="unreadCount > 0" :value="`${unreadCount} não lidas`" severity="info" />
                        </div>
                    </div>
                </div>

                <Divider class="my-0" />

                <div class="notification-list">
                    <div v-if="store.notifications.length === 0" class="empty-state">
                        <i class="pi pi-inbox text-4xl text-gray-300 mb-3"></i>
                        <p class="text-gray-500 text-sm">Nenhuma notificação</p>
                    </div>

                    <div v-else class="max-h-80 overflow-y-auto">
                        <div v-for="notification in store.notifications" :key="notification.id" class="notification-item" :class="{ unread: notification.readAt === null }" @click="markAsRead(notification)">
                            <div class="notification-content">
                                <div class="notification-icon-wrapper">
                                    <div class="notification-icon" :class="getNotificationColor(notification.data.icon)" style="font-size: 1.5rem; width: 40px; height: 40px">
                                        <i :class="`pi ${notification.data.icon}`" style="font-size: 1.5rem"></i>
                                    </div>
                                </div>

                                <div class="notification-body">
                                    <div class="notification-main">
                                        <div class="notification-actions">
                                            <span class="notification-time">{{ formatDate(new Date(notification.createdAt)) }}</span>
                                            <Button icon="pi pi-times" class="notification-remove" text rounded severity="secondary" @click.stop="removeNotification(notification.id)" />
                                        </div>
                                    </div>
                                    <p class="notification-message">{{ notification.data.message }}</p>
                                </div>
                            </div>

                            <div v-if="notification.readAt === null" class="unread-indicator"></div>
                        </div>
                    </div>
                </div>

                <div v-if="store.notifications.length > 0" class="notification-footer">
                    <Divider class="my-0" />
                    <div class="flex justify-between items-center pt-3">
                        <Button label="Marcar todas como lidas" icon="pi pi-check" text :disabled="unreadCount === 0" @click="markAllAsRead" />
                        <Button label="Limpar todas" icon="pi pi-trash" text severity="secondary" @click="clearAll" />
                    </div>
                </div>
            </div>
        </Popover>
        <audio ref="audioRef" :src="notificationSound" preload="auto" style="display: none"></audio>
    </div>
</template>

<style scoped>
.notification-bell {
    position: relative;
    transition: all 0.3s ease;
}

.notification-bell:hover {
    transform: scale(1.02);
}

.notification-badge {
    position: absolute;
    top: 15%;
    right: 15%;
    transform: translate(50%, -50%);
    min-width: 16px;
    height: 16px;
    font-size: 9px;
    font-weight: 600;
    animation: gentle-pulse 2s infinite;
}

@keyframes gentle-pulse {
    0%,
    100% {
        opacity: 1;
        transform: translate(-50%, -50%) scale(1);
    }
    50% {
        opacity: 0.8;
        transform: translate(-50%, -50%) scale(1.1);
    }
}

.notification-panel {
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    border-radius: 12px;
    border: 1px solid var(--surface-border);
}

.notification-container {
    padding: 0;
}

.notification-header {
    padding: 1rem 1.25rem 0.75rem;
    background: var(--surface-50);
    border-radius: 12px 12px 0 0;
}

.notification-title {
    margin: 0;
    color: var(--text-color);
    font-weight: 600;
    font-size: 1.1rem;
    display: flex;
    align-items: center;
}

.notification-list {
    max-height: 400px;
}

.empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 3rem 1rem;
    text-align: center;
}

.notification-item {
    position: relative;
    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--surface-border);
    cursor: pointer;
    transition: all 0.2s ease;
    background: var(--surface-0);
}

.notification-item:hover {
    background: var(--surface-50);
}

.notification-item:last-child {
    border-bottom: none;
}

.notification-item.unread {
    background: var(--primary-50);
    border-left: 4px solid var(--primary-color);
}

.notification-item.unread:hover {
    background: var(--primary-100);
}

.notification-content {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
}

.notification-icon-wrapper {
    flex-shrink: 0;
    margin-top: 0.125rem;
}

.notification-avatar {
    width: 32px !important;
    height: 32px !important;
    font-size: 0.75rem;
}

.notification-icon {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--surface-100);
    font-size: 0.9rem;
}

.notification-body {
    flex: 1;
    min-width: 0;
}

.notification-main {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 0.5rem;
    margin-bottom: 0.25rem;
}

.notification-item-title {
    margin: 0;
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--text-color);
    line-height: 1.3;
}

.notification-actions {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-shrink: 0;
}

.notification-time {
    font-size: 0.75rem;
    color: var(--text-color-secondary);
    white-space: nowrap;
}

.notification-remove {
    opacity: 0;
    transition: opacity 0.2s ease;
    width: 24px !important;
    height: 24px !important;
}

.notification-item:hover .notification-remove {
    opacity: 1;
}

.notification-message {
    margin: 0;
    font-size: 0.8rem;
    color: var(--text-color-secondary);
    line-height: 1.4;
}

.unread-indicator {
    position: absolute;
    right: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    width: 8px;
    height: 8px;
    background: var(--primary-color);
    border-radius: 50%;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% {
        box-shadow: 0 0 0 0 var(--primary-color);
    }
    70% {
        box-shadow: 0 0 0 8px rgba(var(--primary-color-rgb), 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(var(--primary-color-rgb), 0);
    }
}

.notification-footer {
    padding: 0.75rem 1.25rem 1rem;
    background: var(--surface-50);
    border-radius: 0 0 12px 12px;
}

.text-blue-600 {
    color: #2563eb;
}

.text-gray-600 {
    color: #6b7280;
}

.text-orange-600 {
    color: #ea580c;
}

.text-red-600 {
    color: #dc2626;
}

.text-green-600 {
    color: #16a34a;
}

@media (max-width: 480px) {
    .notification-panel {
        width: 100vw !important;
        max-width: 380px;
    }

    .notification-item {
        padding: 0.75rem 1rem;
    }

    .notification-header {
        padding: 0.75rem 1rem 0.5rem;
    }

    .notification-footer {
        padding: 0.5rem 1rem 0.75rem;
    }
}

.notification-item,
.notification-remove,
.notification-bell {
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.notification-bell:focus,
.notification-item:focus {
    outline: 2px solid var(--primary-color);
    outline-offset: 2px;
}
</style>
