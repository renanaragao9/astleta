import api from '@/config/api';
import type { PublicCompany, PublicCompanyField } from '@/types/public/company/companyProfile';

interface ApiResponse {
    status: string;
    message: string;
    data: {
        company: PublicCompany;
        fields: PublicCompanyField[];
    };
}

export class PublicCompanyService {
    private static readonly BASE_URL = '/public_field/companies';

    static async getCompanyProfile(companyId: number): Promise<ApiResponse> {
        const response = await api.get(`${this.BASE_URL}/${companyId}`);
        return response.data;
    }
}
