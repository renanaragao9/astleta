declare module 'vue-router' {
    interface RouteMeta {
        requiresAuth?: boolean;
        guestOnly?: boolean;
        allowedProfiles?: Array<'company' | 'athlete' | 'admin'>;
    }
}

export const USER_PROFILES = {
    COMPANY: 'company',
    ATHLETE: 'athlete',
    ADMIN: 'admin'
} as const;

export type UserProfile = (typeof USER_PROFILES)[keyof typeof USER_PROFILES];

export const homeByProfile: Record<UserProfile, { name: string }> = {
    [USER_PROFILES.COMPANY]: { name: 'companyDashboard' },
    [USER_PROFILES.ATHLETE]: { name: 'athleteProfile' },
    [USER_PROFILES.ADMIN]: { name: 'crud' }
};

export const defaultRoute = { name: 'home' };
