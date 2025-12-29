import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import type { Me, LoginRequest, AuthResponse, ApiResponse } from '@/types/loginType';
import type { ErrorResponse } from '@/types/global/errorResponse';
import { AuthService, setAuthToken, clearAuthToken, isAxiosError, AppError } from '@/services/auth/loginService';

type ActionResult = { success: boolean; message: string };

export const useAuthStore = defineStore('auth', () => {
    const user = ref<Me | null>(null);
    const token = ref<string | null>(null);
    const isAuthenticated = computed(() => !!token.value);
    const isLoading = ref(false);
    const error = ref<ErrorResponse | null>(null);

    const login = async (credentials: LoginRequest): Promise<ActionResult> => {
        isLoading.value = true;
        error.value = null;
        try {
            const response: ApiResponse<AuthResponse> = await AuthService.login(credentials);
            token.value = response.data.token;
            user.value = response.data.user;

            if (typeof window !== 'undefined') {
                localStorage.setItem('token', response.data.token);
            }

            setAuthToken(response.data.token);

            return { success: true, message: response.message };
        } catch (err: unknown) {
            let message = 'Erro ao fazer login';
            if (err instanceof AppError) {
                error.value = err.data as ErrorResponse;
                message = err.message;
            } else if (isAxiosError(err)) {
                error.value = err.response?.data as ErrorResponse;
                message = error.value?.message ?? message;
            } else if (err instanceof Error) {
                message = err.message;
                error.value = { message, errors: {} };
            }
            return { success: false, message };
        } finally {
            isLoading.value = false;
        }
    };

    const logout = async (): Promise<void> => {
        isLoading.value = true;
        try {
            await AuthService.logout();
        } finally {
            user.value = null;
            token.value = null;
            error.value = null;
            if (typeof window !== 'undefined') {
                localStorage.removeItem('token');
            }
            clearAuthToken();
            isLoading.value = false;
        }
    };

    const fetchUser = async (): Promise<void> => {
        if (!token.value) return;

        isLoading.value = true;
        try {
            const response: ApiResponse<Me> = await AuthService.getUser();
            user.value = response.data;
        } catch (err: unknown) {
            if (isAxiosError(err)) {
                const status = err.response?.status;
                if (status === 401 || status === 419) {
                    await logout();
                    return;
                }
            }
            error.value = { message: 'Falha ao buscar usuário', errors: {} };
        } finally {
            isLoading.value = false;
        }
    };

    const initializeAuth = async (): Promise<void> => {
        if (typeof window === 'undefined') return;

        const storedToken = localStorage.getItem('token');
        if (storedToken) {
            token.value = storedToken;
            setAuthToken(storedToken);
            await fetchUser();
        }
    };

    const clearError = (): void => {
        error.value = null;
    };

    return {
        user,
        token,
        isAuthenticated,
        isLoading,
        error,
        login,
        logout,
        fetchUser,
        initializeAuth,
        clearError
    };
});
