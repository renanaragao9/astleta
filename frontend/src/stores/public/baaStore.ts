import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import type { AthleteProfile, AthleteProfileFilters } from '@/types/public/baa';
import { BaaService } from '@/services/public/BaaService';
import { getErrorMessage } from '@/utils/errorUtils';

export const useBaaStore = defineStore('baa', () => {
    const athletes = ref<AthleteProfile[]>([]);
    const selectedAthlete = ref<AthleteProfile | null>(null);
    const loading = ref(false);
    const error = ref<string | null>(null);
    const currentPage = ref(1);
    const pagination = ref({
        currentPage: 1,
        lastPage: 1,
        perPage: 12,
        total: 0
    });

    const hasAthletes = computed(() => athletes.value.length > 0);

    async function getAthletes(filters: Partial<AthleteProfileFilters> = {}) {
        loading.value = true;
        error.value = null;
        try {
            const apiFilters = { ...filters };
            if (!apiFilters.page) {
                apiFilters.page = currentPage.value;
            }
            if (!apiFilters.per_page) {
                apiFilters.per_page = pagination.value.perPage;
            }
            const response = await BaaService.getAthletes(apiFilters);
            athletes.value = response.data;
            pagination.value = {
                currentPage: response.meta.current_page,
                lastPage: response.meta.last_page,
                perPage: response.meta.per_page,
                total: response.meta.total
            };
            currentPage.value = response.meta.current_page;
        } catch (err: unknown) {
            error.value = getErrorMessage(err, 'Erro ao carregar atletas');
            athletes.value = [];
        } finally {
            loading.value = false;
        }
    }

    async function getAthlete(id: number) {
        loading.value = true;
        error.value = null;
        try {
            const response = await BaaService.getAthlete(id);
            selectedAthlete.value = response.data;
            return response.data;
        } catch (err: unknown) {
            error.value = getErrorMessage(err, 'Erro ao carregar perfil do atleta');
            selectedAthlete.value = null;
            throw err;
        } finally {
            loading.value = false;
        }
    }

    function clearError() {
        error.value = null;
    }

    function clearSelectedAthlete() {
        selectedAthlete.value = null;
    }

    function clearAthletes() {
        athletes.value = [];
        currentPage.value = 1;
        pagination.value = {
            currentPage: 1,
            lastPage: 1,
            perPage: 12,
            total: 0
        };
    }

    function setPage(page: number) {
        if (page >= 1 && page <= pagination.value.lastPage) {
            currentPage.value = page;
            getAthletes();
        }
    }

    function nextPage() {
        if (currentPage.value < pagination.value.lastPage) {
            currentPage.value++;
            getAthletes();
        }
    }

    function prevPage() {
        if (currentPage.value > 1) {
            currentPage.value--;
            getAthletes();
        }
    }

    function goToFirstPage() {
        setPage(1);
    }

    function goToLastPage() {
        setPage(pagination.value.lastPage);
    }

    return {
        athletes,
        selectedAthlete,
        loading,
        error,
        currentPage,
        pagination,
        hasAthletes,
        getAthletes,
        getAthlete,
        clearError,
        clearSelectedAthlete,
        clearAthletes,
        setPage,
        nextPage,
        prevPage,
        goToFirstPage,
        goToLastPage
    };
});
