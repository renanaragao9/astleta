import { type RouteRecordRaw } from 'vue-router';

export const notFoundRoute: RouteRecordRaw = {
    path: '/:pathMatch(.*)*',
    name: 'not-found',
    component: () => import('@/views/pages/NotFound.vue')
};
