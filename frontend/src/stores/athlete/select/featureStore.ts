import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import type { Feature } from '@/types/athlete/select/Feature';
import { FeatureService } from '@/services/athlete/select/featureService';
import type { SelectOption } from '@/types/global/SelectOption';
import { errorMessageHttpResquestUtils } from '@/utils/errorMessageHttpResquestUtils';

export const useFeatureStore = defineStore('feature', () => {
    const features = ref<Feature[]>([]);
    const loading = ref(false);
    const error = ref<string | null>(null);

    const featureOptions = computed<SelectOption[]>(() =>
        features.value.map((feature) => ({
            label: feature.name,
            value: feature.id
        }))
    );

    async function fetchFeatures(positionId?: number, forceReload = false) {
        if (features.value.length > 0 && !forceReload) return;

        loading.value = true;
        error.value = null;

        try {
            features.value = await FeatureService.getFeatures(positionId);
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao carregar características');
            features.value = [];
        } finally {
            loading.value = false;
        }
    }

    function clearError() {
        error.value = null;
    }

    function clearCache() {
        features.value = [];
    }

    return {
        features,
        loading,
        error,
        featureOptions,
        fetchFeatures,
        clearError,
        clearCache
    };
});
