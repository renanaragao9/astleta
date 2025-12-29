import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import type { BookingStatistic, BookingStatisticPayload } from '@/types/athlete/booking/bookingStatistic';
import { BookingStatisticService } from '@/services/athlete/booking/bookingStatisticService';
import { errorMessageHttpResquestUtils } from '@/utils/errorMessageHttpResquestUtils';

export const useBookingStatisticStore = defineStore('athlete-booking-statistic', () => {
    const statistics = ref<BookingStatistic[]>([]);
    const statistic = ref<BookingStatistic | null>(null);
    const loading = ref(false);
    const error = ref<string | null>(null);

    const hasStatistics = computed(() => statistics.value.length > 0);
    const statisticsByUser = computed(() => {
        const grouped: Record<number, BookingStatistic[]> = {};
        statistics.value.forEach((stat) => {
            if (stat.user?.id) {
                if (!grouped[stat.user.id]) {
                    grouped[stat.user.id] = [];
                }
                grouped[stat.user.id].push(stat);
            }
        });
        return grouped;
    });

    async function getStatistics(bookingId: number, forceReload = false) {
        if (!forceReload && statistics.value.length > 0 && !loading.value) {
            return;
        }

        loading.value = true;
        error.value = null;
        try {
            const response = await BookingStatisticService.getStatistics(bookingId);
            statistics.value = response;
        } catch (err: unknown) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao carregar estatísticas');
            statistics.value = [];
        } finally {
            loading.value = false;
        }
    }

    async function createStatistic(bookingId: number, payload: BookingStatisticPayload) {
        loading.value = true;
        error.value = null;
        try {
            const newStatistic = await BookingStatisticService.createStatistic(bookingId, payload);
            statistics.value.unshift(newStatistic);
            return newStatistic;
        } catch (err: unknown) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao adicionar estatística');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function updateStatistic(bookingId: number, statisticId: number, payload: BookingStatisticPayload) {
        loading.value = true;
        error.value = null;
        try {
            const updatedStatistic = await BookingStatisticService.updateStatistic(bookingId, statisticId, payload);
            const index = statistics.value.findIndex((s) => s.id === statisticId);
            if (index !== -1) {
                statistics.value[index] = updatedStatistic;
            }
            return updatedStatistic;
        } catch (err: unknown) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao atualizar estatística');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function deleteStatistic(bookingId: number, statisticId: number) {
        loading.value = true;
        error.value = null;
        try {
            await BookingStatisticService.deleteStatistic(bookingId, statisticId);
            statistics.value = statistics.value.filter((s) => s.id !== statisticId);
        } catch (err: unknown) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao remover estatística');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    function clearStatistics() {
        statistics.value = [];
        statistic.value = null;
        error.value = null;
    }

    return {
        statistics,
        statistic,
        loading,
        error,
        hasStatistics,
        statisticsByUser,
        getStatistics,
        createStatistic,
        updateStatistic,
        deleteStatistic,
        clearStatistics
    };
});
