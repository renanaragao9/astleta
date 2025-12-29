import api from '@/config/api';
import type { Feature } from '@/types/athlete/select/Feature';

export class FeatureService {
    private static readonly BASE_URL = '/athlete/features';

    static async getFeatures(positionId?: number): Promise<Feature[]> {
        const params = positionId ? { position_id: positionId } : {};
        const response = await api.get(this.BASE_URL, { params });
        return response.data.data;
    }
}
