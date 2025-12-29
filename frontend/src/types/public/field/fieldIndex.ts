export interface PublicFieldCompany {
    id: number;
    name: string;
    address: string;
}

export interface PublicField {
    id: number;
    name: string;
    description: string | null;
    pricePerHour: string;
    extraHourPrice: string;
    isAllowsExtraHour: boolean;
    imagePath: string;
    fieldType: {
        name: string;
    };
    fieldSurface: {
        name: string;
    };
    fieldSize: {
        name: string;
    };
    company: PublicFieldCompany;
}

export interface PublicFieldFilters {
    search?: string;
    city?: string;
    state?: string;
    district?: string;
    sportType?: string;
    priceMin?: number;
    priceMax?: number;
    sort?: 'name' | 'price' | 'price_low_to_high' | 'price_high_to_low' | 'company_name' | 'city' | 'newest' | 'oldest' | 'created_at' | 'updated_at';
    direction?: 'asc' | 'desc';
    page?: number;
    perPage?: number;
}

export interface PublicFieldResponse {
    data: PublicField[];
    links: {
        first: string | null;
        last: string | null;
        prev: string | null;
        next: string | null;
    };
    meta: {
        current_page: number;
        from: number;
        last_page: number;
        links: Array<{
            url: string | null;
            label: string;
            page: number | null;
            active: boolean;
        }>;
        path: string;
        per_page: number;
        to: number;
        total: number;
    };
}
