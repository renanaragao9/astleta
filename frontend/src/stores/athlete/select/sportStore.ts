import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import type { Sport } from '@/types/athlete/select/Sport';
import { SportService } from '@/services/athlete/select/sportService';
import type { SelectOption } from '@/types/global/SelectOption';
import { errorMessageHttpResquestUtils } from '@/utils/errorMessageHttpResquestUtils';

export const useSportStore = defineStore('sport', () => {
    const sports = ref<Sport[]>([]);
    const loading = ref(false);
    const error = ref<string | null>(null);

    const sportOptions = computed<SelectOption[]>(() =>
        sports.value.map((sport) => ({
            label: sport.name,
            value: sport.id
        }))
    );

    async function fetchSports() {
        if (sports.value.length > 0) return;

        loading.value = true;
        error.value = null;

        try {
            sports.value = await SportService.getSports();
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao carregar esportes');
            sports.value = [];
        } finally {
            loading.value = false;
        }
    }

    function clearError() {
        error.value = null;
    }

    function clearCache() {
        sports.value = [];
    }

    return {
        sports,
        loading,
        error,
        sportOptions,
        fetchSports,
        clearError,
        clearCache
    };
});
