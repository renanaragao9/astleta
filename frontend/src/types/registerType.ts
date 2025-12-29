import type { Me } from './loginType';

export interface RegisterRequest {
    name: string;
    cpf: string;
    email: string;
    phone: string;
    password: string;
    password_confirmation: string;
    date: string;
    gender: string;
}

export interface RegisterResponse {
    user: Me;
    requires_verification: boolean;
}

export interface ApiResponse<T> {
    data: T;
    message: string;
}

export interface ValidationError {
    field: string;
    message: string;
}

export interface RegisterFormData {
    name: string;
    cpf: string;
    email: string;
    phone: string;
    password: string;
    confirmPassword: string;
    birthDate: string;
    gender: string;
}
