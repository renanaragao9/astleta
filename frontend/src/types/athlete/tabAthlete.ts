import type { PaymentForm } from '@/types/company/select/paymentForm';
import type { Product } from '@/types/company/product';

interface User {
    id: number;
    name: string;
    email: string;
}

export interface TabItem {
    id: number;
    quantity: number;
    total: number;
    observation?: string;
    tabId: number;
    productId: number;
    created: string;
    product?: Product;
}

export interface Tab {
    id: number;
    code: string;
    customerName: string;
    status: 'aberto' | 'pago' | 'cancelado';
    totalAmount: number;
    openedAt: string;
    closedAt?: string;
    userId: number;
    paymentFormId?: number;
    created: string;
    user?: User;
    paymentForm?: PaymentForm;
    tabItems?: TabItem[];
}

export interface TabResponse {
    data: Tab[];
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
