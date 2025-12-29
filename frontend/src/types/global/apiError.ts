export interface ApiError {
    response?: {
        data?: {
            message?: string;
            messages?: string[];
            errors?: Record<string, string[]>;
        };
    };
}
