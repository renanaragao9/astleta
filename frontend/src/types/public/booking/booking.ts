export interface PublicBooking {
    id: number;
    bookingNumber: string;
    bookingDate: string;
    startTime: string;
    endTime: string;
    durationMinutes: string;
    totalAmount: number;
    bookingStatus: 'pendente' | 'confirmado' | 'cancelado' | 'concluido';
    createdAt: string;
    isExtraHour: boolean;
    cancellation_reason?: string;
    field: {
        id: number;
        name: string;
        pricePerHour: string;
        extraHourPrice: string;
    };
    company?: {
        id: number;
        name: string;
        address: string | null;
    };
    user: {
        id: number;
        name: string;
        phone?: string;
    };
    coupon?: {
        id: number;
        code: string;
        discountAmount: number;
        discountType: string;
    };
    paymentForm?: {
        id: number;
        name: string;
        type: string;
    };
}

export interface PublicBookingPayload {
    field_id: number;
    booking_date: Date;
    start_time: string;
    end_time: string;
    payment_type: 'online' | 'presencial';
    booking_status?: 'pendente' | 'confirmado' | 'cancelado' | 'concluido';
    payment_form_id?: number;
    coupon_id?: number;
    is_extra_hour?: boolean;
    notes?: string;
    discount_amount?: number;
}
