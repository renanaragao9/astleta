export interface AthleteProfile {
    id: number;
    user: {
        id: number;
        name: string;
        email: string;
        image?: string;
    };
    team?: {
        name: string;
        shieldPath?: string;
    };
    sport?: {
        id: number;
        name: string;
    };
    position?: {
        id: number;
        name: string;
    };
    subposition?: {
        id: number;
        name: string;
    };
    feature?: {
        id: number;
        name: string;
    };
    subfeature?: {
        id: number;
        name: string;
    };
    dominantSide?: string;
    height?: number;
    weight?: number;
    bio?: string;
    statistics?: Record<string, { value: number; icon: string; color: string }>;
    rating?: number;
    created_at?: string;
    updated_at?: string;
}

export interface AthleteProfileResponse {
    data: AthleteProfile[];
    meta: {
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
    };
}

export interface AthleteProfileFilters {
    name?: string;
    page?: number;
    per_page?: number;
}
