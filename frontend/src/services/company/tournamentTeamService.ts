import api from '@/config/api';
import type { TournamentTeamFilter } from '@/types/company/filters/tournamentTeamFilter';
import type { TournamentTeam, TournamentTeamPayload, TournamentTeamResponse } from '@/types/company/tournamentTeam';

export class TournamentTeamService {
    private static readonly BASE_URL = '/company/tournament-teams';

    private static getHeaders(): Record<string, string> {
        return {
            'Content-Type': 'application/json'
        };
    }

    private static preparePayload(tournamentTeamData: TournamentTeamPayload | Partial<TournamentTeamPayload>): TournamentTeamPayload | Partial<TournamentTeamPayload> {
        return tournamentTeamData;
    }

    static async getTournamentTeams(filters: Partial<TournamentTeamFilter> = {}): Promise<TournamentTeamResponse> {
        const params = new URLSearchParams();

        if (filters.search) params.append('search', filters.search);
        if (filters.tournamentId) params.append('tournament_id', filters.tournamentId.toString());
        if (filters.teamId) params.append('team_id', filters.teamId.toString());
        if (filters.sort) params.append('sort', filters.sort);
        if (filters.direction) params.append('direction', filters.direction);
        if (filters.perPage) params.append('per_page', filters.perPage.toString());
        if (filters.page) params.append('page', filters.page.toString());

        const queryString = params.toString();
        const url = queryString ? `${this.BASE_URL}?${queryString}` : this.BASE_URL;

        const response = await api.get(url);
        return response.data;
    }

    static async getTournamentTeam(id: number): Promise<{ data: TournamentTeam; message: string }> {
        const response = await api.get(`${this.BASE_URL}/${id}`);
        return { data: response.data.data, message: response.data.message };
    }

    static async createTournamentTeam(tournamentTeamData: TournamentTeamPayload): Promise<{ data: TournamentTeam; message: string }> {
        const payload = this.preparePayload(tournamentTeamData);
        const response = await api.post(this.BASE_URL, payload, {
            headers: this.getHeaders()
        });
        return { data: response.data.data, message: response.data.message };
    }

    static async updateTournamentTeam(id: number, tournamentTeamData: Partial<TournamentTeamPayload>): Promise<{ data: TournamentTeam; message: string }> {
        const payload = this.preparePayload(tournamentTeamData);
        const response = await api.put(`${this.BASE_URL}/${id}`, payload, {
            headers: this.getHeaders()
        });
        return { data: response.data.data, message: response.data.message };
    }

    static async deleteTournamentTeam(id: number): Promise<{ message: string }> {
        const response = await api.delete(`${this.BASE_URL}/${id}`);
        return { message: response.data.message };
    }
}
