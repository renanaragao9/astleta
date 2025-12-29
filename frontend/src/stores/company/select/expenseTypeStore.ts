import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import type { ExpenseType } from '@/types/company/select/expenseType';
import { ExpenseTypeService } from '@/services/company/select/ExpenseTypeService';
import type { SelectOption } from '@/types/global/SelectOption';
import { getErrorMessage } from '@/utils/errorUtils';

export const useExpenseTypeStore = defineStore('expenseType', () => {
    const expenseTypes = ref<ExpenseType[]>([]);
    const loading = ref(false);
    const error = ref<string | null>(null);

    const expenseTypeOptions = computed<SelectOption[]>(() =>
        expenseTypes.value.map((type) => ({
            label: type.name,
            value: type.id
        }))
    );

    async function fetchExpenseTypes() {
        if (expenseTypes.value.length > 0) return;

        loading.value = true;
        error.value = null;

        try {
            expenseTypes.value = await ExpenseTypeService.getExpenseTypes();
        } catch (err) {
            error.value = getErrorMessage(err, 'Erro ao carregar tipos de despesa');
            expenseTypes.value = [];
        } finally {
            loading.value = false;
        }
    }

    function clearError() {
        error.value = null;
    }

    function clearCache() {
        expenseTypes.value = [];
    }

    return {
        // State
        expenseTypes,
        loading,
        error,

        // Getters
        expenseTypeOptions,

        // Actions
        fetchExpenseTypes,
        clearError,
        clearCache
    };
});
