export interface BookingParticipant {
    id: number;
    name: string;
    phone?: string;
    amountPaid?: number;
    isPaid: boolean;
    paidAt?: string;
    status: 'pendente' | 'confirmado' | 'cancelado';
    confirmedAt?: string;
    createdAt: string;
    updatedAt: string;
    featureName?: string;
    positionName?: string;
    imagePath?: string;
    user?: {
        id: number;
        name: string;
        email: string;
        phone?: string;
    };
    addedByUser?: {
        id: number;
        name: string;
    };
    booking?: {
        id: number;
        bookingNumber: string;
        bookingDate: string;
        startTime: string;
        endTime: string;
    };
}

export interface BookingParticipantPayload {
    name?: string;
    phone?: string;
    amount_paid?: number;
    is_paid?: boolean;
    status?: 'pendente' | 'confirmado' | 'cancelado';
    user_id?: number;
    user_phone?: string;
}

export interface BookingParticipantResponse {
    status: 'success' | 'error';
    message: string;
    data: BookingParticipant | BookingParticipant[];
    errors?: Record<string, string[]>;
}

export interface BookingParticipantState {
    participants: BookingParticipant[];
    participant: BookingParticipant | null;
    loading: boolean;
    error: string | null;
}
