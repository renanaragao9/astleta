import { defineStore } from 'pinia';
import { ref } from 'vue';
import type { Team, TeamPayload, TeamResponse, TeamStats, TeamStatsResponse, TeamDeparture, TeamDeparturesResponse } from '@/types/athlete/team/team';
import { TeamService } from '@/services/athlete/team/teamService';
import { errorMessageHttpResquestUtils } from '@/utils/errorMessageHttpResquestUtils';

export const useTeamStore = defineStore('athlete-team', () => {
    const team = ref<Team | null>(null);
    const teamStats = ref<TeamStats | null>(null);
    const teamDepartures = ref<TeamDeparture[]>([]);
    const loading = ref(false);
    const error = ref<string | null>(null);

    async function fetchMyTeam(): Promise<Team> {
        loading.value = true;
        error.value = null;
        try {
            const response: TeamResponse = await TeamService.getMyTeam();
            team.value = response.data;
            return response.data;
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao carregar time');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function fetchTeam(id: number): Promise<Team> {
        loading.value = true;
        error.value = null;
        try {
            const response: TeamResponse = await TeamService.getTeam(id);
            team.value = response.data;
            return response.data;
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao carregar time');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function createTeam(teamData: TeamPayload): Promise<Team> {
        loading.value = true;
        error.value = null;
        try {
            const response: TeamResponse = await TeamService.createTeam(teamData);
            return response.data;
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao criar time');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function updateTeam(id: number, teamData: TeamPayload): Promise<Team> {
        loading.value = true;
        error.value = null;
        try {
            const response: TeamResponse = await TeamService.updateTeam(id, teamData);
            if (team.value && team.value.id === id) {
                team.value = response.data;
            }
            return response.data;
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao atualizar time');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function deleteTeam(id: number): Promise<void> {
        loading.value = true;
        error.value = null;
        try {
            await TeamService.deleteTeam(id);
            if (team.value && team.value.id === id) {
                team.value = null;
            }
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao deletar time');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function updateTeamImage(id: number, image: File): Promise<void> {
        loading.value = true;
        error.value = null;
        try {
            const response = await TeamService.updateTeamImage(id, image);
            if (team.value && team.value.id === id) {
                team.value.shieldPath = response.data.shield_path;
            }
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao atualizar imagem do time');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function fetchTeamStats(id: number): Promise<TeamStats> {
        loading.value = true;
        error.value = null;
        try {
            const response: TeamStatsResponse = await TeamService.getTeamStats(id);
            teamStats.value = response.data;
            return response.data;
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao carregar estatísticas do time');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function fetchTeamDepartures(id: number): Promise<TeamDeparture[]> {
        loading.value = true;
        error.value = null;
        try {
            const response: TeamDeparturesResponse = await TeamService.getTeamDepartures(id);
            teamDepartures.value = response.data;
            return response.data;
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao carregar partidas do time');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    function clearError() {
        error.value = null;
    }

    function clearTeam() {
        team.value = null;
    }

    return {
        team,
        teamStats,
        teamDepartures,
        loading,
        error,
        fetchMyTeam,
        fetchTeam,
        createTeam,
        updateTeam,
        updateTeamImage,
        fetchTeamStats,
        fetchTeamDepartures,
        deleteTeam,
        clearError,
        clearTeam
    };
});
