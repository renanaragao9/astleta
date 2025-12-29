import api from '@/config/api';
import type { BookingStatistic, BookingStatisticPayload, StatisticOption } from '@/types/athlete/booking/bookingStatistic';

export class BookingStatisticService {
    private static readonly BASE_URL = '/athlete/bookings';

    static async getStatistics(bookingId: number): Promise<BookingStatistic[]> {
        const response = await api.get(`${this.BASE_URL}/${bookingId}/statistics`);
        return response.data.data;
    }

    static async createStatistic(bookingId: number, payload: BookingStatisticPayload): Promise<BookingStatistic> {
        const response = await api.post(`${this.BASE_URL}/${bookingId}/statistics`, payload);
        return response.data.data;
    }

    static async updateStatistic(bookingId: number, statisticId: number, payload: BookingStatisticPayload): Promise<BookingStatistic> {
        const response = await api.put(`${this.BASE_URL}/${bookingId}/statistics/${statisticId}`, payload);
        return response.data.data;
    }

    static async deleteStatistic(bookingId: number, statisticId: number): Promise<void> {
        await api.delete(`${this.BASE_URL}/${bookingId}/statistics/${statisticId}`);
    }

    static async getAvailableStatistics(): Promise<StatisticOption[]> {
        const response = await api.get('/athlete/statistics');
        return response.data.data;
    }

    static async getStatisticsBySport(sportId: number): Promise<StatisticOption[]> {
        const response = await api.get(`/athlete/statistics/sport/${sportId}`);
        return response.data.data;
    }
}
