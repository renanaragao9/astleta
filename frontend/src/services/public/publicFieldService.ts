import api from '@/config/api';
import type { PublicFieldFilters, PublicFieldResponse } from '@/types/public/field/fieldIndex';
import type { PublicField as PublicFieldDetail } from '@/types/public/field/fieldDetail';

export class PublicFieldService {
    private static readonly BASE_URL = '/public_field/fields';

    static async getFields(filters: Partial<PublicFieldFilters> = {}): Promise<PublicFieldResponse & { message: string }> {
        const params = new URLSearchParams();

        if (filters.search) params.append('search', filters.search);
        if (filters.city) params.append('city', filters.city);
        if (filters.state) params.append('state', filters.state);
        if (filters.sportType) params.append('sport_type', filters.sportType);
        if (filters.priceMin !== undefined) params.append('price_min', filters.priceMin.toString());
        if (filters.priceMax !== undefined) params.append('price_max', filters.priceMax.toString());
        if (filters.sort) params.append('sort', filters.sort);
        if (filters.direction) params.append('direction', filters.direction);
        if (filters.page) params.append('page', filters.page.toString());
        if (filters.perPage) params.append('per_page', filters.perPage.toString());

        const queryString = params.toString();
        const url = queryString ? `${this.BASE_URL}?${queryString}` : this.BASE_URL;

        const response = await api.get(url);
        return response.data;
    }

    static async getField(id: number): Promise<{ data: PublicFieldDetail; message: string }> {
        const response = await api.get(`${this.BASE_URL}/${id}`);
        return response.data;
    }
}
