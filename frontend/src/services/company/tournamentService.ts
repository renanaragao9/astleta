import api from '@/config/api';
import type { TournamentFilters } from '@/types/company/filters/tournamentFilter';
import type { Tournament, TournamentPayload, TournamentResponse } from '@/types/company/tournament';

export class TournamentService {
    private static readonly BASE_URL = '/company/tournaments';

    private static getHeaders(): Record<string, string> {
        return {
            'Content-Type': 'application/json'
        };
    }

    private static preparePayload(tournamentData: TournamentPayload | Partial<TournamentPayload>): TournamentPayload | Partial<TournamentPayload> {
        return tournamentData;
    }

    static async getTournaments(filters: Partial<TournamentFilters> = {}): Promise<TournamentResponse> {
        const params = new URLSearchParams();

        if (filters.search) params.append('search', filters.search);
        if (filters.status) params.append('status', filters.status);
        if (filters.isPublic !== undefined) params.append('is_public', filters.isPublic.toString());
        if (filters.sort) params.append('sort', filters.sort);
        if (filters.direction) params.append('direction', filters.direction);
        if (filters.perPage) params.append('per_page', filters.perPage.toString());
        if (filters.page) params.append('page', filters.page.toString());

        const queryString = params.toString();
        const url = queryString ? `${this.BASE_URL}?${queryString}` : this.BASE_URL;

        const response = await api.get(url);
        return response.data;
    }

    static async getTournament(id: number): Promise<{ data: Tournament; message: string }> {
        const response = await api.get(`${this.BASE_URL}/${id}`);
        return { data: response.data.data, message: response.data.message };
    }

    static async createTournament(tournamentData: TournamentPayload): Promise<{ data: Tournament; message: string }> {
        const payload = this.preparePayload(tournamentData);
        const response = await api.post(this.BASE_URL, payload, {
            headers: this.getHeaders()
        });
        return { data: response.data.data, message: response.data.message };
    }

    static async updateTournament(id: number, tournamentData: Partial<TournamentPayload>): Promise<{ data: Tournament; message: string }> {
        const payload = this.preparePayload(tournamentData);
        const response = await api.put(`${this.BASE_URL}/${id}`, payload, {
            headers: this.getHeaders()
        });
        return { data: response.data.data, message: response.data.message };
    }

    static async deleteTournament(id: number): Promise<{ message: string }> {
        const response = await api.delete(`${this.BASE_URL}/${id}`);
        return { message: response.data.message };
    }
}
