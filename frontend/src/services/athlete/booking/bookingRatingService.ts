import api from '@/config/api';
import type { BookingRating, BookingRatingPayload } from '@/types/athlete/booking/bookingRating';

export class BookingRatingService {
    private static readonly BASE_URL = '/athlete/bookings';

    static async getRatings(bookingId: number): Promise<BookingRating[]> {
        const response = await api.get(`${this.BASE_URL}/${bookingId}/ratings`);
        return response.data.data;
    }

    static async createRating(bookingId: number, payload: BookingRatingPayload): Promise<BookingRating> {
        const response = await api.post(`${this.BASE_URL}/${bookingId}/ratings`, payload);
        return response.data.data;
    }

    static async updateRating(bookingId: number, ratingId: number, payload: BookingRatingPayload): Promise<BookingRating> {
        const response = await api.put(`${this.BASE_URL}/${bookingId}/ratings/${ratingId}`, payload);
        return response.data.data;
    }

    static async deleteRating(bookingId: number, ratingId: number): Promise<void> {
        await api.delete(`${this.BASE_URL}/${bookingId}/ratings/${ratingId}`);
    }
}
