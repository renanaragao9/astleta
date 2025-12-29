export interface SendBookingData {
    booking_id: number;
    send_method: 'email' | 'system';
    email?: string;
    phone?: string;
}
