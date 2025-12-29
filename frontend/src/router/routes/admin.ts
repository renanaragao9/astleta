import AppLayout from '@/layout/AppLayout.vue';
import { type RouteRecordRaw } from 'vue-router';
import { USER_PROFILES } from '@/router/types';

export const adminRoutes: RouteRecordRaw[] = [
    {
        path: '/pages/crud',
        component: AppLayout,
        meta: { requiresAuth: true, allowedProfiles: [USER_PROFILES.ADMIN] },
        children: [
            {
                path: '',
                name: 'crud',
                component: () => import('@/views/pages/Crud.vue')
            }
        ]
    }
];
