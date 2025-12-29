import api from '@/config/api';
import type { AthleteProfile, AthleteProfileResponse, AthleteProfileFilters } from '@/types/public/baa';

export class BaaService {
    private static readonly BASE_URL = '/baa';

    static async getAthletes(filters: Partial<AthleteProfileFilters> = {}): Promise<AthleteProfileResponse> {
        const params = new URLSearchParams();

        if (filters.name) {
            params.append('name', filters.name);
        }
        if (filters.page) {
            params.append('page', filters.page.toString());
        }
        if (filters.per_page) {
            params.append('per_page', filters.per_page.toString());
        }

        const response = await api.get(`${this.BASE_URL}/athletes?${params.toString()}`);
        const rawData = response.data;
        const athletes = rawData.data && Array.isArray(rawData.data) ? rawData.data : Array.isArray(rawData) ? rawData : [];

        return {
            data: athletes,
            meta: {
                current_page: rawData.current_page || rawData.meta?.current_page || 1,
                last_page: rawData.last_page || rawData.meta?.last_page || 1,
                per_page: rawData.per_page || rawData.meta?.per_page || 7,
                total: rawData.total || rawData.meta?.total || 0
            }
        };
    }

    static async getAthlete(id: number): Promise<{ data: AthleteProfile; message: string }> {
        const response = await api.get(`${this.BASE_URL}/athletes/${id}`);
        return response.data;
    }
}
