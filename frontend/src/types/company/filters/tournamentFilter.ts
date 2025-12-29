export interface TournamentFilters {
    search?: string;
    status?: string;
    isPublic?: boolean;
    sort?: 'id' | 'name' | 'status' | 'start_date' | 'end_date' | 'max_participants' | 'is_public' | string;
    direction: 'asc' | 'desc';
    perPage: number;
    page: number;
}
