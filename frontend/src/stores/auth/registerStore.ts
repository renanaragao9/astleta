import { defineStore } from 'pinia';
import { ref } from 'vue';
import type { RegisterRequest, RegisterResponse, ApiResponse } from '@/types/registerType';
import type { ErrorResponse } from '@/types/global/errorResponse';
import { RegisterService, isAxiosError } from '@/services/auth/registerService';

type ActionResult = { success: boolean; message: string };

export const useRegisterStore = defineStore('register', () => {
    const isLoading = ref(false);
    const error = ref<ErrorResponse | null>(null);
    const registeredUser = ref<RegisterResponse | null>(null);

    const register = async (registerData: RegisterRequest): Promise<ActionResult> => {
        isLoading.value = true;
        error.value = null;

        try {
            const response: ApiResponse<RegisterResponse> = await RegisterService.register(registerData);
            registeredUser.value = response.data;

            return { success: true, message: response.message };
        } catch (err: unknown) {
            let message = 'Erro ao registrar usuário';

            if (isAxiosError(err)) {
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

    const resendVerificationCode = async (email: string): Promise<ActionResult> => {
        isLoading.value = true;
        error.value = null;

        try {
            const response = await RegisterService.resendVerificationCode(email);
            return { success: true, message: response.message };
        } catch (err: unknown) {
            let message = 'Erro ao reenviar código de verificação';

            if (isAxiosError(err)) {
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

    const verifyEmail = async (email: string, code: string): Promise<ActionResult> => {
        isLoading.value = true;
        error.value = null;

        try {
            const response = await RegisterService.verifyEmail(email, code);
            return { success: true, message: response.message };
        } catch (err: unknown) {
            let message = 'Erro ao verificar email';

            if (isAxiosError(err)) {
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

    const clearError = (): void => {
        error.value = null;
    };

    const resetState = (): void => {
        isLoading.value = false;
        error.value = null;
        registeredUser.value = null;
    };

    return {
        isLoading,
        error,
        registeredUser,
        register,
        resendVerificationCode,
        verifyEmail,
        clearError,
        resetState
    };
});
