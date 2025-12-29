import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import type { TeamStatisticsBooking, CreateTeamStatisticsBookingPayload, UpdateTeamStatisticsBookingPayload } from '@/types/athlete/team/teamStatisticsBooking';
import { TeamStatisticsBookingService } from '@/services/athlete/team/teamStatisticsBookingService';
import { errorMessageHttpResquestUtils } from '@/utils/errorMessageHttpResquestUtils';

export const useTeamStatisticsBookingStore = defineStore('athlete-team-statistics-booking', () => {
    const statistics = ref<TeamStatisticsBooking[]>([]);
    const statistic = ref<TeamStatisticsBooking | null>(null);
    const loading = ref(false);
    const error = ref<string | null>(null);

    const hasStatistics = computed(() => statistics.value.length > 0);
    const statisticsByTeam = computed(() => {
        const grouped: Record<number, TeamStatisticsBooking[]> = {};
        statistics.value.forEach((stat) => {
            if (stat.team?.id) {
                if (!grouped[stat.team.id]) {
                    grouped[stat.team.id] = [];
                }
                grouped[stat.team.id].push(stat);
            }
        });
        return grouped;
    });

    async function getStatistics(teamBookingId: number, forceReload = false) {
        if (!forceReload && statistics.value.length > 0 && !loading.value) {
            return;
        }

        loading.value = true;
        error.value = null;
        try {
            const response = await TeamStatisticsBookingService.getStatistics(teamBookingId);
            statistics.value = response;
        } catch (err: unknown) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao carregar estatísticas de time');
            statistics.value = [];
        } finally {
            loading.value = false;
        }
    }

    async function createStatistic(teamBookingId: number, payload: CreateTeamStatisticsBookingPayload) {
        loading.value = true;
        error.value = null;
        try {
            const newStatistic = await TeamStatisticsBookingService.createStatistic(teamBookingId, payload);
            statistics.value.unshift(newStatistic);
            return newStatistic;
        } catch (err: unknown) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao adicionar estatística de time');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function updateStatistic(teamBookingId: number, statisticId: number, payload: UpdateTeamStatisticsBookingPayload) {
        loading.value = true;
        error.value = null;
        try {
            const updatedStatistic = await TeamStatisticsBookingService.updateStatistic(teamBookingId, statisticId, payload);
            const index = statistics.value.findIndex((s) => s.id === statisticId);
            if (index !== -1) {
                statistics.value[index] = updatedStatistic;
            }
            return updatedStatistic;
        } catch (err: unknown) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao atualizar estatística de time');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function deleteStatistic(teamBookingId: number, statisticId: number) {
        loading.value = true;
        error.value = null;
        try {
            await TeamStatisticsBookingService.deleteStatistic(teamBookingId, statisticId);
            statistics.value = statistics.value.filter((s) => s.id !== statisticId);
        } catch (err: unknown) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao remover estatística de time');
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
        statisticsByTeam,
        getStatistics,
        createStatistic,
        updateStatistic,
        deleteStatistic,
        clearStatistics
    };
});
