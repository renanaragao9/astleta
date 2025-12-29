import api from '@/config/api';
import type { Booking, BookingResponse } from '@/types/athlete/booking/bookingAthlete';
import type { BookingFilters } from '@/types/athlete/filters/bookingFilters';

export class BookingService {
    private static readonly BASE_URL = '/athlete/bookings';

    static async getBookings(filters: Partial<BookingFilters> = {}): Promise<BookingResponse> {
        const params = new URLSearchParams();

        if (filters.search) params.append('search', filters.search);
        if (filters.sort) params.append('sort', filters.sort);
        if (filters.direction) params.append('direction', filters.direction);
        if (filters.per_page) params.append('per_page', filters.per_page.toString());
        if (filters.page) params.append('page', filters.page.toString());
        if (filters.booking_date) params.append('booking_date', filters.booking_date);
        if (filters.booking_status) params.append('booking_status', filters.booking_status);
        if (filters.field_id) params.append('field_id', filters.field_id.toString());
        if (filters.start_date) params.append('start_date', filters.start_date);
        if (filters.end_date) params.append('end_date', filters.end_date);
        if (filters.payment_type) params.append('payment_type', filters.payment_type);

        const queryString = params.toString();
        const url = queryString ? `${this.BASE_URL}?${queryString}` : this.BASE_URL;

        const response = await api.get(url);
        return response.data;
    }

    static async getBooking(id: number): Promise<{ data: Booking; message: string }> {
        const response = await api.get(`${this.BASE_URL}/${id}`);
        return { data: response.data.data, message: response.data.message };
    }

    static async downloadReceipt(id: number): Promise<Blob> {
        const response = await api.get(`${this.BASE_URL}/${id}/receipt`, {
            responseType: 'blob'
        });
        return response.data;
    }
}
