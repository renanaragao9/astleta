import axios, { type AxiosError } from 'axios';
import api from '@/config/api';
import type { ApiNotification, ApiResponse } from '@/types/notificationType';

export const isAxiosError = (error: unknown): error is AxiosError => axios.isAxiosError(error);

export class AppError extends Error {
    status?: number;
    data?: unknown;
    constructor(message: string, status?: number, data?: unknown) {
        super(message);
        this.name = 'AppError';
        this.status = status;
        this.data = data;
    }
}

export class NotificationService {
    static async fetchNotifications(): Promise<ApiResponse<ApiNotification[]>> {
        try {
            const { data } = await api.get<ApiResponse<ApiNotification[]>>('/notification-messages');
            return data;
        } catch (error) {
            if (isAxiosError(error)) {
                const message = (error.response?.data as { message?: string })?.message ?? 'Erro ao carregar notificações';
                throw new AppError(message, error.response?.status, error.response?.data);
            }
            throw error;
        }
    }

    static async markAsRead(id: string): Promise<void> {
        try {
            await api.post(`/notification-messages/${id}/read`);
        } catch (error) {
            if (isAxiosError(error)) {
                const message = (error.response?.data as { message?: string })?.message ?? 'Erro ao marcar notificação como lida';
                throw new AppError(message, error.response?.status, error.response?.data);
            }
            throw error;
        }
    }

    static async markAllAsRead(): Promise<void> {
        try {
            await api.post('/notification-messages/mark-all-read');
        } catch (error) {
            if (isAxiosError(error)) {
                const message = (error.response?.data as { message?: string })?.message ?? 'Erro ao marcar todas as notificações como lidas';
                throw new AppError(message, error.response?.status, error.response?.data);
            }
            throw error;
        }
    }

    static async removeNotification(id: string): Promise<void> {
        try {
            await api.delete(`/notification-messages/${id}`);
        } catch (error) {
            if (isAxiosError(error)) {
                const message = (error.response?.data as { message?: string })?.message ?? 'Erro ao remover notificação';
                throw new AppError(message, error.response?.status, error.response?.data);
            }
            throw error;
        }
    }

    static async clearAll(): Promise<void> {
        try {
            await api.post('/notification-messages/delete-all');
        } catch (error) {
            if (isAxiosError(error)) {
                const message = (error.response?.data as { message?: string })?.message ?? 'Erro ao limpar todas as notificações';
                throw new AppError(message, error.response?.status, error.response?.data);
            }
            throw error;
        }
    }
}
