import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import type { Tournament, TournamentResponse, TournamentPayload } from '@/types/company/tournament';
import type { TournamentFilters } from '@/types/company/filters/tournamentFilter';
import { TournamentService } from '@/services/company/tournamentService';
import { errorMessageHttpResquestUtils } from '@/utils/errorMessageHttpResquestUtils';

export const useTournamentStore = defineStore('tournament', () => {
    const loading = ref(false);
    const error = ref<string | null>(null);
    const selectedTournaments = ref<Tournament[]>([]);
    const tournaments = ref<Tournament[]>([]);
    const tournament = ref<Tournament>({
        id: 0,
        name: '',
        status: '',
        description: null,
        awards: null,
        welcomeEmail: null,
        startDate: null,
        endDate: null,
        maxParticipants: 0,
        isPublic: false,
        created: ''
    });
    const pagination = ref({
        currentPage: 1,
        lastPage: 1,
        perPage: 15,
        total: 0
    });

    const hasTournaments = computed(() => tournaments.value.length > 0);
    const getTournamentById = computed(() => (id: number) => {
        return tournaments.value.find((tournament) => tournament.id === id);
    });

    async function fetchTournaments(filters: Partial<TournamentFilters> = {}) {
        loading.value = true;
        error.value = null;

        try {
            const response: TournamentResponse = await TournamentService.getTournaments(filters);
            tournaments.value = response.data;
            pagination.value = {
                currentPage: response.meta.currentPage,
                lastPage: response.meta.lastPage,
                perPage: response.meta.perPage,
                total: response.meta.total
            };
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao carregar torneios');
            tournaments.value = [];
        } finally {
            loading.value = false;
        }
    }

    async function fetchTournament(id: number) {
        loading.value = true;
        error.value = null;

        try {
            const response = await TournamentService.getTournament(id);
            tournament.value = response.data;
            return response;
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao carregar torneio');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function createTournament(tournamentData: TournamentPayload) {
        loading.value = true;
        error.value = null;

        try {
            const response = await TournamentService.createTournament(tournamentData);
            tournaments.value.unshift(response.data);
            return response;
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao criar torneio');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function updateTournament(id: number, tournamentData: Partial<TournamentPayload>) {
        loading.value = true;
        error.value = null;

        try {
            const response = await TournamentService.updateTournament(id, tournamentData);
            const index = tournaments.value.findIndex((tournament) => tournament.id === id);
            if (index !== -1) {
                tournaments.value[index] = response.data;
            }
            if (tournament.value.id === id) {
                tournament.value = response.data;
            }
            return response;
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao atualizar torneio');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function deleteTournament(id: number) {
        loading.value = true;
        error.value = null;

        try {
            await TournamentService.deleteTournament(id);
            tournaments.value = tournaments.value.filter((tournament) => tournament.id !== id);
            selectedTournaments.value = selectedTournaments.value.filter((tournament) => tournament.id !== id);
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao deletar torneio');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function deleteSelectedTournaments() {
        if (selectedTournaments.value.length === 0) return;

        loading.value = true;
        error.value = null;

        try {
            const deletePromises = selectedTournaments.value.map((tournament) => (tournament.id ? TournamentService.deleteTournament(tournament.id) : Promise.resolve()));

            await Promise.all(deletePromises);

            const selectedIds = selectedTournaments.value.map((tournament) => tournament.id);
            tournaments.value = tournaments.value.filter((tournament) => !selectedIds.includes(tournament.id));
            selectedTournaments.value = [];
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao deletar torneios selecionados');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    function clearSelectedTournaments() {
        selectedTournaments.value = [];
    }

    function clearTournament() {
        tournament.value = {
            id: 0,
            name: '',
            status: '',
            description: null,
            awards: null,
            welcomeEmail: null,
            startDate: null,
            endDate: null,
            maxParticipants: 0,
            isPublic: false,
            created: ''
        };
    }

    function clearError() {
        error.value = null;
    }

    return {
        // State
        tournaments,
        tournament,
        selectedTournaments,
        loading,
        error,
        pagination,

        // Getters
        getTournamentById,
        hasTournaments,

        // Actions
        fetchTournaments,
        fetchTournament,
        createTournament,
        updateTournament,
        deleteTournament,
        deleteSelectedTournaments,
        clearSelectedTournaments,
        clearTournament,
        clearError
    };
});
