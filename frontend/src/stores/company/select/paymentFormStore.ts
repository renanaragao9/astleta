import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import type { PaymentForm } from '@/types/company/select/paymentForm';
import { PaymentFormService } from '@/services/company/select/PaymentFormService';
import type { SelectOption } from '@/types/global/SelectOption';
import { getErrorMessage } from '@/utils/errorUtils';

export const usePaymentFormStore = defineStore('paymentForm', () => {
    const paymentForms = ref<PaymentForm[]>([]);
    const loading = ref(false);
    const error = ref<string | null>(null);

    const paymentFormOptions = computed<SelectOption[]>(() =>
        paymentForms.value.map((form) => ({
            label: form.name,
            value: form.id
        }))
    );

    async function fetchPaymentForms(type?: string) {
        loading.value = true;
        error.value = null;

        try {
            paymentForms.value = await PaymentFormService.getPaymentForms(type);
        } catch (err) {
            error.value = getErrorMessage(err, 'Erro ao carregar formas de pagamento');
            paymentForms.value = [];
        } finally {
            loading.value = false;
        }
    }

    function clearError() {
        error.value = null;
    }

    function clearCache() {
        paymentForms.value = [];
    }

    return {
        paymentForms,
        loading,
        error,

        paymentFormOptions,

        fetchPaymentForms,
        clearError,
        clearCache
    };
});
