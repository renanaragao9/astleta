import axios, { type AxiosError } from 'axios';
import api from '@/config/api';
import type { LoginRequest, AuthResponse, ApiResponse, Me } from '@/types/loginType';

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

export const setAuthToken = (token: string): void => {
    api.defaults.headers.common['Authorization'] = `Bearer ${token}`;
};

export const clearAuthToken = (): void => {
    delete api.defaults.headers.common['Authorization'];
};

export class AuthService {
    static async login(credentials: LoginRequest): Promise<ApiResponse<AuthResponse>> {
        try {
            const { data } = await api.post<ApiResponse<AuthResponse>>('/auth/login', credentials);
            return data;
        } catch (error) {
            if (isAxiosError(error)) {
                const message = (error.response?.data as { message?: string })?.message ?? 'Erro ao fazer login';
                throw new AppError(message, error.response?.status, error.response?.data);
            }
            throw error;
        }
    }

    static async logout(): Promise<void> {
        try {
            await api.post('/auth/logout');
        } catch {
            // Silently handle logout errors
        }
    }

    static async getUser(): Promise<ApiResponse<Me>> {
        try {
            const { data } = await api.get<ApiResponse<Me>>('/me');
            return data;
        } catch (error) {
            if (isAxiosError(error)) {
                const message = (error.response?.data as { message?: string })?.message ?? 'Erro ao buscar usuário';
                throw new AppError(message, error.response?.status, error.response?.data);
            }
            throw error;
        }
    }
}
