import { type RouteRecordRaw } from 'vue-router';

export const publicRoutes: RouteRecordRaw[] = [
    {
        path: '/',
        name: 'home',
        component: () => import('@/views/pages/public/FieldsIndex.vue'),
        meta: { requiresAuth: false }
    },
    {
        path: '/arenas',
        name: 'fields-index',
        component: () => import('@/views/pages/public/FieldsIndex.vue'),
        meta: { requiresAuth: false }
    },
    {
        path: '/campos',
        name: 'fields-index',
        component: () => import('@/views/pages/public/FieldsIndex.vue'),
        meta: { requiresAuth: false }
    },
    {
        path: '/arena/:id',
        name: 'field-detail',
        component: () => import('@/views/pages/public/FieldDetail.vue'),
        meta: { requiresAuth: false }
    },
    {
        path: '/campo/:id',
        name: 'field-detail',
        component: () => import('@/views/pages/public/FieldDetail.vue'),
        meta: { requiresAuth: false }
    },
    {
        path: '/empresa/:id',
        name: 'company-profile',
        component: () => import('@/views/pages/public/CompanyProfile.vue'),
        meta: { requiresAuth: false }
    },
    {
        path: '/compania/:id',
        name: 'company-profile',
        component: () => import('@/views/pages/public/CompanyProfile.vue'),
        meta: { requiresAuth: false }
    },
    {
        path: '/anunciar-campo',
        name: 'advertise-field',
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
