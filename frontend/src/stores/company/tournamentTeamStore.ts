import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import type { TournamentTeam, TournamentTeamResponse, TournamentTeamPayload, TeamBasic } from '@/types/company/tournamentTeam';
import type { Team } from '@/types/athlete/team/team';
import type { TournamentTeamFilter } from '@/types/company/filters/tournamentTeamFilter';
import { TournamentTeamService } from '@/services/company/tournamentTeamService';
import { errorMessageHttpResquestUtils } from '@/utils/errorMessageHttpResquestUtils';

export const useTournamentTeamStore = defineStore('tournamentTeam', () => {
    const loading = ref(false);
    const error = ref<string | null>(null);
    const selectedTournamentTeams = ref<TournamentTeam[]>([]);
    const tournamentTeams = ref<TournamentTeam[]>([]);
    const tournamentTeam = ref<TournamentTeam>({
        id: 0,
        tournamentId: 0,
        teamId: 0,
        points: 0,
        position: null,
        wins: 0,
        draws: 0,
        losses: 0,
        created: '',
        team: {} as TeamBasic
    });
    const pagination = ref({
        currentPage: 1,
        lastPage: 1,
        perPage: 15,
        total: 0
    });

    const hasTournamentTeams = computed(() => tournamentTeams.value.length > 0);
    const getTournamentTeamById = computed(() => (id: number) => {
        return tournamentTeams.value.find((tournamentTeam) => tournamentTeam.id === id);
    });

    async function fetchTournamentTeams(filters: Partial<TournamentTeamFilter> = {}) {
        loading.value = true;
        error.value = null;

        try {
            const response: TournamentTeamResponse = await TournamentTeamService.getTournamentTeams(filters);
            tournamentTeams.value = response.data;
            pagination.value = {
                currentPage: response.meta.currentPage,
                lastPage: response.meta.lastPage,
                perPage: response.meta.perPage,
                total: response.meta.total
            };
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao carregar times do torneio');
            tournamentTeams.value = [];
        } finally {
            loading.value = false;
        }
    }

    async function fetchTournamentTeam(id: number) {
        loading.value = true;
        error.value = null;

        try {
            const response = await TournamentTeamService.getTournamentTeam(id);
            tournamentTeam.value = response.data;
            return response;
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao carregar time do torneio');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function createTournamentTeam(tournamentTeamData: TournamentTeamPayload) {
        loading.value = true;
        error.value = null;

        try {
            const response = await TournamentTeamService.createTournamentTeam(tournamentTeamData);
            tournamentTeams.value.unshift(response.data);
            return response;
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao criar time do torneio');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function updateTournamentTeam(id: number, tournamentTeamData: Partial<TournamentTeamPayload>) {
        loading.value = true;
        error.value = null;

        try {
            const response = await TournamentTeamService.updateTournamentTeam(id, tournamentTeamData);
            const index = tournamentTeams.value.findIndex((tournamentTeam) => tournamentTeam.id === id);
            if (index !== -1) {
                tournamentTeams.value[index] = response.data;
            }
            if (tournamentTeam.value.id === id) {
                tournamentTeam.value = response.data;
            }
            return response;
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao atualizar time do torneio');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function deleteTournamentTeam(id: number) {
        loading.value = true;
        error.value = null;

        try {
            await TournamentTeamService.deleteTournamentTeam(id);
            tournamentTeams.value = tournamentTeams.value.filter((tournamentTeam) => tournamentTeam.id !== id);
            selectedTournamentTeams.value = selectedTournamentTeams.value.filter((tournamentTeam) => tournamentTeam.id !== id);
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao deletar time do torneio');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function deleteSelectedTournamentTeams() {
        if (selectedTournamentTeams.value.length === 0) return;

        loading.value = true;
        error.value = null;

        try {
            const deletePromises = selectedTournamentTeams.value.map((tournamentTeam) => (tournamentTeam.id ? TournamentTeamService.deleteTournamentTeam(tournamentTeam.id) : Promise.resolve()));

            await Promise.all(deletePromises);

            const selectedIds = selectedTournamentTeams.value.map((tournamentTeam) => tournamentTeam.id);
            tournamentTeams.value = tournamentTeams.value.filter((tournamentTeam) => !selectedIds.includes(tournamentTeam.id));
            selectedTournamentTeams.value = [];
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao deletar times do torneio selecionados');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    function clearSelectedTournamentTeams() {
        selectedTournamentTeams.value = [];
    }

    function clearTournamentTeam() {
        tournamentTeam.value = {
            id: 0,
            tournamentId: 0,
            teamId: 0,
            points: 0,
            position: null,
            wins: 0,
            draws: 0,
            losses: 0,
            created: '',
            team: {} as TeamBasic
        };
    }

    function clearError() {
        error.value = null;
    }

    return {
        // State
        tournamentTeams,
        tournamentTeam,
        selectedTournamentTeams,
        loading,
        error,
        pagination,

        // Getters
        getTournamentTeamById,
        hasTournamentTeams,

        // Actions
        fetchTournamentTeams,
        fetchTournamentTeam,
        createTournamentTeam,
        updateTournamentTeam,
        deleteTournamentTeam,
        deleteSelectedTournamentTeams,
        clearSelectedTournamentTeams,
        clearTournamentTeam,
        clearError
    };
});
