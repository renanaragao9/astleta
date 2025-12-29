import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import type { FieldItem } from '@/types/company/select/fieldItem';
import { FieldItemService } from '@/services/company/select/FieldItemService';
import type { SelectOption } from '@/types/global/SelectOption';
import { getErrorMessage } from '@/utils/errorUtils';

export const useFieldItemStore = defineStore('fieldItem', () => {
    const fieldItems = ref<FieldItem[]>([]);
    const loading = ref(false);
    const error = ref<string | null>(null);

    const fieldItemOptions = computed<SelectOption[]>(() =>
        fieldItems.value.map((item) => ({
            label: item.name,
            value: item.id
        }))
    );

    async function fetchFieldItems() {
        if (fieldItems.value.length > 0) return;

        loading.value = true;
        error.value = null;

        try {
            const response = await FieldItemService.getFieldItems();
            fieldItems.value = response.data;
        } catch (err) {
            error.value = getErrorMessage(err, 'Erro ao carregar itens de campo');
            fieldItems.value = [];
        } finally {
            loading.value = false;
        }
    }

    function clearError() {
        error.value = null;
    }

    function clearCache() {
        fieldItems.value = [];
    }

    return {
        // State
        fieldItems,
        loading,
        error,

        // Getters
        fieldItemOptions,

        // Actions
        fetchFieldItems,
        clearError,
        clearCache
    };
});
