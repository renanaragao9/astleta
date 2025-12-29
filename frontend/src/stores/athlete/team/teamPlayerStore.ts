import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import type { TeamPlayer, PayloadTeamPlayerData, TeamPlayersResponse } from '@/types/athlete/team/teamPlayer';
import { TeamPlayerService } from '@/services/athlete/team/teamPlayerService';
import { errorMessageHttpResquestUtils } from '@/utils/errorMessageHttpResquestUtils';

export const useTeamPlayerStore = defineStore('athlete-team-player', () => {
    const teamPlayers = ref<TeamPlayer[]>([]);
    const loading = ref(false);
    const error = ref<string | null>(null);

    const hasPlayers = computed(() => teamPlayers.value.length > 0);
    const activePlayers = computed(() => teamPlayers.value.filter((p) => p.status === 'ativo'));
    const captains = computed(() => teamPlayers.value.filter((p) => p.role === 'capitao'));

    async function fetchTeamPlayers(teamId: number, filters: Record<string, string | number | boolean> = {}): Promise<void> {
        loading.value = true;
        error.value = null;
        try {
            const response: TeamPlayersResponse = await TeamPlayerService.getTeamPlayers(teamId, filters);
            teamPlayers.value = response.data;
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao carregar jogadores do time');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function addPlayerToTeam(teamId: number, playerData: PayloadTeamPlayerData): Promise<TeamPlayer> {
        loading.value = true;
        error.value = null;
        try {
            const response = await TeamPlayerService.addPlayerToTeam(teamId, playerData);
            teamPlayers.value.push(response.data);
            return response.data;
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao adicionar jogador ao time');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function updateTeamPlayer(teamId: number, playerId: number, playerData: PayloadTeamPlayerData): Promise<TeamPlayer> {
        loading.value = true;
        error.value = null;
        try {
            const response = await TeamPlayerService.updateTeamPlayer(teamId, playerId, playerData);
            const index = teamPlayers.value.findIndex((p) => p.id === playerId);
            if (index !== -1) {
                teamPlayers.value[index] = response.data;
            }
            return response.data;
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao atualizar jogador');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function removePlayerFromTeam(teamId: number, playerId: number): Promise<void> {
        loading.value = true;
        error.value = null;
        try {
            await TeamPlayerService.removePlayerFromTeam(teamId, playerId);
            teamPlayers.value = teamPlayers.value.filter((p) => p.id !== playerId);
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao remover jogador do time');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function leaveTeam(teamId: number): Promise<void> {
        loading.value = true;
        error.value = null;
        try {
            await TeamPlayerService.leaveTeam(teamId);
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao sair do time');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    function clearError() {
        error.value = null;
    }

    function clearPlayers() {
        teamPlayers.value = [];
    }

    return {
        teamPlayers,
        loading,
        error,
        hasPlayers,
        activePlayers,
        captains,
        fetchTeamPlayers,
        addPlayerToTeam,
        updateTeamPlayer,
        removePlayerFromTeam,
        leaveTeam,
        clearError,
        clearPlayers
    };
});
