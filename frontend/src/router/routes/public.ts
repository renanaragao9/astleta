import { type RouteRecordRaw } from 'vue-router';

export const publicRoutes: RouteRecordRaw[] = [
    {
        path: '/',
        name: 'home',
        component: () => import('@/views/pages/public/FieldsIndex.vue'),
        meta: { requiresAuth: false }
    },

    {
        path: '/campos',
        name: 'campos',
        component: () => import('@/views/pages/public/FieldsIndex.vue'),
        meta: { requiresAuth: false }
    },

    {
        path: '/arenas',
        redirect: '/campos'
    },

    {
        path: '/campo/:id',
        name: 'campo-detalhe',
        component: () => import('@/views/pages/public/FieldDetail.vue'),
        meta: { requiresAuth: false }
    },

    {
        path: '/arena/:id',
        redirect: (to) => `/campo/${to.params.id}`
    },

    {
        path: '/empresa/:id',
        name: 'empresa-perfil',
        component: () => import('@/views/pages/public/CompanyProfile.vue'),
        meta: { requiresAuth: false }
    },

    {
        path: '/anunciar-campo',
        name: 'anunciar-campo',
        component: () => import('@/views/pages/public/AdvertiseField.vue'),
        meta: { requiresAuth: false }
    },

    {
        path: '/como-funciona',
        name: 'como-funciona',
        component: () => import('@/views/pages/public/HowItWorks.vue'),
        meta: { requiresAuth: false }
    },

    {
        path: '/baa',
        name: 'baa',
        component: () => import('@/views/pages/public/BAA.vue'),
        meta: { requiresAuth: false }
    }
];
