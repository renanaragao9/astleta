import api from '@/config/api';
import type { FieldItem } from '@/types/company/select/fieldItem';

export class FieldItemService {
    private static readonly BASE_URL = '/company/field-items';

    static async getFieldItems(): Promise<{ data: FieldItem[] }> {
        const response = await api.get(this.BASE_URL);
        return response.data;
    }
}
