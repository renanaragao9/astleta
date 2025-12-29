export interface Tournament {
    id: number;
    name: string;
    status: string;
    description: string | null;
    awards: string | null;
    welcomeEmail: string | null;
    startDate: Date | null;
    endDate: Date | null;
    maxParticipants: number;
    isPublic: boolean;
    created: string;
}

export interface TournamentPayload {
    name: string;
    status: string;
    description?: string;
    awards?: string;
    welcome_email?: string;
    start_date: string;
    end_date: string;
    max_participants: number;
    is_public: boolean;
}

export interface TournamentResponse {
    data: Tournament[];
    links: {
        first: string | null;
        last: string | null;
        prev: string | null;
        next: string | null;
    };
    meta: {
        currentPage: number;
        from: number;
        lastPage: number;
        links: {
            url: string | null;
            label: string;
            page: number | null;
            active: boolean;
        }[];
        path: string;
        perPage: number;
        to: number;
        total: number;
    };
}
