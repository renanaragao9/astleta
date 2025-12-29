export interface BookingFilters {
    booking_status?: string;
    booking_date?: string;
    field_id?: number;
    start_date?: string;
    end_date?: string;
    payment_type?: string;
    search?: string;
    per_page?: number;
    page?: number;
    sort?: string;
    direction?: 'asc' | 'desc';
}
