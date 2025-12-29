import api from '@/config/api';
import type { TeamPayload, TeamResponse, TeamStatsResponse, TeamDeparturesResponse } from '@/types/athlete/team/team';

export class TeamService {
    private static readonly BASE_URL = '/athlete/teams';

    static async getMyTeam(): Promise<TeamResponse> {
        const response = await api.get(`${this.BASE_URL}/me`);
        return response.data;
    }

    static async getTeam(id: number): Promise<TeamResponse> {
        const response = await api.get(`${this.BASE_URL}/${id}`);
        return response.data;
    }

    static async createTeam(teamData: TeamPayload): Promise<TeamResponse> {
        const response = await api.post(this.BASE_URL, teamData);
        return response.data;
    }

    static async updateTeam(id: number, teamData: TeamPayload): Promise<TeamResponse> {
        const response = await api.put(`${this.BASE_URL}/${id}`, teamData);
        return response.data;
    }

    static async deleteTeam(id: number): Promise<{ status: string; message: string }> {
        const response = await api.delete(`${this.BASE_URL}/${id}`);
        return response.data;
    }

    static async updateTeamImage(id: number, image: File): Promise<{ status: string; message: string; data: { shield_path: string } }> {
        const formData = new FormData();
        formData.append('image', image);

        const response = await api.post(`${this.BASE_URL}/${id}/image`, formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });
        return response.data;
    }

    static async getTeamStats(id: number): Promise<TeamStatsResponse> {
        const response = await api.get(`${this.BASE_URL}/${id}/statistics`);
        return response.data;
    }

    static async getTeamDepartures(id: number): Promise<TeamDeparturesResponse> {
        const response = await api.get(`${this.BASE_URL}/${id}/departures`);
        return response.data;
    }
}
