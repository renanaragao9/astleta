import api from '@/config/api';
import type { Field, FieldResponse, FieldPayload } from '@/types/company/field';
import type { Filters } from '@/types/global/filter';

export class FieldService {
    private static readonly BASE_URL = '/company/fields';

    private static preparePayload(fieldData: FieldPayload | Partial<FieldPayload>): FormData | FieldPayload | Partial<FieldPayload> {
        return fieldData;
    }

    private static getHeaders(): Record<string, string> {
        return {
            'Content-Type': 'application/json'
        };
    }

    static async getFields(filters: Partial<Filters> = {}): Promise<FieldResponse> {
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

    static async getField(id: number): Promise<{ data: Field; message: string }> {
        const response = await api.get(`${this.BASE_URL}/${id}`);
        return { data: response.data.data, message: response.data.message };
    }

    static async createField(fieldData: FieldPayload): Promise<{ data: Field; message: string }> {
        const payload = this.preparePayload(fieldData);
        const response = await api.post(this.BASE_URL, payload, {
            headers: this.getHeaders()
        });
        return { data: response.data.data, message: response.data.message };
    }

    static async updateField(id: number, fieldData: Partial<FieldPayload>): Promise<{ data: Field; message: string }> {
        const payload = this.preparePayload(fieldData);
        const response = await api.put(`${this.BASE_URL}/${id}`, payload, {
            headers: this.getHeaders()
        });
        return { data: response.data.data, message: response.data.message };
    }

    static async deleteField(id: number): Promise<{ message: string }> {
        const response = await api.delete(`${this.BASE_URL}/${id}`);
        return { message: response.data.message };
    }

    static async updateImage(id: number, image: File): Promise<{ data: { image_path: string }; message: string }> {
        const formData = new FormData();
        formData.append('image', image);

        const response = await api.post(`${this.BASE_URL}/${id}/update-image`, formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });

        return { data: response.data.data, message: response.data.message };
    }
}
