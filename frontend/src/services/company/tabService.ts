import api from '@/config/api';
import type { Tab, TabResponse, TabPayload } from '@/types/company/tab';
import type { TabFilters } from '@/types/company/filters/tabFilter';
import type { SendTabDataPayload } from '@/types/company/tab';

export class TabService {
    private static readonly BASE_URL = '/company/tabs';

    private static preparePayload(tabData: TabPayload | Partial<TabPayload>): TabPayload | Partial<TabPayload> {
        return tabData;
    }

    private static getHeaders(): Record<string, string> {
        return {
            'Content-Type': 'application/json'
        };
    }

    static async getTabs(filters: Partial<TabFilters> = {}): Promise<TabResponse> {
        const params = new URLSearchParams();

        if (filters.search) params.append('search', filters.search);
        if (filters.sort) params.append('sort', filters.sort);
        if (filters.direction) params.append('direction', filters.direction);
        if (filters.perPage) params.append('per_page', filters.perPage.toString());
        if (filters.page) params.append('page', filters.page.toString());
        if (filters.status) params.append('status', filters.status);
        if (filters.customerName) params.append('customer_name', filters.customerName);
        if (filters.paymentFormId) params.append('payment_form_id', filters.paymentFormId.toString());
        if (filters.startCreatedDate) params.append('start_created_date', filters.startCreatedDate);
        if (filters.endCreatedDate) params.append('end_created_date', filters.endCreatedDate);

        const queryString = params.toString();
        const url = queryString ? `${this.BASE_URL}?${queryString}` : this.BASE_URL;

        const response = await api.get(url);
        return response.data;
    }

    static async getTab(id: number): Promise<{ data: Tab; message: string }> {
        const response = await api.get(`${this.BASE_URL}/${id}`);
        return { data: response.data.data, message: response.data.message };
    }

    static async createTab(tabData: TabPayload): Promise<{ data: Tab; message: string }> {
        const payload = this.preparePayload(tabData);
        const response = await api.post(this.BASE_URL, payload, {
            headers: this.getHeaders()
        });
        return { data: response.data.data, message: response.data.message };
    }

    static async updateTab(id: number, tabData: Partial<TabPayload>): Promise<{ data: Tab; message: string }> {
        const payload = this.preparePayload(tabData);
        const response = await api.put(`${this.BASE_URL}/${id}`, payload, {
            headers: this.getHeaders()
        });
        return { data: response.data.data, message: response.data.message };
    }

    static async cancelTab(id: number): Promise<{ message: string }> {
        const response = await api.put(`${this.BASE_URL}/${id}`, { status: 'cancelado' });
        return { message: response.data.message };
    }

    static async sendTab(sendData: SendTabDataPayload): Promise<{ message: string }> {
        const response = await api.post(`${this.BASE_URL}/send`, sendData, {
            headers: this.getHeaders()
        });
        return { message: response.data.message };
    }
}
