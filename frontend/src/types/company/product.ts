import type { ProductType } from '@/types/company/select/productType';

export interface Product {
    id: number;
    name: string;
    description: string;
    price: number | null;
    productTypeId: number;
    isActive: boolean;
    created: string;
    productType: ProductType;
}

export interface ProductPayload {
    name: string;
    price: number;
    description?: string;
    product_type_id: number;
    is_active: boolean;
}

export interface ProductResponse {
    data: Product[];
    links: {
        first: string | null;
        last: string | null;
        prev: string | null;
        next: string | null;
    };
    meta: {
        currentPage: number;
        from: number;
        lastPage: number;
        links: {
            url: string | null;
            label: string;
            page: number | null;
            active: boolean;
        }[];
        path: string;
        perPage: number;
        to: number;
        total: number;
    };
}
