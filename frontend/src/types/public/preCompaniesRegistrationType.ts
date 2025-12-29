export interface PreCompaniesRegistrationRequest {
    name: string;
    email: string;
    phone: string;
    description?: string;
}

export interface PreCompaniesRegistrationResponse {
    id: number;
    name: string;
    email: string;
    phone: string;
    description?: string;
    created_at: string;
    updated_at: string;
}

export interface ApiResponse<T> {
    data: T;
    status: string;
    message: string;
}
