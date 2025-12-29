import { defineStore } from 'pinia';
import { ref } from 'vue';
import type { FinancialData, FinancialFilters } from '@/types/company/financial';
import { FinancialService } from '@/services/company/financialService';
import { getErrorMessage } from '@/utils/errorUtils';

export const useFinancialStore = defineStore('financial', () => {
    const financials = ref<FinancialData | null>(null);
    const loading = ref(false);
    const error = ref<string | null>(null);

    async function fetchFinancials(filters: Partial<FinancialFilters> = {}) {
        loading.value = true;
        error.value = null;

        try {
            const response = await FinancialService.getFinancials(filters);
            financials.value = response.data;
        } catch (err) {
            error.value = getErrorMessage(err, 'Erro ao carregar dados financeiros');
            financials.value = null;
        } finally {
            loading.value = false;
        }
    }

    function clearFinancials() {
        financials.value = null;
        error.value = null;
    }

    return {
        financials,
        loading,
        error,
        fetchFinancials,
        clearFinancials
    };
});
