import { defineStore } from 'pinia';
import { ref } from 'vue';
import type { PreCompaniesRegistrationRequest, PreCompaniesRegistrationResponse, ApiResponse } from '@/types/public/preCompaniesRegistrationType';
import type { ErrorResponse } from '@/types/global/errorResponse';
import { PreCompaniesRegistrationService, isAxiosError } from '@/services/public/preCompaniesRegistrationService';

type ActionResult = { success: boolean; message: string };

export const usePreCompaniesRegistrationStore = defineStore('preCompaniesRegistration', () => {
    const isLoading = ref(false);
    const error = ref<ErrorResponse | null>(null);
    const registeredCompany = ref<PreCompaniesRegistrationResponse | null>(null);

    const register = async (registrationData: PreCompaniesRegistrationRequest): Promise<ActionResult> => {
        isLoading.value = true;
        error.value = null;

        try {
            const response: ApiResponse<PreCompaniesRegistrationResponse> = await PreCompaniesRegistrationService.register(registrationData);
            registeredCompany.value = response.data;

            return { success: true, message: response.message };
        } catch (err: unknown) {
            let message = 'Erro ao realizar pré-registro';

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

    const clearError = () => {
        error.value = null;
    };

    const clearData = () => {
        registeredCompany.value = null;
        error.value = null;
    };

    return {
        isLoading,
        error,
        registeredCompany,
        register,
        clearError,
        clearData
    };
});
