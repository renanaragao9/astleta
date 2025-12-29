import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import type { TeamType } from '@/types/athlete/select/TeamType';
import { TeamTypeService } from '@/services/athlete/select/teamTypeService';
import type { SelectOption } from '@/types/global/SelectOption';
import { errorMessageHttpResquestUtils } from '@/utils/errorMessageHttpResquestUtils';

export const useTeamTypeStore = defineStore('teamType', () => {
    const teamTypes = ref<TeamType[]>([]);
    const loading = ref(false);
    const error = ref<string | null>(null);

    const teamTypeOptions = computed<SelectOption[]>(() =>
        teamTypes.value.map((teamType) => ({
            label: teamType.name,
            value: teamType.id
        }))
    );

    async function fetchTeamTypes() {
        if (teamTypes.value.length > 0) return;

        loading.value = true;
        error.value = null;

        try {
            teamTypes.value = await TeamTypeService.getTeamTypes();
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao carregar tipos de equipe');
            teamTypes.value = [];
        } finally {
            loading.value = false;
        }
    }

    function clearError() {
        error.value = null;
    }

    function clearCache() {
        teamTypes.value = [];
    }

    return {
        teamTypes,
        loading,
        error,
        teamTypeOptions,
        fetchTeamTypes,
        clearError,
        clearCache
    };
});
