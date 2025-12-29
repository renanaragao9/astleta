export interface TabFilters {
    search?: string;
    status?: 'aberto' | 'pago' | 'cancelado';
    customerName?: string;
    paymentFormId?: number;
    startCreatedDate?: string;
    endCreatedDate?: string;
    sort?: string;
    direction?: 'asc' | 'desc';
    perPage?: number;
    page?: number;
}

export interface TabItemFilters {
    tabId?: number;
    productId?: number;
    sort?: string;
    direction?: 'asc' | 'desc';
    perPage?: number;
    page?: number;
}
