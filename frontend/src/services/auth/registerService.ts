import axios, { type AxiosError } from 'axios';
import api from '@/config/api';
import type { RegisterRequest, RegisterResponse, ApiResponse } from '@/types/registerType';

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

export class RegisterService {
    static async register(userData: RegisterRequest): Promise<ApiResponse<RegisterResponse>> {
        try {
            const { data } = await api.post<ApiResponse<RegisterResponse>>('/auth/register', userData);
            return data;
        } catch (error) {
            if (isAxiosError(error)) {
                const message = (error.response?.data as { message?: string })?.message ?? 'Erro ao registrar usuário';
                throw new AppError(message, error.response?.status, error.response?.data);
            }
            throw error;
        }
    }

    static async resendVerificationCode(email: string): Promise<ApiResponse<{ message: string }>> {
        try {
            const { data } = await api.post<ApiResponse<{ message: string }>>('/auth/resend-verification-code', { email });
            return data;
        } catch (error) {
            if (isAxiosError(error)) {
                const message = (error.response?.data as { message?: string })?.message ?? 'Erro ao reenviar código de verificação';
                throw new AppError(message, error.response?.status, error.response?.data);
            }
            throw error;
        }
    }

    static async verifyEmail(email: string, code: string): Promise<ApiResponse<{ message: string }>> {
        try {
            const { data } = await api.post<ApiResponse<{ message: string }>>('/auth/verify-email', { email, code });
            return data;
        } catch (error) {
            if (isAxiosError(error)) {
                const message = (error.response?.data as { message?: string })?.message ?? 'Erro ao verificar email';
                throw new AppError(message, error.response?.status, error.response?.data);
            }
            throw error;
        }
    }
}
