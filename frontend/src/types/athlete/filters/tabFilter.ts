export interface TabFilters {
    search?: string;
    status?: 'aberto' | 'pago' | 'cancelado';
    customer_name?: string;
    payment_form_id?: number;
    start_created_date?: string;
    end_created_date?: string;
    sort?: string;
    direction?: 'asc' | 'desc';
    perPage?: number;
    page?: number;
}
