import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import type { PublicField, PublicFieldResponse, PublicFieldFilters } from '@/types/public/field/fieldIndex';
import type { PublicField as PublicFieldDetail } from '@/types/public/field/fieldDetail';
import { PublicFieldService } from '@/services/public/publicFieldService';
import { getErrorMessage } from '@/utils/errorUtils';

export const usePublicFieldStore = defineStore('publicField', () => {
    const fields = ref<PublicField[]>([]);
    const field = ref<PublicFieldDetail | null>(null);
    const loading = ref(false);
    const error = ref<string | null>(null);
    const pagination = ref({
        currentPage: 1,
        lastPage: 1,
        perPage: 12,
        total: 0
    });

    const getFieldById = computed(() => (id: number) => {
        return fields.value.find((field) => field.id === id);
    });

    const hasFields = computed(() => fields.value.length > 0);

    async function fetchFields(filters: Partial<PublicFieldFilters> = {}) {
        loading.value = true;
        error.value = null;

        try {
            const response: PublicFieldResponse = await PublicFieldService.getFields(filters);
            fields.value = response.data;
            pagination.value = {
                currentPage: response.meta.current_page,
                lastPage: response.meta.last_page,
                perPage: response.meta.per_page,
                total: response.meta.total
            };
        } catch (err) {
            error.value = getErrorMessage(err, 'Erro ao carregar arenas');
            fields.value = [];
        } finally {
            loading.value = false;
        }
    }

    async function fetchField(id: number) {
        loading.value = true;
        error.value = null;

        try {
            const response = await PublicFieldService.getField(id);
            field.value = response.data;
            return response;
        } catch (err) {
            error.value = getErrorMessage(err, 'Erro ao carregar arena');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    function clearField() {
        field.value = null;
    }

    function clearError() {
        error.value = null;
    }

    return {
        // State
        fields,
        field,
        loading,
        error,
        pagination,

        // Getters
        getFieldById,
        hasFields,

        // Actions
        fetchFields,
        fetchField,
        clearField,
        clearError
    };
});
