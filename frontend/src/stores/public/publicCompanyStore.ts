import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import type { PublicCompany, PublicCompanyField } from '@/types/public/company/companyProfile';
import { PublicCompanyService } from '@/services/public/publicCompanyService';
import { getErrorMessage } from '@/utils/errorUtils';

export const usePublicCompanyStore = defineStore('publicCompany', () => {
    const company = ref<PublicCompany | null>(null);
    const fields = ref<PublicCompanyField[]>([]);
    const loading = ref(false);
    const error = ref<string | null>(null);

    const getFieldById = computed(() => (id: number) => {
        return fields.value.find((field) => field.id === id);
    });

    const hasFields = computed(() => fields.value.length > 0);
    const hasCompany = computed(() => company.value !== null);

    async function fetchCompanyProfile(companyId: number) {
        loading.value = true;
        error.value = null;

        try {
            const response = await PublicCompanyService.getCompanyProfile(companyId);
            company.value = response.data.company;
            fields.value = response.data.fields;
        } catch (err) {
            error.value = getErrorMessage(err, 'Erro ao carregar perfil da empresa');
            company.value = null;
            fields.value = [];
        } finally {
            loading.value = false;
        }
    }

    function clearCompany() {
        company.value = null;
        fields.value = [];
    }

    function clearError() {
        error.value = null;
    }

    return {
        // State
        company,
        fields,
        loading,
        error,

        // Getters
        getFieldById,
        hasFields,
        hasCompany,

        // Actions
        fetchCompanyProfile,
        clearCompany,
        clearError
    };
});
