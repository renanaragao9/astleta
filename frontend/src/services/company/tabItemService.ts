import api from '@/config/api';
import type { TabItem, TabItemResponse, TabItemPayload } from '@/types/company/tab';
import type { TabItemFilters } from '@/types/company/filters/tabFilter';

export class TabItemService {
    private static readonly BASE_URL = '/company/tab-items';

    static async getTabItems(filters: Partial<TabItemFilters> = {}): Promise<TabItemResponse> {
        const params = new URLSearchParams();

        if (filters.tabId) params.append('tab_id', filters.tabId.toString());
        if (filters.productId) params.append('product_id', filters.productId.toString());
        if (filters.sort) params.append('sort', filters.sort);
        if (filters.direction) params.append('direction', filters.direction);
        if (filters.perPage) params.append('per_page', filters.perPage.toString());
        if (filters.page) params.append('page', filters.page.toString());

        const queryString = params.toString();
        const url = queryString ? `${this.BASE_URL}?${queryString}` : this.BASE_URL;

        const response = await api.get(url);
        return response.data;
    }

    static async getTabItem(id: number): Promise<{ data: TabItem; message: string }> {
        const response = await api.get(`${this.BASE_URL}/${id}`);
        return { data: response.data.data, message: response.data.message };
    }

    static async createTabItem(tabItemData: TabItemPayload): Promise<{ data: TabItem; message: string }> {
        const payload = this.preparePayload(tabItemData);
        const response = await api.post(this.BASE_URL, payload, {
            headers: this.getHeaders()
        });
        return { data: response.data.data, message: response.data.message };
    }

    static async deleteTabItem(id: number): Promise<{ message: string }> {
        const response = await api.delete(`${this.BASE_URL}/${id}`);
        return { message: response.data.message };
    }

    private static preparePayload(tabItemData: TabItemPayload | Partial<TabItemPayload>): TabItemPayload | Partial<TabItemPayload> {
        return tabItemData;
    }

    private static getHeaders(): Record<string, string> {
        return {
            'Content-Type': 'application/json'
        };
    }
}
