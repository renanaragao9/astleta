<script setup lang="ts">
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth/loginStore';
import Button from 'primevue/button';

const router = useRouter();
const authStore = useAuthStore();

const goToDashboard = () => {
    const userProfile = authStore.user?.profile.name;
    if (userProfile === 'company') {
        router.push({ name: 'company-dashboard' });
    } else if (userProfile === 'athlete') {
        router.push({ name: 'athlete-dashboard' });
    } else {
        router.push({ name: 'dashboard' });
    }
};
</script>

<template>
    <div class="flex align-items-center justify-content-center min-h-screen">
        <div class="text-center">
            <h1 class="text-6xl font-bold text-red-500 mb-4">403</h1>
            <h2 class="text-2xl font-semibold mb-4">Acesso Negado</h2>
            <p class="text-gray-600 mb-6">Você não tem permissão para acessar esta página.</p>
            <Button label="Voltar ao Dashboard" @click="goToDashboard" class="p-button-primary" />
        </div>
    </div>
</template>

<style scoped>
.min-h-screen {
    min-height: 100vh;
}
</style>
