export interface Filters {
    search: string;
    sort: string;
    direction: 'asc' | 'desc';
    perPage: number;
    page?: number;
}
