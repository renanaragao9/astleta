export interface Booking {
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
    paymentForm?: {
        id: number;
        name: string;
        type: string;
    };
    coupon?: {
        id: number;
        code: string;
        discountAmount: number;
        discountType: string;
    };
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
