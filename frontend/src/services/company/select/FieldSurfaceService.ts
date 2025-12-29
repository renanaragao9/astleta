import api from '@/config/api';
import type { FieldSurface } from '@/types/company/select/fieldSurface';

export class FieldSurfaceService {
    private static readonly BASE_URL = '/company';

    static async getFieldSurfaces(): Promise<FieldSurface[]> {
        const response = await api.get(`${this.BASE_URL}/field-surfaces`);
        return response.data.data;
    }
}
