export interface Filters {
    search: string;
    sort: string;
    direction: 'asc' | 'desc';
    perPage: number;
    page: number;
    bookingDate?: string;
    bookingStatus?: string;
    fieldId?: number;
    startDate?: string;
    endDate?: string;
    paymentType?: string;
}
