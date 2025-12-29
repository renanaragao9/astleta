import { defineStore } from 'pinia';
import { ref } from 'vue';
import type { AthleteProfile, AthleteProfilePayload, UserPayload } from '@/types/athlete/profile';
import { AthleteProfileService } from '@/services/athlete/athleteProfileService';
import { errorMessageHttpResquestUtils } from '@/utils/errorMessageHttpResquestUtils';

export const useAthleteProfileStore = defineStore('athlete-profile', () => {
    const profile = ref<AthleteProfile | null>(null);
    const loading = ref(false);
    const error = ref<string | null>(null);

    async function fetchProfile(): Promise<void> {
        loading.value = true;
        error.value = null;

        try {
            const response = await AthleteProfileService.getProfile();
            profile.value = response.data;
        } catch (err: unknown) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao carregar perfil');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function createProfile(payload: AthleteProfilePayload): Promise<void> {
        loading.value = true;
        error.value = null;

        try {
            const response = await AthleteProfileService.createProfile(payload);
            profile.value = response.data;
        } catch (err: unknown) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao criar perfil');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function updateProfile(payload: AthleteProfilePayload): Promise<void> {
        loading.value = true;
        error.value = null;

        try {
            const response = await AthleteProfileService.updateProfile(payload);
            profile.value = response.data;
        } catch (err: unknown) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao atualizar perfil');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function updateImage(image: File): Promise<void> {
        loading.value = true;
        error.value = null;

        try {
            const response = await AthleteProfileService.updateImage(image);
            profile.value = response.data;
        } catch (err: unknown) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao atualizar imagem');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function updateUser(payload: UserPayload): Promise<void> {
        loading.value = true;
        error.value = null;

        try {
            const response = await AthleteProfileService.updateUser(payload);
            profile.value = response.data;
        } catch (err: unknown) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao atualizar informações pessoais');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    function clearProfile(): void {
        profile.value = null;
        error.value = null;
    }

    return {
        profile,
        loading,
        error,
        fetchProfile,
        updateProfile,
        createProfile,
        updateImage,
        updateUser,
        clearProfile
    };
});
