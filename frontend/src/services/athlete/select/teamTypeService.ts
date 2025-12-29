import api from '@/config/api';
import type { TeamType } from '@/types/athlete/select/TeamType';

export class TeamTypeService {
    private static readonly BASE_URL = '/athlete/team-types';

    static async getTeamTypes(): Promise<TeamType[]> {
        const response = await api.get(this.BASE_URL);
        return response.data.data;
    }
}
