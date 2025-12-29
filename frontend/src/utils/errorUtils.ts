import type { ErrorResponse } from '@/types/global/errorResponse';

export function getErrorMessage(error: unknown, fallback: string): string {
    if (error && typeof error === 'object' && 'response' in error) {
        const err = error as { response?: { data?: ErrorResponse } };
        return err.response?.data?.message || fallback;
    }
    return fallback;
}
