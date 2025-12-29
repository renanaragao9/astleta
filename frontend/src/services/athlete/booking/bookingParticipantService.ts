import api from '@/config/api';
import type { BookingParticipant, BookingParticipantPayload } from '@/types/athlete/booking/bookingParticipant';

export class BookingParticipantService {
    private static readonly BASE_URL = '/athlete/bookings';

    static async getParticipants(bookingId: number): Promise<BookingParticipant[]> {
        const response = await api.get(`${this.BASE_URL}/${bookingId}/participants`);
        return response.data.data;
    }

    static async createParticipant(bookingId: number, payload: BookingParticipantPayload): Promise<BookingParticipant> {
        const response = await api.post(`${this.BASE_URL}/${bookingId}/participants`, payload);
        return response.data.data;
    }

    static async updateParticipant(bookingId: number, participantId: number, payload: BookingParticipantPayload): Promise<BookingParticipant> {
        const response = await api.put(`${this.BASE_URL}/${bookingId}/participants/${participantId}`, payload);
        return response.data.data;
    }

    static async deleteParticipant(bookingId: number, participantId: number): Promise<void> {
        await api.delete(`${this.BASE_URL}/${bookingId}/participants/${participantId}`);
    }
}
