<script setup lang="ts">
import { ref, inject } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Popover from 'primevue/popover';
import { useAuthStore } from '@/stores/auth/loginStore';
import Notification from '@/components/Notification.vue';

const router = useRouter();
const route = useRoute();
const mobileMenuOpen = ref(false);
const userMenuOverlay = ref();
const imageError = ref(false);

const authStore = useAuthStore();

const searchQuery = inject('searchQuery', ref(''));
const showSearchInTopbar = inject('showSearchInTopbar', ref(false));
const handleSearchFromParent = inject('handleSearch', () => {});

const navigateToLogin = () => {
    router.push({ name: 'login' });
};

const navigateToRegister = () => {
    router.push({ name: 'register' });
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
};

const navigateToBookings = () => {
    router.push({ name: 'athleteBookings' });
    userMenuOverlay.value?.hide();
};

const navigateToOrders = () => {
    router.push({ name: 'athleteTabs' });
    userMenuOverlay.value?.hide();
};

const navigateToCompanyBookings = () => {
    router.push({ name: 'companyBookings' });
    userMenuOverlay.value?.hide();
};

const navigateToCompanyTabs = () => {
    router.push({ name: 'companyTabs' });
    userMenuOverlay.value?.hide();
};

const navigateToHome = () => {
    router.push({ name: 'home' });
};

const navigateToFields = () => {
    router.push({ name: 'fields-index' });
};

const navigateToAdvertiseField = () => {
    router.push({ name: 'advertise-field' });
};

const navigateToHowItWorks = () => {
    router.push({ name: 'how-it-works' });
};

const navigateToBAA = () => {
    router.push({ name: 'baa' });
};

const toggleUserMenu = (event: Event) => {
    userMenuOverlay.value?.toggle(event);
};

const handleLogout = async () => {
    await authStore.logout();
    router.push({ name: 'home' });
    userMenuOverlay.value?.hide();
};

const handleImageError = () => {
    imageError.value = true;
};

const handleSearch = () => {
    handleSearchFromParent();
};
</script>

