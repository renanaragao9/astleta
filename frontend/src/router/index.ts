import '@/router/types';
import { createRouter, createWebHistory, type RouteRecordRaw } from 'vue-router';
import { homeByProfile, type UserProfile, USER_PROFILES, defaultRoute } from './types';
import { checkAuthentication, checkPermissions } from '@/router/utils';
import { publicRoutes } from '@/router/routes/public';
import { authRoutes } from '@/router/routes/auth';
import { companyRoutes } from '@/router/routes/company';
import { athleteRoutes } from '@/router/routes/athlete';
import { adminRoutes } from '@/router/routes/admin';
import { notFoundRoute } from '@/router/routes/notFound';
import { useAuthStore } from '@/stores/auth/loginStore';

const routes: RouteRecordRaw[] = [...publicRoutes, ...companyRoutes, ...athleteRoutes, ...adminRoutes, ...authRoutes, notFoundRoute];

const router = createRouter({
    history: createWebHistory('/'),
    routes
});

router.beforeEach(async (to) => {
    const auth = useAuthStore();

    const authed = await checkAuthentication(auth);
    const profile = auth.user?.profile?.name as UserProfile | undefined;

    if (to.matched.some((r) => r.meta.requiresAuth) && !authed) {
        return { name: 'login', query: { redirect: to.fullPath } };
    }

    if (to.matched.some((r) => r.meta.guestOnly) && authed) {
        const redirectRoute = homeByProfile[profile ?? USER_PROFILES.COMPANY] ?? defaultRoute;
        return { ...redirectRoute, replace: true };
    }

    const withACL = to.matched.find((r) => r.meta.allowedProfiles);
    if (withACL?.meta.allowedProfiles && authed) {
        const allowed = withACL.meta.allowedProfiles;
        if (!checkPermissions(profile, allowed)) {
            const redirectRoute = homeByProfile[profile ?? USER_PROFILES.COMPANY] ?? defaultRoute;
            return { ...redirectRoute, replace: true };
        }
    }

    return true;
});

router.afterEach((to) => {
    if (to.meta.title) document.title = to.meta.title as string;

    if (to.meta.description) {
        const meta = document.querySelector('meta[name="description"]');
        if (meta) meta.setAttribute('content', to.meta.description as string);
    }
});

export default router;
