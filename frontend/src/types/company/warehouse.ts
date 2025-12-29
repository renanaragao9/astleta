export interface WarehouseProduct {
    name: string;
    total: number;
    totalSold: number;
    averageCost: number;
}

export interface Warehouse {
    id: number;
    name: string;
    location: string | null;
    created: string;
    totalStockValue: number;
    totalStock: number;
    totalSold: number;
    totalUnavailable: number;
    products: WarehouseProduct[];
}

export interface WarehousePayload {
    name: string;
    location?: string;
}

export interface WarehouseResponse {
    data: Warehouse[];
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
