import api from '@/config/api';
import type { FieldType } from '@/types/company/select/fieldType';

export class FieldTypeService {
    private static readonly BASE_URL = '/company';

    static async getFieldTypes(): Promise<FieldType[]> {
        const response = await api.get(`${this.BASE_URL}/field-types`);
        return response.data.data;
    }
}
