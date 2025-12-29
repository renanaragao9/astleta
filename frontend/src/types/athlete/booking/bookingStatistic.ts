export interface BookingStatistic {
    id: number;
    count: number;
    createdAt: string;
    updatedAt: string;
    user?: {
        id: number;
        name: string;
        email: string;
    };
    participant?: {
        id: number;
        name: string;
        user_id?: number;
    };
    statistic?: {
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

export interface BookingStatisticPayload {
    booking_participant_id?: number;
    statistic_id?: number;
    count: number;
}

export interface StatisticOption {
    id: number;
    name: string;
    sport?: {
        id: number;
        name: string;
    };
    position?: {
        id: number;
        name: string;
    };
}
