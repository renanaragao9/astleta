import api from '@/config/api';
import type { AthleteProfilePayload, AthleteProfileResponse, UserPayload } from '@/types/athlete/profile';

export class AthleteProfileService {
    private static readonly BASE_URL = '/athlete/profile';

    static async getProfile(): Promise<AthleteProfileResponse> {
        const response = await api.get(this.BASE_URL);
        return { data: response.data.data, message: response.data.message };
    }

    static async createProfile(data: AthleteProfilePayload): Promise<AthleteProfileResponse> {
        const response = await api.post(this.BASE_URL, data);
        return { data: response.data.data, message: response.data.message };
    }

    static async updateProfile(data: AthleteProfilePayload): Promise<AthleteProfileResponse> {
        const response = await api.put(this.BASE_URL, data);
        return { data: response.data.data, message: response.data.message };
    }

    static async updateImage(image: File): Promise<AthleteProfileResponse> {
        const formData = new FormData();
        formData.append('image', image);

        const response = await api.post('/athlete/update-image', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });
        return { data: response.data.data, message: response.data.message };
    }

    static async updateUser(data: UserPayload): Promise<AthleteProfileResponse> {
        const response = await api.patch('/athlete/profile/user', data);
        return { data: response.data.data, message: response.data.message };
    }
}
