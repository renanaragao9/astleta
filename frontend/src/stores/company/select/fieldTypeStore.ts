import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import type { FieldType } from '@/types/company/select/fieldType';
import { FieldTypeService } from '@/services/company/select/FieldTypeService';
import type { SelectOption } from '@/types/global/SelectOption';
import { getErrorMessage } from '@/utils/errorUtils';

export const useFieldTypeStore = defineStore('fieldType', () => {
    const fieldTypes = ref<FieldType[]>([]);
    const loading = ref(false);
    const error = ref<string | null>(null);

    const fieldTypeOptions = computed<SelectOption[]>(() =>
        fieldTypes.value.map((type) => ({
            label: type.name,
            value: type.id
        }))
    );

    async function fetchFieldTypes() {
        if (fieldTypes.value.length > 0) return;

        loading.value = true;
        error.value = null;

        try {
            fieldTypes.value = await FieldTypeService.getFieldTypes();
        } catch (err) {
            error.value = getErrorMessage(err, 'Erro ao carregar tipos de campo');
            fieldTypes.value = [];
        } finally {
            loading.value = false;
        }
    }

    function clearError() {
        error.value = null;
    }

    function clearCache() {
        fieldTypes.value = [];
    }

    return {
        // State
        fieldTypes,
        loading,
        error,

        // Getters
        fieldTypeOptions,

        // Actions
        fetchFieldTypes,
        clearError,
        clearCache
    };
});
