import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import type { Position } from '@/types/athlete/select/Position';
import { PositionService } from '@/services/athlete/select/positionService';
import type { SelectOption } from '@/types/global/SelectOption';
import { errorMessageHttpResquestUtils } from '@/utils/errorMessageHttpResquestUtils';

export const usePositionStore = defineStore('position', () => {
    const positions = ref<Position[]>([]);
    const loading = ref(false);
    const error = ref<string | null>(null);

    const positionOptions = computed<SelectOption[]>(() =>
        positions.value.map((position) => ({
            label: position.name,
            value: position.id
        }))
    );

    async function fetchPositions(sportId?: number, forceReload = false) {
        if (positions.value.length > 0 && !forceReload) return;

        loading.value = true;
        error.value = null;

        try {
            positions.value = await PositionService.getPositions(sportId);
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao carregar posições');
            positions.value = [];
        } finally {
            loading.value = false;
        }
    }

    function clearError() {
        error.value = null;
    }

    function clearCache() {
        positions.value = [];
    }

    return {
        positions,
        loading,
        error,
        positionOptions,
        fetchPositions,
        clearError,
        clearCache
    };
});
