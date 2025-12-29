import api from '@/config/api';
import type { Booking, BookingResponse, BookingPayload } from '@/types/company/booking';
import type { SimpleBookingResponse } from '@/types/company/booking';
import type { Filters } from '@/types/company/booking/filters';
import type { AvailabilityResponse } from '@/types/company/booking/availability';
import type { SendBookingData } from '@/types/company/booking/sendBooking';

export class BookingService {
    private static readonly BASE_URL = '/company/bookings';

    private static getHeaders(): Record<string, string> {
        return {
            'Content-Type': 'application/json'
        };
    }

    static async getBookings(filters: Partial<Filters> = {}): Promise<BookingResponse> {
        const params = new URLSearchParams();

        if (filters.search) params.append('search', filters.search);
        if (filters.sort) params.append('sort', filters.sort);
        if (filters.direction) params.append('direction', filters.direction);
        if (filters.perPage) params.append('per_page', filters.perPage.toString());
        if (filters.page) params.append('page', filters.page.toString());
        if (filters.bookingDate) params.append('booking_date', filters.bookingDate);
        if (filters.startDate) params.append('start_date', filters.startDate);
        if (filters.endDate) params.append('end_date', filters.endDate);
        if (filters.bookingStatus) params.append('booking_status', filters.bookingStatus);
        if (filters.paymentType) params.append('payment_type', filters.paymentType);
        if (filters.fieldId) params.append('field_id', filters.fieldId.toString());

        const queryString = params.toString();
        const url = queryString ? `${this.BASE_URL}?${queryString}` : this.BASE_URL;

        const response = await api.get(url);
        return response.data;
    }

    static async getBooking(id: number): Promise<{ data: Booking; message: string }> {
        const response = await api.get(`${this.BASE_URL}/${id}`);
        return { data: response.data.data, message: response.data.message };
    }

    static async createBooking(bookingData: BookingPayload): Promise<{ data: Booking; message: string }> {
        const payload = {
            ...bookingData,
            booking_date: bookingData.booking_date.toISOString().split('T')[0]
        };
        const response = await api.post(this.BASE_URL, payload, {
            headers: this.getHeaders()
        });
        return { data: response.data.data, message: response.data.message };
    }

    static async updateBookingStatus(id: number, status: string, cancellationReason?: string): Promise<{ data: Booking; message: string }> {
        const payload: { bookingStatus: string; cancellation_reason?: string } = { bookingStatus: status };
        if (cancellationReason) {
            payload.cancellation_reason = cancellationReason;
        }
        const response = await api.patch(`${this.BASE_URL}/${id}/status`, payload, {
            headers: this.getHeaders()
        });
        return { data: response.data.data, message: response.data.message };
    }

    static async getAvailability(
        fieldId: number,
        date: Date
    ): Promise<{
        data: AvailabilityResponse;
        message: string;
    }> {
        const dateStr = date.toISOString().split('T')[0];
        const response = await api.get(`${this.BASE_URL}/availability?field_id=${fieldId}&date=${dateStr}`);
        return response.data;
    }

    static async calculatePrice(calculationData: {
        field_id: number;
        start_time: string;
        end_time: string;
        include_extra_hour?: boolean;
    }): Promise<{ data: { durationMinutes: number; durationHours: number; basePrice: number; extraHourPrice: number; totalPrice: number }; message: string }> {
        const response = await api.post(`${this.BASE_URL}/calculate-price`, calculationData, {
            headers: this.getHeaders()
        });
        return response.data;
    }

    static async sendBooking(sendData: SendBookingData): Promise<{ message: string }> {
        const response = await api.post(`${this.BASE_URL}/send`, sendData, {
            headers: this.getHeaders()
        });
        return { message: response.data.message };
    }

    static async getBookingsByMonth(year: number, month: number): Promise<SimpleBookingResponse> {
        const params = new URLSearchParams();
        params.append('year', year.toString());
        params.append('month', month.toString());

        const url = `${this.BASE_URL}/by-month?${params.toString()}`;
        const response = await api.get(url);
        return response.data;
    }
}
