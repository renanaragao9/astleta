import api from '@/config/api';
import type { CompanyResponse } from '@/types/company/company';

export class CompanyService {
    private static readonly BASE_URL = '/company/company';

    static async getCompany(): Promise<CompanyResponse> {
        const response = await api.get(this.BASE_URL);
        return response.data;
    }

    static async updateImage(image: File): Promise<{ data: { image_path: string }; message: string }> {
        const formData = new FormData();
        formData.append('image', image);

        const response = await api.post(`${this.BASE_URL}/update-image`, formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });
        return { data: response.data.data, message: response.data.message };
    }
}
