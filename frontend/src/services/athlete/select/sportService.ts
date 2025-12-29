import api from '@/config/api';
import type { Sport } from '@/types/athlete/select/Sport';

export class SportService {
    private static readonly BASE_URL = '/athlete/sports';

    static async getSports(): Promise<Sport[]> {
        const response = await api.get(this.BASE_URL);
        return response.data.data;
    }
}
