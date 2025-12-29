import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import type { FieldSurface } from '@/types/company/select/fieldSurface';
import { FieldSurfaceService } from '@/services/company/select/FieldSurfaceService';
import type { SelectOption } from '@/types/global/SelectOption';
import { getErrorMessage } from '@/utils/errorUtils';

export const useFieldSurfaceStore = defineStore('fieldSurface', () => {
    const fieldSurfaces = ref<FieldSurface[]>([]);
    const loading = ref(false);
    const error = ref<string | null>(null);

    const fieldSurfaceOptions = computed<SelectOption[]>(() =>
        fieldSurfaces.value.map((surface) => ({
            label: surface.name,
            value: surface.id
        }))
    );

    async function fetchFieldSurfaces() {
        if (fieldSurfaces.value.length > 0) return;

        loading.value = true;
        error.value = null;

        try {
            fieldSurfaces.value = await FieldSurfaceService.getFieldSurfaces();
        } catch (err) {
            error.value = getErrorMessage(err, 'Erro ao carregar superfícies de campo');
            fieldSurfaces.value = [];
        } finally {
            loading.value = false;
        }
    }

    function clearError() {
        error.value = null;
    }

    function clearCache() {
        fieldSurfaces.value = [];
    }

    return {
        // State
        fieldSurfaces,
        loading,
        error,

        // Getters
        fieldSurfaceOptions,

        // Actions
        fetchFieldSurfaces,
        clearError,
        clearCache
    };
});
