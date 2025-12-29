import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import type { FieldSize } from '@/types/company/select/fieldSize';
import { FieldSizeService } from '@/services/company/select/FieldSizeService';
import type { SelectOption } from '@/types/global/SelectOption';
import { getErrorMessage } from '@/utils/errorUtils';

export const useFieldSizeStore = defineStore('fieldSize', () => {
    const fieldSizes = ref<FieldSize[]>([]);
    const loading = ref(false);
    const error = ref<string | null>(null);

    const fieldSizeOptions = computed<SelectOption[]>(() =>
        fieldSizes.value.map((size) => ({
            label: size.name,
            value: size.id
        }))
    );

    async function fetchFieldSizes() {
        if (fieldSizes.value.length > 0) return;

        loading.value = true;
        error.value = null;

        try {
            fieldSizes.value = await FieldSizeService.getFieldSizes();
        } catch (err) {
            error.value = getErrorMessage(err, 'Erro ao carregar tamanhos de campo');
            fieldSizes.value = [];
        } finally {
            loading.value = false;
        }
    }

    function clearError() {
        error.value = null;
    }

    function clearCache() {
        fieldSizes.value = [];
    }

    return {
        // State
        fieldSizes,
        loading,
        error,

        // Getters
        fieldSizeOptions,

        // Actions
        fetchFieldSizes,
        clearError,
        clearCache
    };
});
