import api from '@/config/api';
import type { TeamPlayersResponse, TeamPlayerResponse, PayloadTeamPlayerData } from '@/types/athlete/team/teamPlayer';

export class TeamPlayerService {
    private static getBaseUrl(teamId: number) {
        return `/athlete/teams/${teamId}/players`;
    }

    static async getTeamPlayers(teamId: number, filters?: Record<string, string | number | boolean>): Promise<TeamPlayersResponse> {
        const params = new URLSearchParams();
        if (filters) {
            Object.entries(filters).forEach(([key, value]) => {
                if (value !== undefined && value !== null) {
                    params.append(key, String(value));
                }
            });
        }
        const url = params.toString() ? `${this.getBaseUrl(teamId)}?${params.toString()}` : this.getBaseUrl(teamId);
        const response = await api.get(url);
        return response.data;
    }

    static async addPlayerToTeam(teamId: number, playerData: PayloadTeamPlayerData): Promise<TeamPlayerResponse> {
        const response = await api.post(this.getBaseUrl(teamId), playerData);
        return response.data;
    }

    static async updateTeamPlayer(teamId: number, playerId: number, playerData: PayloadTeamPlayerData): Promise<TeamPlayerResponse> {
        const response = await api.put(`${this.getBaseUrl(teamId)}/${playerId}`, playerData);
        return response.data;
    }

    static async removePlayerFromTeam(teamId: number, playerId: number): Promise<{ status: string; message: string }> {
        const response = await api.delete(`${this.getBaseUrl(teamId)}/${playerId}`);
        return response.data;
    }

    static async leaveTeam(teamId: number): Promise<{ status: string; message: string }> {
        const response = await api.delete(this.getBaseUrl(teamId));
        return response.data;
    }
}
