import axios, { type AxiosError } from 'axios';
import api from '@/config/api';
import type { ApiResponse } from '@/types/loginType';

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

export interface ResetPasswordRequest {
    email: string;
    token: string;
    password: string;
    password_confirmation: string;
}

export class ResetPasswordService {
    static async resetPassword(data: ResetPasswordRequest): Promise<ApiResponse<{ message: string }>> {
        try {
            const { data: response } = await api.post<ApiResponse<{ message: string }>>('/auth/reset-password', data);
            return response;
        } catch (error) {
            if (isAxiosError(error)) {
                const message = (error.response?.data as { message?: string })?.message ?? 'Erro ao redefinir senha';
                throw new AppError(message, error.response?.status, error.response?.data);
            }
            throw error;
        }
    }
}
