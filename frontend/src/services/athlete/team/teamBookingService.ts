import api from '@/config/api';
import type { TeamBookingData, TeamBookingPayload, Team } from '@/types/athlete/team/teamBooking';

export class TeamBookingService {
    private static readonly BASE_URL = '/athlete/bookings';

    static async getTeamBooking(bookingId: number): Promise<TeamBookingData> {
        const response = await api.get(`${this.BASE_URL}/${bookingId}/team-booking`);
        return response.data.data;
    }

    static async createTeamBooking(bookingId: number, payload: TeamBookingPayload): Promise<TeamBookingData> {
        const response = await api.post(`${this.BASE_URL}/${bookingId}/team-booking`, payload);
        return response.data.data;
    }

    static async updateTeamBooking(bookingId: number, payload: TeamBookingPayload): Promise<TeamBookingData> {
        const response = await api.put(`${this.BASE_URL}/${bookingId}/team-booking`, payload);
        return response.data.data;
    }

    static async deleteTeamBooking(bookingId: number): Promise<void> {
        await api.delete(`${this.BASE_URL}/${bookingId}/team-booking`);
    }

    static async getAvailableTeams(sportId?: number): Promise<Team[]> {
        if (sportId) {
            const response = await api.get(`/athlete/teams-available?sport_id=${sportId}`);
            return response.data.data;
        }
        const response = await api.get('/athlete/teams-available');
        return response.data.data;
    }
}
