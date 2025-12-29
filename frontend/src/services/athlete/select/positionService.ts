import api from '@/config/api';
import type { Position } from '@/types/athlete/select/Position';

export class PositionService {
    private static readonly BASE_URL = '/athlete/positions';

    static async getPositions(sportId?: number): Promise<Position[]> {
        const params = sportId ? { sport_id: sportId } : {};
        const response = await api.get(this.BASE_URL, { params });
        return response.data.data;
    }
}
