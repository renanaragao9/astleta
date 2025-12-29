import type { PaymentForm } from '@/types/company/select/paymentForm';
import type { Product } from '@/types/company/product';

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
    paymentFormId?: number;
    openedAt: string;
    closedAt?: string;
    companyId: number;
    created: string;
    company?: { id: number; name: string };
    paymentForm?: PaymentForm;
    tabItems?: TabItem[];
}

export interface TabPayload {
    code: string;
    customer_name: string;
    status?: 'aberto' | 'pago' | 'cancelado';
    total_amount?: number;
    opened_at?: string;
    closed_at?: string;
    payment_form_id?: number;
}

export interface TabItemPayload {
    quantity: number;
    total: number;
    observation?: string;
    tab_id: number;
    product_id: number;
}

export interface SendTabDataPayload {
    tab_id: number;
    send_method: 'email' | 'system' | '';
    email: string;
    phone: string;
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

export interface TabItemResponse {
    data: TabItem[];
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
