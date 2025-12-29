export interface TeamBasic {
    id: number;
    name: string;
    shieldPath: string | null;
}

export interface TournamentTeam {
    id: number;
    tournamentId: number;
    teamId: number;
    points: number;
    position: number | null;
    wins: number;
    draws: number;
    losses: number;
    created: string;
    team: TeamBasic;
}

export interface TournamentTeamPayload {
    tournament_id: number;
    team_id: number;
    points?: number;
    position?: number;
    wins?: number;
    draws?: number;
    losses?: number;
}

export interface TournamentTeamResponse {
    data: TournamentTeam[];
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
