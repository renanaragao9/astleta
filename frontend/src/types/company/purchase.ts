export interface Purchase {
    id: number;
    invoiceNumber: string;
    purchaseDate: Date | null;
    status: string | null;
    totalAmount: number | null;
    supplierId: number;
    created: string;
    supplier?: {
        id: number;
        name: string;
    };
    products?: Array<{
        name: string;
        total: number;
        average_cost: number;
    }>;
}

export interface PurchasePayload {
    invoice_number: string;
    purchase_date: string;
    total_amount: number;
    supplier_id: number;
    items?: Array<{
        product_id: number;
        warehouse_id: number;
        quantity: number;
    }>;
}

export interface PurchaseResponse {
    data: Purchase[];
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
