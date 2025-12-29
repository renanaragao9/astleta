export interface TournamentTeamFilter {
    search?: string;
    tournamentId?: number;
    teamId?: number;
    sort?: string;
    direction?: 'asc' | 'desc';
    perPage?: number;
    page?: number;
}
