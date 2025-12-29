export interface Me {
    uuid: string;
    name: string;
    username: string;
    email: string;
    phone: string;
    cpf: string;
    date: string;
    imagePath: string;
    email_verified_at: string | null;
    profile: {
        name: string;
    };
}

export interface LoginResponse {
    status: string;
    message: string;
    data: Me;
}

export interface LoginRequest {
    email: string;
    password: string;
}

export interface AuthResponse {
    token: string;
    user: Me;
}

export interface ApiResponse<T> {
    data: T;
    message: string;
}
