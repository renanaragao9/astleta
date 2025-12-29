import api from '@/config/api';
import type { PurchaseFilters } from '@/types/company/filters/purchaseFilter';
import type { Purchase, PurchasePayload, PurchaseResponse } from '@/types/company/purchase';

export class PurchaseService {
    private static readonly BASE_URL = '/company/purchases';

    private static getHeaders(): Record<string, string> {
        return {
            'Content-Type': 'application/json'
        };
    }

    private static preparePayload(purchaseData: PurchasePayload | Partial<PurchasePayload>): PurchasePayload | Partial<PurchasePayload> {
        return purchaseData;
    }

    static async getPurchases(filters: Partial<PurchaseFilters> = {}): Promise<PurchaseResponse> {
        const params = new URLSearchParams();

        if (filters.search) params.append('search', filters.search);
        if (filters.sort) params.append('sort', filters.sort);
        if (filters.direction) params.append('direction', filters.direction);
        if (filters.perPage) params.append('per_page', filters.perPage.toString());
        if (filters.page) params.append('page', filters.page.toString());
        if (filters.supplierId) params.append('supplier_id', filters.supplierId.toString());
        if (filters.status) params.append('status', filters.status);
        if (filters.startPurchaseDate) params.append('start_purchase_date', filters.startPurchaseDate);
        if (filters.endPurchaseDate) params.append('end_purchase_date', filters.endPurchaseDate);

        const queryString = params.toString();
        const url = queryString ? `${this.BASE_URL}?${queryString}` : this.BASE_URL;

        const response = await api.get(url);
        return response.data;
    }

    static async getPurchase(id: number): Promise<{ data: Purchase; message: string }> {
        const response = await api.get(`${this.BASE_URL}/${id}`, {
            headers: this.getHeaders()
        });

        return { data: response.data.data, message: response.data.message };
    }

    static async createPurchase(purchaseData: PurchasePayload): Promise<{ data: Purchase; message: string }> {
        const payload = this.preparePayload(purchaseData);

        const response = await api.post(this.BASE_URL, payload, {
            headers: this.getHeaders()
        });

        return { data: response.data.data, message: response.data.message };
    }

    static async updatePurchase(id: number, purchaseData: Partial<PurchasePayload>): Promise<{ data: Purchase; message: string }> {
        const payload = this.preparePayload(purchaseData);

        const response = await api.put(`${this.BASE_URL}/${id}`, payload, {
            headers: this.getHeaders()
        });

        return { data: response.data.data, message: response.data.message };
    }

    static async deletePurchase(id: number): Promise<{ message: string }> {
        const response = await api.delete(`${this.BASE_URL}/${id}`, {
            headers: this.getHeaders()
        });

        return { message: response.data.message };
    }

    static async deleteSelectedPurchases(ids: number[]): Promise<{ message: string }> {
        const response = await api.delete(this.BASE_URL, {
            data: { ids },
            headers: this.getHeaders()
        });

        return { message: response.data.message };
    }

    static async selectPurchases(): Promise<{ data: Purchase[]; message: string }> {
        const response = await api.get(`${this.BASE_URL}/select`);
        return response.data;
    }
}
