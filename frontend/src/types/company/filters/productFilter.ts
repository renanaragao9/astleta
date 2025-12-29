export interface ProductFilters {
    search?: string;
    productTypeId?: number;
    sort?: 'id' | 'name' | 'description' | 'price' | 'is_active' | 'product_type_id' | string;
    direction: 'asc' | 'desc';
    perPage: number;
    page: number;
}
