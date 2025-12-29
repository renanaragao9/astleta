import { type RouteRecordRaw } from 'vue-router';

export const authRoutes: RouteRecordRaw[] = [
    {
        path: '/login',
        name: 'login',
        component: () => import('@/views/pages/auth/Login.vue'),
        meta: { guestOnly: true }
    },
    {
        path: '/registro',
        name: 'register',
        component: () => import('@/views/pages/auth/Register.vue'),
        meta: { guestOnly: true }
    },
    {
        path: '/verificar-email',
        name: 'verifyEmail',
        component: () => import('@/views/pages/auth/VerifyEmail.vue'),
        meta: { guestOnly: true }
    },
    {
        path: '/esqueci-minha-senha',
        name: 'forgotPassword',
        component: () => import('@/views/pages/auth/ForgotPassword.vue'),
        meta: { guestOnly: true }
    },
    {
        path: '/redefinir-senha',
        name: 'resetPassword',
        component: () => import('@/views/pages/auth/ResetPassword.vue'),
        meta: { guestOnly: true }
    },
    {
        path: '/acesso-negado',
        name: 'accessDenied',
        component: () => import('@/views/pages/auth/AccessDenied.vue')
    },
    {
        path: '/erro',
        name: 'error',
        component: () => import('@/views/pages/auth/Error.vue')
    }
];
