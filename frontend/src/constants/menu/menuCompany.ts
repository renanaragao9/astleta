import type { MenuItem } from '@/types/menu/menu';

export const menuCompany: MenuItem[] = [
    {
        label: 'Explorar',
        items: [
            {
                label: 'Painel Principal',
                icon: 'pi pi-home',
                to: { name: 'companyDashboard' }
            },
            {
                label: 'Reservas',
                icon: 'pi pi-fw pi-calendar',
                to: { name: 'companyBookings' }
            },
            {
                label: 'Comandas',
                icon: 'pi pi-fw pi-receipt',
                to: { name: 'companyTabs' }
            }
        ]
    },
    {
        label: 'Gestão',
        items: [
            {
                label: 'Arenas',
                icon: 'pi pi-fw pi-map',
                to: { name: 'companyFields' }
            },
            {
                label: 'Torneios',
                icon: 'pi pi-fw pi-trophy',
                to: { name: 'companyTournaments' }
            },
            {
                label: 'Despesas',
                icon: 'pi pi-fw pi-money-bill',
                to: { name: 'companyExpenses' }
            },
            {
                label: 'Financeiro',
                icon: 'pi pi-fw pi-wallet',
                to: { name: 'companyFinancial' }
            }
        ]
    },
    {
        label: 'Estoque',
        items: [
            {
                label: 'Produtos',
                icon: 'pi pi-fw pi-box',
                to: { name: 'companyProducts' }
            },
            {
                label: 'Fornecedores',
                icon: 'pi pi-fw pi-truck',
                to: { name: 'companySuppliers' }
            },
            {
                label: 'Armazéns',
                icon: 'pi pi-warehouse',
                to: { name: 'companyWarehouses' }
            },
            {
                label: 'Compras',
                icon: 'pi pi-fw pi-shopping-cart',
                to: { name: 'companyPurchases' }
            }
        ]
    }
];
