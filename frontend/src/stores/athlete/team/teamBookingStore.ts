import { defineStore } from 'pinia';
import { ref } from 'vue';
import type { TeamBookingData, TeamBookingPayload } from '@/types/athlete/team/teamBooking';
import { TeamBookingService } from '@/services/athlete/team/teamBookingService';
import { errorMessageHttpResquestUtils } from '@/utils/errorMessageHttpResquestUtils';

export const useTeamBookingStore = defineStore('athlete-team-booking', () => {
    const teamBooking = ref<TeamBookingData | null>(null);
    const loading = ref(false);
    const error = ref<string | null>(null);

    async function getTeamBooking(bookingId: number, forceReload = false) {
        if (!forceReload && teamBooking.value && !loading.value) {
            return;
        }

        loading.value = true;
        error.value = null;
        try {
            const response = await TeamBookingService.getTeamBooking(bookingId);
            teamBooking.value = response;
        } catch (err: unknown) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao carregar team booking');
            teamBooking.value = null;
        } finally {
            loading.value = false;
        }
    }

    async function createTeamBooking(bookingId: number, payload: TeamBookingPayload) {
        loading.value = true;
        error.value = null;
        try {
            const response = await TeamBookingService.createTeamBooking(bookingId, payload);
            teamBooking.value = response;
            return response;
        } catch (err: unknown) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao criar team booking');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function updateTeamBooking(bookingId: number, payload: TeamBookingPayload) {
        loading.value = true;
        error.value = null;
        try {
            const response = await TeamBookingService.updateTeamBooking(bookingId, payload);
            teamBooking.value = response;
            return response;
        } catch (err: unknown) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao atualizar team booking');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function deleteTeamBooking(bookingId: number) {
        loading.value = true;
        error.value = null;
        try {
            await TeamBookingService.deleteTeamBooking(bookingId);
            teamBooking.value = null;
        } catch (err: unknown) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao remover team booking');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    function clearTeamBooking() {
        teamBooking.value = null;
        error.value = null;
    }

    return {
        teamBooking,
        loading,
        error,
        getTeamBooking,
        createTeamBooking,
        updateTeamBooking,
        deleteTeamBooking,
        clearTeamBooking
    };
});
