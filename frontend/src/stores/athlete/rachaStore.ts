import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import type { AthleteRacha } from '@/types/athlete/racha';
import type { RachaFilters } from '@/types/athlete/filters/rachaFilters';
import { AthleteRachaService } from '@/services/athlete/rachaService';
import { errorMessageHttpResquestUtils } from '@/utils/errorMessageHttpResquestUtils';

export const useAthleteRachaStore = defineStore('athleteRacha', () => {
    const rachas = ref<AthleteRacha[]>([]);
    const racha = ref<AthleteRacha | null>(null);
    const loading = ref(false);
    const error = ref<string | null>(null);
    const pagination = ref({
        currentPage: 1,
        lastPage: 1,
        perPage: 10,
        total: 0
    });

    const hasRachas = computed(() => rachas.value.length > 0);

    const getRachas = async (filters: Partial<RachaFilters> = {}): Promise<void> => {
        loading.value = true;
        error.value = null;
        try {
            const response = await AthleteRachaService.getRachas(filters);

            rachas.value = response.data;
            pagination.value = {
                currentPage: response.meta.currentPage,
                lastPage: response.meta.lastPage,
                perPage: response.meta.perPage,
                total: response.meta.total
            };
        } catch (err: unknown) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao carregar rachas');
            rachas.value = [];
        } finally {
            loading.value = false;
        }
    };

    const getRacha = async (id: number): Promise<void> => {
        loading.value = true;
        error.value = null;
        try {
            const response = await AthleteRachaService.getRacha(id);
            racha.value = response.data;
        } catch (err: unknown) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao carregar racha');
        } finally {
            loading.value = false;
        }
    };

    const joinRacha = async (rachaNumber: string): Promise<void> => {
        loading.value = true;
        error.value = null;
        try {
            await AthleteRachaService.joinRacha(rachaNumber);
        } catch (err: unknown) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao entrar no racha');
            throw err;
        } finally {
            loading.value = false;
        }
    };

    const clearError = (): void => {
        error.value = null;
    };

    const resetRacha = (): void => {
        racha.value = null;
    };

    return {
        rachas,
        racha,
        loading,
        error,
        pagination,
        hasRachas,
        getRachas,
        getRacha,
        joinRacha,
        clearError,
        resetRacha
    };
});
