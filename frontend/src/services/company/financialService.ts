import api from '@/config/api';
import type { FinancialData, FinancialFilters } from '@/types/company/financial';

export class FinancialService {
    private static readonly BASE_URL = '/company/financial';

    static async getFinancials(filters: Partial<FinancialFilters> = {}): Promise<{ data: FinancialData; message: string }> {
        const params = new URLSearchParams();

        if (filters.start_date) params.append('start_date', filters.start_date);
        if (filters.end_date) params.append('end_date', filters.end_date);

        const queryString = params.toString();
        const url = queryString ? `${this.BASE_URL}?${queryString}` : this.BASE_URL;

        const response = await api.get(url);
        return { data: response.data.data.data, message: response.data.message };
    }
}
