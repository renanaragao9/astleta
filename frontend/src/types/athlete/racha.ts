export interface AthleteRacha {
    id: number;
    bookingNumber: string;
    bookingDate: string;
    startTime: string;
    endTime: string;
    bookingStatus: 'pendente' | 'confirmado' | 'cancelado' | 'concluido';
    fieldName: string;
    statistics: Record<
        string,
        {
            value: number;
            icon: string;
            color: string;
        }
    >;
    rating: {
        overall: number;
        technical: number;
        tactical: number;
        physical: number;
        mental: number;
        teamwork: number;
        comment: string;
    } | null;
    participationId: number;
}

export interface AthleteRachaResponse {
    data: AthleteRacha[];
    message: string;
}

export interface AthleteRachaListResponse {
    data: AthleteRacha[];
    meta: {
        currentPage: number;
        lastPage: number;
        perPage: number;
        total: number;
    };
}
