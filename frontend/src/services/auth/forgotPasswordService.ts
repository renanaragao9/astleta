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

export class ForgotPasswordService {
    static async forgotPassword(email: string): Promise<ApiResponse<{ message: string }>> {
        try {
            const { data } = await api.post<ApiResponse<{ message: string }>>('/auth/forgot-password', { email });
            return data;
        } catch (error) {
            if (isAxiosError(error)) {
                const message = (error.response?.data as { message?: string })?.message ?? 'Erro ao enviar solicitação de redefinição de senha';
                throw new AppError(message, error.response?.status, error.response?.data);
            }
            throw error;
        }
    }
}
