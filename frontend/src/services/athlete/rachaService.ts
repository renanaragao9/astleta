import api from '@/config/api';
import type { AthleteRachaListResponse, AthleteRacha } from '@/types/athlete/racha';
import type { RachaFilters } from '@/types/athlete/filters/rachaFilters';

export class AthleteRachaService {
    private static readonly BASE_URL = '/athlete/rachas';

    static async getRachas(filters: Partial<RachaFilters> = {}): Promise<AthleteRachaListResponse> {
        const params = new URLSearchParams();

        if (filters.search) params.append('search', filters.search);
        if (filters.sort) params.append('sort', filters.sort);
        if (filters.direction) params.append('direction', filters.direction);
        if (filters.per_page) params.append('per_page', filters.per_page.toString());
        if (filters.page) params.append('page', filters.page.toString());
        if (filters.booking_status) params.append('booking_status', filters.booking_status);
        if (filters.start_date) params.append('start_date', filters.start_date);
        if (filters.end_date) params.append('end_date', filters.end_date);

        const queryString = params.toString();
        const url = queryString ? `${this.BASE_URL}?${queryString}` : this.BASE_URL;

        const response = await api.get(url);
        return {
            data: response.data.data,
            meta: response.data.meta
        };
    }

    static async getRacha(id: number): Promise<{ data: AthleteRacha; message: string }> {
        const response = await api.get(`${this.BASE_URL}/${id}`);
        return { data: response.data.data, message: response.data.message };
    }

    static async joinRacha(racha_number: string): Promise<{ message: string }> {
        const response = await api.post(`${this.BASE_URL}/join`, { racha_number });
        return { message: response.data.message };
    }
}
