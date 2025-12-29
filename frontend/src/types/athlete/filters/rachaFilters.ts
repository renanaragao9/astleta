export interface RachaFilters {
    booking_status?: string;
    start_date?: string;
    end_date?: string;
    search?: string;
    per_page?: number;
    page?: number;
    sort?: string;
    direction?: 'asc' | 'desc';
}
