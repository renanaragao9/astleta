import api from '@/config/api';
import type { FieldSize } from '@/types/company/select/fieldSize';

export class FieldSizeService {
    private static readonly BASE_URL = '/company';

    static async getFieldSizes(): Promise<FieldSize[]> {
        const response = await api.get(`${this.BASE_URL}/field-sizes`);
        return response.data.data;
    }
}
