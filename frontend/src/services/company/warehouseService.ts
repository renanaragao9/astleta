import api from '@/config/api';
import type { WarehouseFilters } from '@/types/company/filters/warehouseFilter';
import type { Warehouse, WarehousePayload, WarehouseResponse } from '@/types/company/warehouse';

export class WarehouseService {
    private static readonly BASE_URL = '/company/warehouses';

    private static getHeaders(): Record<string, string> {
        return {
            'Content-Type': 'application/json'
        };
    }

    private static preparePayload(warehouseData: WarehousePayload | Partial<WarehousePayload>): WarehousePayload | Partial<WarehousePayload> {
        return warehouseData;
    }

    static async getWarehouses(filters: Partial<WarehouseFilters> = {}): Promise<WarehouseResponse> {
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

    static async getWarehouse(id: number): Promise<{ data: Warehouse; message: string }> {
        const response = await api.get(`${this.BASE_URL}/${id}`);
        return { data: response.data.data, message: response.data.message };
    }

    static async createWarehouse(warehouseData: WarehousePayload): Promise<{ data: Warehouse; message: string }> {
        const payload = this.preparePayload(warehouseData);
        const response = await api.post(this.BASE_URL, payload, {
            headers: this.getHeaders()
        });
        return { data: response.data.data, message: response.data.message };
    }

    static async updateWarehouse(id: number, warehouseData: Partial<WarehousePayload>): Promise<{ data: Warehouse; message: string }> {
        const payload = this.preparePayload(warehouseData);
        const response = await api.put(`${this.BASE_URL}/${id}`, payload, {
            headers: this.getHeaders()
        });
        return { data: response.data.data, message: response.data.message };
    }

    static async deleteWarehouse(id: number): Promise<{ message: string }> {
        const response = await api.delete(`${this.BASE_URL}/${id}`);
        return { message: response.data.message };
    }

    static async deleteSelectedWarehouses(ids: number[]): Promise<{ message: string }> {
        const response = await api.delete(this.BASE_URL, {
            data: { ids },
            headers: this.getHeaders()
        });
        return { message: response.data.message };
    }

    static async selectWarehouses(): Promise<{ data: Warehouse[]; message: string }> {
        const response = await api.get(`${this.BASE_URL}-select`);
        return response.data;
    }
}
