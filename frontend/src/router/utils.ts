import { useAuthStore } from '@/stores/auth/loginStore';
import { type UserProfile } from '@/router/types';

export const checkAuthentication = async (auth: ReturnType<typeof useAuthStore>): Promise<boolean> => {
    const isAuthenticated = auth.isAuthenticated ?? !!localStorage.getItem('token');

    if (isAuthenticated && !auth.user) {
        try {
            await auth.fetchUser();
            return true;
        } catch {
            auth.logout?.();
            return false;
        }
    }

    return !!auth.user;
};

export const checkPermissions = (profile: UserProfile | undefined, allowedProfiles: UserProfile[]): boolean => {
    return !!profile && allowedProfiles.includes(profile);
};
