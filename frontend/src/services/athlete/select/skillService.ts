import api from '@/config/api';
import type { Skill } from '@/types/athlete/select/Skill';

export class SkillService {
    private static readonly BASE_URL = '/athlete/skills';

    static async getSkills(sportId?: number): Promise<Skill[]> {
        const params = sportId ? { sport_id: sportId } : {};
        const response = await api.get(this.BASE_URL, { params });
        return response.data.data;
    }
}
