import axios, { type AxiosError } from 'axios';
import api from '@/config/api';
import type { PreCompaniesRegistrationRequest, PreCompaniesRegistrationResponse, ApiResponse } from '@/types/public/preCompaniesRegistrationType';

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

export class PreCompaniesRegistrationService {
    static async register(registrationData: PreCompaniesRegistrationRequest): Promise<ApiResponse<PreCompaniesRegistrationResponse>> {
        try {
            const { data } = await api.post<ApiResponse<PreCompaniesRegistrationResponse>>('/pre-companies-registration', registrationData);
            return data;
        } catch (error) {
            if (isAxiosError(error)) {
                const message = (error.response?.data as { message?: string })?.message ?? 'Erro ao realizar pré-registro';
                throw new AppError(message, error.response?.status, error.response?.data);
            }
            throw error;
        }
    }
}
