<script setup lang="ts">
import { useLayout } from '@/layout/composables/layout';
import { Ref, ref } from 'vue';
import { useAuthStore } from '@/stores/auth/loginStore';
import { useRouter } from 'vue-router';
import Popover from 'primevue/popover';
import Notification from '@/components/Notification.vue';

interface LayoutContext {
    toggleMenu: () => void;
    toggleDarkMode: () => void;
    isDarkTheme: Ref<boolean>;
}

const { toggleMenu, toggleDarkMode, isDarkTheme } = useLayout() as LayoutContext;
const authStore = useAuthStore();
const router = useRouter();

const profileOverlay = ref<InstanceType<typeof Popover>>();
const imageError = ref(false);

const toggleProfileMenu = (event: Event) => {
    profileOverlay.value?.toggle(event);
};

const logout = async () => {
    await authStore.logout();
    router.push({ name: 'login' });
    profileOverlay.value?.hide();
};

const navigateToProfile = () => {
    const profile = authStore.user?.profile?.name;
    if (profile === 'athlete') {
        router.push({ name: 'athleteProfile' });
    } else if (profile === 'company') {
        router.push({ name: 'companyDashboard' });
    } else if (profile === 'admin') {
        router.push({ name: 'crud' });
    } else {
        router.push({ name: 'home' });
    }
    profileOverlay.value?.hide();
};

const navigateToBookings = () => {
    router.push({ name: 'athleteBookings' });
    profileOverlay.value?.hide();
};

const navigateToOrders = () => {
    router.push({ name: 'athleteTabs' });
    profileOverlay.value?.hide();
};

const navigateToCompanyBookings = () => {
    router.push({ name: 'companyBookings' });
    profileOverlay.value?.hide();
};

const navigateToCompanyTabs = () => {
    router.push({ name: 'companyTabs' });
    profileOverlay.value?.hide();
};

const navigateToBAA = () => {
    router.push({ name: 'baa' });
};

const handleImageError = () => {
    imageError.value = true;
};

const goHome = () => {
    router.push('/');
};
</script>

<template>
    <div class="layout-topbar">
        <div class="layout-topbar-logo-container">
            <button class="layout-menu-button layout-topbar-action" @click="toggleMenu">
                <i class="pi pi-bars text-gray-600 dark:text-gray-300"></i>
            </button>
            <router-link :to="{ name: 'home' }" class="layout-topbar-logo">
                <div class="flex-shrink-0 cursor-pointer">
                    <div class="flex items-center space-x-2">
                        <i class="pi pi-shield text-primary-500 dark:text-primary-400" style="font-size: 1.5rem"></i>
                        <span class="hidden md:inline text-2xl font-bold text-gray-900 dark:text-gray-100">SeuRacha</span>
                    </div>
                </div>
            </router-link>
        </div>

        <div class="layout-topbar-actions">
            <div class="layout-config-menu">
                <button type="button" class="layout-topbar-action" @click="toggleDarkMode">
                    <i :class="['pi', { 'pi-moon': isDarkTheme, 'pi-sun': !isDarkTheme, 'text-gray-600 dark:text-gray-300': true }]"></i>
                </button>
                <Notification />
            </div>

            <button type="button" class="layout-topbar-action" @click="goHome">
                <i class="pi pi-shop text-gray-600 dark:text-gray-300"></i>
            </button>

            <div v-if="authStore.isAuthenticated" class="flex items-center space-x-2 cursor-pointer" @click="toggleProfileMenu($event)">
                <img v-if="authStore.user?.imagePath && !imageError" :src="authStore.user.imagePath" alt="Avatar" class="w-8 h-8 rounded-full object-cover" @error="handleImageError" />
                <div v-else class="w-8 h-8 bg-primary-500 rounded-full flex items-center justify-center">
                    <i class="pi pi-user text-white text-sm"></i>
                </div>
                <span class="hidden md:inline text-sm font-medium text-gray-700 dark:text-gray-200">{{ authStore.user?.name }}</span>
                <i class="pi pi-chevron-down text-gray-500 dark:text-gray-400 text-sm"></i>
            </div>
            <button v-else type="button" class="layout-topbar-action" @click="toggleProfileMenu($event)">
                <i class="pi pi-user text-gray-600 dark:text-gray-300"></i>
            </button>
        </div>

        <Popover ref="profileOverlay" class="!w-60 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
            <div v-if="!authStore.isAuthenticated" class="flex flex-col space-y-1">
                <button class="text-left px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">Cadastrar-se</button>
                <button class="text-left px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">Entrar</button>
                <hr class="my-2 border-gray-200 dark:border-gray-600" />
                <button class="text-left px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">Anunciar seu campo</button>
                <button class="text-left px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">Centro de ajuda</button>
            </div>
            <div v-else-if="authStore.user?.profile?.name !== 'company'" class="flex flex-col space-y-1">
                <button @click="navigateToProfile" class="text-left px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors"><i class="pi pi-fw pi-user"></i> Perfil</button>
                <button @click="navigateToBookings" class="text-left px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    <i class="pi pi-fw pi-calendar"></i> Minhas Reservas
                </button>
                <button @click="navigateToOrders" class="text-left px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors"><i class="pi pi-fw pi-receipt"></i> Minhas Comandas</button>
                <button @click="logout" class="text-left px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors"><i class="pi pi-fw pi-sign-out"></i> Sair</button>
                <hr class="my-2 border-gray-200 dark:border-gray-600" />
                <button @click="navigateToBAA" class="text-left px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">BAA - Boletim do Atleta Amador</button>
            </div>
            <div v-else class="flex flex-col space-y-1">
                <button @click="navigateToProfile" class="text-left px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    <i class="pi pi-fw pi-compass"></i> Painel Principal
                </button>
                <button @click="navigateToCompanyBookings" class="text-left px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    <i class="pi pi-fw pi-calendar"></i> Reservas
                </button>
                <button @click="navigateToCompanyTabs" class="text-left px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors"><i class="pi pi-fw pi-receipt"></i> Comandas</button>
                <button @click="logout" class="text-left px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors"><i class="pi pi-fw pi-sign-out"></i> Sair</button>
            </div>
        </Popover>
    </div>
</template>
