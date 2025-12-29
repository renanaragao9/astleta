export interface Supplier {
    id: number;
    name: string;
    email: string;
    phone: string;
    address: string;
    created: string;
}

export interface SupplierPayload {
    name: string;
    email?: string;
    phone?: string;
    address?: string;
}

export interface SupplierResponse {
    data: Supplier[];
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
