import type { ApiError } from '@/types/global/apiError';

export function errorMessageHttpResquestUtils(err: unknown, defaultMessage: string): string {
    const axiosError = err as ApiError;
    const errorData = axiosError.response?.data;

    const errorMessages = errorData?.messages || (errorData?.errors ? Object.values(errorData.errors).flat() : []);
    return errorMessages.length > 0 ? errorMessages.join(', ') : errorData?.message || defaultMessage;
}
