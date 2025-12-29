import type { MenuItem } from '@/types/menu/menu';

export const menuAthlete: MenuItem[] = [
    {
        label: 'Início',
        items: [
            {
                label: 'Meu Perfil',
                icon: 'pi pi-fw pi-user',
                to: { name: 'athleteProfile' }
            },
            {
                label: 'Minhas Reservas',
                icon: 'pi pi-fw pi-calendar',
                to: { name: 'athleteBookings' }
            },
            {
                label: 'Minhas Comandas',
                icon: 'pi pi-fw pi-receipt',
                to: { name: 'athleteTabs' }
            },
            {
                label: 'Meus Rachas',
                icon: 'pi pi-bookmark',
                to: { name: 'athleteRachas' }
            },
            {
                label: 'Meu Time',
                icon: 'pi pi-shield',
                to: { name: 'athleteTeams' }
            }
        ]
    }
];
