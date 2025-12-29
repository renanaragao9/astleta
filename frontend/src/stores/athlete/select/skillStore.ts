import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import type { Skill } from '@/types/athlete/select/Skill';
import { SkillService } from '@/services/athlete/select/skillService';
import type { SelectOption } from '@/types/global/SelectOption';
import { errorMessageHttpResquestUtils } from '@/utils/errorMessageHttpResquestUtils';

export const useSkillStore = defineStore('skill', () => {
    const skills = ref<Skill[]>([]);
    const loading = ref(false);
    const error = ref<string | null>(null);

    const skillOptions = computed<SelectOption[]>(() =>
        skills.value.map((skill) => ({
            label: skill.name,
            value: skill.id
        }))
    );

    async function fetchSkills(sportId?: number) {
        if (skills.value.length > 0) return;

        loading.value = true;
        error.value = null;

        try {
            skills.value = await SkillService.getSkills(sportId);
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao carregar habilidades');
            skills.value = [];
        } finally {
            loading.value = false;
        }
    }

    function clearError() {
        error.value = null;
    }

    function clearCache() {
        skills.value = [];
    }

    return {
        skills,
        loading,
        error,
        skillOptions,
        fetchSkills,
        clearError,
        clearCache
    };
});
