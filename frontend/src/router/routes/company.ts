import AppLayout from '@/layout/AppLayout.vue';
import { type RouteRecordRaw } from 'vue-router';
import { USER_PROFILES } from '@/router/types';

export const companyRoutes: RouteRecordRaw[] = [
    {
        path: '/empresa',
        component: AppLayout,
        meta: { requiresAuth: true, allowedProfiles: [USER_PROFILES.COMPANY] },
        children: [
            {
                path: '',
                name: 'companyDashboard',
                component: () => import('@/views/company/CompanyView.vue')
            },
            {
                path: 'arenas',
                name: 'companyFields',
                component: () => import('@/views/company/FieldView.vue')
            },
            {
                path: 'reservas',
                name: 'companyBookings',
                component: () => import('@/views/company/BookingView.vue')
            },
            {
                path: 'produtos',
                name: 'companyProducts',
                component: () => import('@/views/company/ProductView.vue')
            },
            {
                path: 'torneios',
                name: 'companyTournaments',
                component: () => import('@/views/company/TournamentView.vue')
            },
            {
                path: 'fornecedores',
                name: 'companySuppliers',
                component: () => import('@/views/company/SupplierView.vue')
            },
            {
                path: 'compras',
                name: 'companyPurchases',
                component: () => import('@/views/company/PurchaseView.vue')
            },
            {
                path: 'depositos',
                name: 'companyWarehouses',
                component: () => import('@/views/company/WarehouseView.vue')
            },
            {
                path: 'comandas',
                name: 'companyTabs',
                component: () => import('@/views/company/TabView.vue')
            },
            {
                path: 'despesas',
                name: 'companyExpenses',
                component: () => import('@/views/company/ExpenseView.vue')
            },
            {
                path: 'financeiro',
                name: 'companyFinancial',
                component: () => import('@/views/company/FinancialView.vue')
            }
        ]
    }
];
