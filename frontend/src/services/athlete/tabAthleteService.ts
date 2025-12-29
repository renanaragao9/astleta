import api from '@/config/api';
import type { Tab, TabResponse } from '@/types/athlete/tabAthlete';
import type { TabFilters } from '@/types/athlete/filters/tabFilter';

export class TabService {
    private static readonly BASE_URL = '/athlete/tabs';

    static async getTabs(filters: Partial<TabFilters> = {}): Promise<TabResponse> {
        const params = new URLSearchParams();

        if (filters.search) params.append('search', filters.search);
        if (filters.status) params.append('status', filters.status);
        if (filters.customer_name) params.append('customer_name', filters.customer_name);
        if (filters.payment_form_id) params.append('payment_form_id', filters.payment_form_id.toString());
        if (filters.start_created_date) params.append('start_created_date', filters.start_created_date);
        if (filters.end_created_date) params.append('end_created_date', filters.end_created_date);
        if (filters.sort) params.append('sort', filters.sort);
        if (filters.direction) params.append('direction', filters.direction);
        if (filters.perPage) params.append('per_page', filters.perPage.toString());
        if (filters.page) params.append('page', filters.page.toString());

        const queryString = params.toString();
        const url = queryString ? `${this.BASE_URL}?${queryString}` : this.BASE_URL;

        const response = await api.get(url);
        return response.data;
    }

    static async getTab(id: number): Promise<{ data: Tab; message: string }> {
        const response = await api.get(`${this.BASE_URL}/${id}`);
        return { data: response.data.data, message: response.data.message };
    }

    static async downloadReceipt(id: number): Promise<Blob> {
        const response = await api.get(`${this.BASE_URL}/${id}/receipt`, {
            responseType: 'blob'
        });
        return response.data;
    }
}
