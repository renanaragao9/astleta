export interface Booking {
    id: number;
    paymentType: string;
    bookingNumber: string;
    bookingDate: Date | null;
    startTime: string;
    endTime: string;
    durationMinutes: number;
    totalAmount: number;
    createdAt: string;
    notes: string;
    cancellationFeason?: string;
    paymentFormId: number;
    bookingStatus: 'pendente' | 'confirmado' | 'cancelado' | 'concluido';
    isExtraHour: boolean;
    field: {
        id: number;
        name: string;
        pricePerHour: string;
        extraHourPrice: string;
    };
    user: {
        id: number;
        name: string;
        phone?: string;
    };
    paymentForm: {
        id: number;
        name: string;
        type: string;
    };
    coupon: {
        id: number;
        code: string;
        discountAmount: number;
        discountType: string;
    };
}

export interface SimpleBooking {
    id: number;
    bookingDate: string;
    startTime: string;
    endTime: string;
    bookingStatus: string;
    fieldName: string;
    userName: string;
    totalAmount: number;
}

export interface BookingPayload {
    field_id: number;
    booking_date: Date;
    start_time: string;
    end_time: string;
    payment_type: 'online' | 'presencial';
    booking_status?: 'pendente' | 'confirmado' | 'cancelado' | 'concluido';
    payment_form_id: number;
    coupon_id?: number;
    is_extra_hour?: boolean;
    notes?: string;
    discount_amount?: number;
    user_phone?: string;
}

export interface BookingResponse {
    data: Booking[];
    meta: {
        currentPage: number;
        lastPage: number;
        perPage: number;
        total: number;
    };
}

export interface SimpleBookingResponse {
    data: SimpleBooking[];
    meta: {
        currentPage: number;
        lastPage: number;
        perPage: number;
        total: number;
    };
}
