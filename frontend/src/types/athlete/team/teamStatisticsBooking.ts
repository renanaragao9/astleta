export interface TeamStatisticsBooking {
    id: number;
    count: number;
    createdAt: string;
    updatedAt: string;
    team?: {
        id: number;
        name: string;
    };
    statistic?: {
        id: number;
        name: string;
    };
    teamBooking?: {
        id: number;
        isFriendly: boolean;
    };
    booking?: {
        id: number;
        bookingNumber: string;
        bookingDate: string;
        startTime: string;
        endTime: string;
    };
}

export interface CreateTeamStatisticsBookingPayload {
    team_id: number;
    statistic_id: number;
    count: number;
}

export interface UpdateTeamStatisticsBookingPayload {
    count: number;
}

export interface StatisticsTeamOption {
    id: number;
    name: string;
    icon?: string;
    color?: string;
    sport?: {
        id: number;
        name: string;
    };
}
