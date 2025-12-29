export interface Team {
    id: number;
    uuid: string;
    name: string;
    nickname?: string;
    stadiumName?: string;
    primaryColor?: string;
    secondaryColor?: string;
    shieldPath?: string;
    sport?: {
        id: number;
        name: string;
    };
}

export interface TeamBookingData {
    id: number;
    bookingId: number;
    isFriendly: boolean;
    result?: string;
    goalsHome?: number;
    goalsAway?: number;
    homeTeam?: Team;
    awayTeam?: Team;
    sport?: {
        id: number;
        name: string;
    };
    winner?: Team;
    createdAt: string;
    updatedAt: string;
}

export interface TeamBookingPayload {
    home_team_uuid?: string;
    away_team_uuid?: string;
    sport_id?: number | null;
    goals_home?: number;
    goals_away?: number;
}

export interface TeamFormType {
    id?: number;
    home_team_uuid?: string;
    away_team_uuid?: string;
    sport_id?: number | null;
}
