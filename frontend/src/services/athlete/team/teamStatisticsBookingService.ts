import api from '@/config/api';
import type { TeamStatisticsBooking, CreateTeamStatisticsBookingPayload, UpdateTeamStatisticsBookingPayload, StatisticsTeamOption } from '@/types/athlete/team/teamStatisticsBooking';

export class TeamStatisticsBookingService {
    private static readonly BASE_URL = '/athlete/team-bookings';

    static async getStatistics(teamBookingId: number): Promise<TeamStatisticsBooking[]> {
        const response = await api.get(`${this.BASE_URL}/${teamBookingId}/statistics`);
        return response.data.data;
    }

    static async createStatistic(teamBookingId: number, payload: CreateTeamStatisticsBookingPayload): Promise<TeamStatisticsBooking> {
        const response = await api.post(`${this.BASE_URL}/${teamBookingId}/statistics`, payload);
        return response.data.data;
    }

    static async updateStatistic(teamBookingId: number, statisticId: number, payload: UpdateTeamStatisticsBookingPayload): Promise<TeamStatisticsBooking> {
        const response = await api.put(`${this.BASE_URL}/${teamBookingId}/statistics/${statisticId}`, payload);
        return response.data.data;
    }

    static async deleteStatistic(teamBookingId: number, statisticId: number): Promise<void> {
        await api.delete(`${this.BASE_URL}/${teamBookingId}/statistics/${statisticId}`);
    }

    static async getAvailableStatistics(sportId?: number): Promise<StatisticsTeamOption[]> {
        if (sportId) {
            const response = await api.get(`/athlete/statistics/sport/${sportId}`);
            return response.data.data;
        }
        const response = await api.get('/athlete/statistics');
        return response.data.data;
    }
}
