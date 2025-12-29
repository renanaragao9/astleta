export type NotificationType = 'message' | 'system' | 'reminder' | 'warning' | 'success';

export interface ApiNotification {
    id: string;
    type: string;
    message?: string;
    action_text?: string;
    action_url?: string;
    open_in_new_tab?: boolean;
    component?: string;
    read_at: string | null;
    created_at: string;
    data?: {
        message?: string;
        icon?: string;
        actionText?: string;
        actionUrl?: string;
        openInNewTab?: boolean;
        component?: string;
    };
}

export interface Notification {
    id: string;
    type: string;
    data: {
        message: string;
        icon: string;
        actionText?: string;
        actionUrl?: string;
        openInNewTab?: boolean;
        component: string;
    };
    readAt: string | null;
    createdAt: string;
}

export interface ApiResponse<T> {
    data: T;
    message: string;
}
