export interface PurchaseFilters {
    search?: string;
    supplierId?: number;
    status?: string;
    startPurchaseDate?: string;
    endPurchaseDate?: string;
    sort?: string;
    direction?: 'asc' | 'desc';
    perPage?: number;
    page?: number;
}
