import AppLayout from '@/layout/AppLayout.vue';
import { type RouteRecordRaw } from 'vue-router';
import { USER_PROFILES } from '@/router/types';

export const athleteRoutes: RouteRecordRaw[] = [
    {
        path: '/atleta',
        component: AppLayout,
        meta: { requiresAuth: true, allowedProfiles: [USER_PROFILES.ATHLETE] },
        children: [
            {
                path: 'perfil',
                name: 'athleteProfile',
                component: () => import('@/views/athlete/ProfileView.vue')
            },
            {
                path: 'reservas',
                name: 'athleteBookings',
                component: () => import('@/views/athlete/BookingAthleteView.vue')
            },
            {
                path: 'reservas/:id',
                name: 'athleteBookingDetails',
                component: () => import('@/views/athlete/booking/BookingDetailsView.vue'),
                props: true
            },
            {
                path: 'comandas',
                name: 'athleteTabs',
                component: () => import('@/views/athlete/TabAthleteView.vue')
            },
            {
                path: 'comandas/:id',
                name: 'athleteTabDetails',
                component: () => import('@/views/athlete/tab/TabDetailsView.vue'),
                props: true
            },
            {
                path: 'rachas',
                name: 'athleteRachas',
                component: () => import('@/views/athlete/RachaView.vue')
            },
            {
                path: 'times',
                name: 'athleteTeams',
                component: () => import('@/views/athlete/TeamView.vue')
            }
        ]
    }
];
