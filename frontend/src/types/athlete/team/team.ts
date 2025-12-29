export interface Team {
    id: number;
    uuid: string;
    name: string;
    nickname?: string | null;
    stadiumName?: string | null;
    primaryColor?: string | null;
    secondaryColor?: string | null;
    shieldPath?: string | null;
    website?: string | null;
    foundedDate?: string | null;
    description: string;
    welcomeEmail?: string | null;
    maxMembers: number;
    isPublic: boolean;
    createdAt: string;
    updatedAt: string;
    sport: {
        id: number;
        name: string;
    };
    teamType: {
        id: number;
        name: string;
    };
    creator: {
        name: string;
        phone: string | null;
        uuid: string;
    };
}

export interface TeamPayload {
    name: string;
    nickname?: string;
    stadium_name?: string;
    primary_color?: string;
    secondary_color?: string;
    shield_path?: string;
    website?: string;
    founded_date?: string;
    description?: string;
    welcome_email?: string;
    max_members?: number | string;
    is_public?: boolean;
    sport_id: number;
    team_type_id: number;
}

export interface TeamStats {
    wins: number;
    losses: number;
    draws: number;
    goalScored: number;
    goalConceded: number;
    matches: number;
}

export interface TeamDeparture {
    id: number;
    date: string;
    opponent: string;
    goalsScored: number;
    goalsConceded: number;
    result: 'vitória' | 'derrota' | 'empate' | 'pendente';
    isHome: boolean;
}

export interface TeamStatsResponse {
    status: string;
    message: string;
    data: TeamStats;
}

export interface TeamDeparturesResponse {
    status: string;
    message: string;
    data: TeamDeparture[];
}

export interface TeamResponse {
    status: string;
    message: string;
    data: Team;
}
