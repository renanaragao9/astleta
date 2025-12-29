import api from '@/config/api';
import type { SupplierFilters } from '@/types/company/filters/supplierFilter';
import type { Supplier, SupplierPayload, SupplierResponse } from '@/types/company/supplier';

export class SupplierService {
    private static readonly BASE_URL = '/company/suppliers';

    private static getHeaders(): Record<string, string> {
        return {
            'Content-Type': 'application/json'
        };
    }

    private static preparePayload(supplierData: SupplierPayload | Partial<SupplierPayload>): SupplierPayload | Partial<SupplierPayload> {
        return supplierData;
    }

    static async getSuppliers(filters: Partial<SupplierFilters> = {}): Promise<SupplierResponse> {
        const params = new URLSearchParams();

        if (filters.search) params.append('search', filters.search);
        if (filters.sort) params.append('sort', filters.sort);
        if (filters.direction) params.append('direction', filters.direction);
        if (filters.perPage) params.append('per_page', filters.perPage.toString());
        if (filters.page) params.append('page', filters.page.toString());

        const queryString = params.toString();
        const url = queryString ? `${this.BASE_URL}?${queryString}` : this.BASE_URL;

        const response = await api.get(url);
        return response.data;
    }

    static async getSupplier(id: number): Promise<{ data: Supplier; message: string }> {
        const response = await api.get(`${this.BASE_URL}/${id}`);
        return { data: response.data.data, message: response.data.message };
    }

    static async createSupplier(supplierData: SupplierPayload): Promise<{ data: Supplier; message: string }> {
        const payload = this.preparePayload(supplierData);
        const response = await api.post(this.BASE_URL, payload, {
            headers: this.getHeaders()
        });
        return { data: response.data.data, message: response.data.message };
    }

    static async updateSupplier(id: number, supplierData: Partial<SupplierPayload>): Promise<{ data: Supplier; message: string }> {
        const payload = this.preparePayload(supplierData);
        const response = await api.put(`${this.BASE_URL}/${id}`, payload, {
            headers: this.getHeaders()
        });
        return { data: response.data.data, message: response.data.message };
    }

    static async deleteSupplier(id: number): Promise<{ message: string }> {
        const response = await api.delete(`${this.BASE_URL}/${id}`);
        return { message: response.data.message };
    }

    static async deleteSelectedSuppliers(ids: number[]): Promise<{ message: string }> {
        const response = await api.delete(this.BASE_URL, {
            data: { ids },
            headers: this.getHeaders()
        });
        return { message: response.data.message };
    }

    static async selectSuppliers(): Promise<{ data: Supplier[]; message: string }> {
        const response = await api.get(`${this.BASE_URL}-select`);
        return response.data;
    }
}