<template>
    <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-50">
        <div class="mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <div class="flex items-center space-x-8">
                    <div class="flex-shrink-0 cursor-pointer" @click="navigateToHome">
                        <div class="flex-shrink-0 cursor-pointer">
                            <div class="flex items-center space-x-2">
                                <i class="pi pi-shield text-primary-500 dark:text-primary-400" style="font-size: 1.5rem"></i>
                                <span class="text-2xl font-bold text-gray-900 dark:text-gray-100">SeuRacha</span>
                            </div>
                        </div>
                    </div>

                    <nav class="hidden md:flex space-x-8">
                        <button
                            type="button"
                            class="bg-transparent border-0 p-0"
                            @click="navigateToHome"
                            :class="route.name === 'home' ? 'text-primary font-medium border-b-2 border-primary-500' : 'text-gray-700 hover:text-gray-900 font-medium transition-colors cursor-pointer'"
                        >
                            Início
                        </button>
                        <button
                            type="button"
                            class="bg-transparent border-0 p-0"
                            @click="navigateToBAA"
                            :class="route.name === 'baa' ? 'text-primary font-medium border-b-2 border-primary-500' : 'text-gray-700 hover:text-gray-900 font-medium transition-colors cursor-pointer'"
                        >
                            BAA - Boletim do Atleta Amador
                        </button>
                        <button
                            type="button"
                            class="bg-transparent border-0 p-0"
                            @click="navigateToHowItWorks"
                            :class="route.name === 'how-it-works' ? 'text-primary font-medium border-b-2 border-primary-500' : 'text-gray-700 hover:text-gray-900 font-medium transition-colors cursor-pointer'"
                        >
                            Como Funciona
                        </button>
                        <button
                            type="button"
                            class="bg-transparent border-0 p-0"
                            @click="navigateToAdvertiseField"
                            :class="route.name === 'advertise-field' ? 'text-primary font-medium border-b-2 border-primary-500' : 'text-gray-700 hover:text-gray-900 font-medium transition-colors cursor-pointer'"
                        >
                            Anunciar seu campo
                        </button>
                    </nav>
                </div>

                <div v-show="showSearchInTopbar" class="hidden lg:flex flex-1 max-w-lg mx-8 transition-all duration-300">
                    <div class="w-full relative">
                        <div class="flex items-center bg-white border border-gray-300 rounded-full shadow-md hover:shadow-lg transition-shadow">
                            <div class="flex-1 px-6 py-3">
                                <InputText v-model="searchQuery" placeholder="Buscar campos, localização..." class="w-full border-none outline-none text-sm" @keyup.enter="handleSearch" />
                            </div>
                            <div class="pr-2">
                                <Button icon="pi pi-search" class="!bg-primary !border-primary !w-12 !h-12 !rounded-full flex items-center justify-center hover:!bg-primary transition-colors" @click="handleSearch" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    <div class="layout-config-menu">
                        <Notification class="topbar-notification" />
                    </div>

                    <div v-if="authStore.isAuthenticated" class="layout-topbar-action">
                        <button @click="navigateToProfile" type="button" class="layout-topbar-action hover:bg-gray-50 transition-colors rounded-lg p-2">
                            <i :class="authStore.user?.profile?.name === 'company' ? 'pi pi-building topbar-profile-icon text-gray-600 dark:text-gray-300' : 'pi pi-user topbar-profile-icon text-gray-600 dark:text-gray-300'"></i>
                        </button>
                    </div>

                    <div class="relative">
                        <Button v-if="!authStore.isAuthenticated" class="!bg-white !border-gray-300 !text-gray-700 flex items-center space-x-2 !px-4 !py-2 !rounded-full hover:!shadow-md transition-shadow" @click="toggleUserMenu($event)">
                            <i class="pi pi-bars text-sm"></i>
                            <i class="pi pi-user-plus text-sm"></i>
                        </Button>
                        <div v-else class="flex items-center space-x-2 cursor-pointer" @click="toggleUserMenu($event)">
                            <img v-if="authStore.user?.imagePath && !imageError" :src="authStore.user.imagePath" alt="Avatar" class="w-8 h-8 rounded-full object-cover" @error="handleImageError" />
                            <div v-else class="w-8 h-8 bg-primary rounded-full flex items-center justify-center">
                                <i class="pi pi-user text-white text-sm"></i>
                            </div>
                            <span class="hidden md:inline text-sm font-medium text-gray-900">{{ authStore.user?.name }}</span>
                            <i class="pi pi-chevron-down text-gray-500 text-sm"></i>
                        </div>

                        <Popover ref="userMenuOverlay" :style="{ width: '250px' }">
                            <div v-if="!authStore.isAuthenticated" class="flex flex-col space-y-1">
                                <button @click="navigateToRegister" class="text-left px-4 py-3 text-sm font-medium text-gray-900 hover:bg-gray-50 rounded-lg transition-colors">Cadastrar-se</button>
                                <button @click="navigateToLogin" class="text-left px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">Entrar</button>
                                <hr class="my-2 border-gray-200" />
                                <button @click="navigateToAdvertiseField" class="text-left px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">Anunciar seu campo</button>
                                <button @click="navigateToHowItWorks" class="text-left px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">Como Funciona</button>
                                <button @click="navigateToBAA" class="text-left px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">BAA - Boletim do Atleta Amador</button>
                            </div>
                            <div v-else-if="authStore.user?.profile?.name !== 'company'" class="flex flex-col space-y-1">
                                <button @click="navigateToProfile" class="text-left px-4 py-3 text-sm font-medium text-gray-900 hover:bg-gray-50 rounded-lg transition-colors"><i class="pi pi-fw pi-user"></i> Perfil</button>
                                <button @click="navigateToBookings" class="text-left px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition-colors"><i class="pi pi-fw pi-calendar"></i> Minhas Reservas</button>
                                <button @click="navigateToOrders" class="text-left px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition-colors"><i class="pi pi-fw pi-receipt"></i> Minhas Comandas</button>
                                <button @click="handleLogout" class="text-left px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition-colors"><i class="pi pi-fw pi-sign-out"></i> Sair</button>
                                <hr class="my-2 border-gray-200" />
                                <button @click="navigateToAdvertiseField" class="text-left px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">Anunciar seu campo</button>
                                <button @click="navigateToHowItWorks" class="text-left px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">Como Funciona</button>
                                <button @click="navigateToBAA" class="text-left px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">BAA - Boletim do Atleta Amador</button>
                            </div>
                            <div v-else class="flex flex-col space-y-1">
                                <button @click="navigateToProfile" class="text-left px-4 py-3 text-sm font-medium text-gray-900 hover:bg-gray-50 rounded-lg transition-colors"><i class="pi pi-fw pi-compass"></i> Painel Principal</button>
                                <button @click="navigateToCompanyBookings" class="text-left px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition-colors"><i class="pi pi-fw pi-calendar"></i> Reservas</button>
                                <button @click="navigateToCompanyTabs" class="text-left px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition-colors"><i class="pi pi-fw pi-receipt"></i> Comandas</button>
                                <button @click="handleLogout" class="text-left px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition-colors"><i class="pi pi-fw pi-sign-out"></i> Sair</button>
                                <hr class="my-2 border-gray-200" />
                                <button @click="navigateToAdvertiseField" class="text-left px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">Anunciar seu campo</button>
                                <button @click="navigateToHowItWorks" class="text-left px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">Como Funciona</button>
                                <button @click="navigateToBAA" class="text-left px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">BAA - Boletim do Atleta Amador</button>
                            </div>
                        </Popover>
                    </div>
                </div>
            </div>

            <div v-if="mobileMenuOpen" class="md:hidden border-t border-gray-200 pb-4">
                <div v-show="showSearchInTopbar" class="px-4 py-4 transition-all duration-300">
                    <div class="flex items-center bg-gray-100 rounded-full">
                        <div class="flex-1 px-4 py-2">
                            <InputText v-model="searchQuery" placeholder="Buscar campos..." class="w-full border-none outline-none bg-transparent text-sm" @keyup.enter="handleSearch" />
                        </div>
                        <Button icon="pi pi-search" class="!bg-primary-500 !border-primary-500 !w-10 !h-10 !rounded-full mr-2" @click="handleSearch" />
                    </div>
                </div>

                <nav class="px-4 py-2 space-y-2">
                    <button type="button" class="block w-full text-left px-4 py-3 text-primary-500 bg-primary-50 rounded-lg font-medium" @click="navigateToFields">Campos</button>
                    <button type="button" class="block w-full text-left px-4 py-3 text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-lg font-medium transition-colors cursor-pointer" @click="navigateToHowItWorks">Como Funciona</button>
                    <button type="button" class="block w-full text-left px-4 py-3 text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-lg font-medium transition-colors cursor-pointer" @click="navigateToBAA">
                        BAA - Boletim do Atleta Amador
                    </button>
                    <button type="button" class="block w-full text-left px-4 py-3 text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-lg font-medium transition-colors cursor-pointer" @click="navigateToAdvertiseField">Seja Parceiro</button>

                    <hr class="my-4 border-gray-200" />

                    <template v-if="!authStore.isAuthenticated">
                        <button @click="navigateToRegister" class="block w-full text-left px-4 py-3 text-gray-900 hover:bg-gray-50 rounded-lg font-medium transition-colors">Cadastrar-se</button>
                        <button @click="navigateToLogin" class="block w-full text-left px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">Entrar</button>
                    </template>
                    <template v-else-if="authStore.user?.profile?.name !== 'company'">
                        <button @click="navigateToProfile" class="block w-full text-left px-4 py-3 text-gray-900 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg font-medium transition-colors">Perfil</button>
                        <button @click="handleLogout" class="block w-full text-left px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">Sair</button>
                    </template>
                    <template v-else>
                        <button @click="navigateToProfile" class="block w-full text-left px-4 py-3 text-gray-900 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg font-medium transition-colors">
                            <i class="pi pi-fw pi-compass"></i> Painel Principal
                        </button>
                        <button @click="navigateToCompanyBookings" class="block w-full text-left px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
                            <i class="pi pi-fw pi-calendar"></i> Reservas
                        </button>
                        <button @click="navigateToCompanyTabs" class="block w-full text-left px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
                            <i class="pi pi-fw pi-receipt"></i> Comandas
                        </button>
                        <button @click="handleLogout" class="block w-full text-left px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">Sair</button>
                    </template>
                </nav>
            </div>
        </div>
    </header>
</template>

<style scoped>
:deep(.p-inputtext) {
    border: none !important;
    box-shadow: none !important;
    background: transparent !important;
}

:deep(.p-inputtext:focus) {
    border: none !important;
    box-shadow: none !important;
}

:deep(.p-button) {
    border: 1px solid #d1d5db !important;
    transition: all 0.2s ease !important;
}

:deep(.p-button:hover) {
    box-shadow:
        0 4px 6px -1px rgba(0, 0, 0, 0.1),
        0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
}

.transition-colors {
    transition: color 0.2s ease;
}

.transition-shadow {
    transition: box-shadow 0.2s ease;
}

.topbar-notification :deep(.pi-bell) {
    font-size: 1.5rem !important;
}

.topbar-profile-icon {
    font-size: 1.5rem !important;
}

.topbar-configurator :deep(.pi-palette) {
    font-size: 1.5rem !important;
}
</style>
