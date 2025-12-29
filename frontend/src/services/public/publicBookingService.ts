import api from '@/config/api';
import type { PublicBooking, PublicBookingPayload } from '@/types/public/booking/booking';
import type { AvailabilityResponse } from '@/types/public/booking/availability';
import type { PriceDetails } from '@/types/public/booking/priceCalculation';

export class PublicBookingService {
    private static readonly BASE_URL = '/public_booking';

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

    static async calculatePrice(calculationData: { field_id: number; start_time: string; end_time: string; include_extra_hour?: boolean }): Promise<{ data: PriceDetails; message: string }> {
        const response = await api.post(`${this.BASE_URL}/calculate-price`, calculationData, {
            headers: this.getHeaders()
        });
        return response.data;
    }

    static async createBooking(bookingData: PublicBookingPayload): Promise<{ data: PublicBooking; message: string }> {
        const payload = {
            ...bookingData,
            booking_date: bookingData.booking_date.toISOString().split('T')[0]
        };
        const response = await api.post(`${this.BASE_URL}/bookings`, payload, {
            headers: this.getHeaders()
        });
        return { data: response.data.data, message: response.data.message };
    }

    private static getHeaders(): Record<string, string> {
        return {
            'Content-Type': 'application/json'
        };
    }
}
