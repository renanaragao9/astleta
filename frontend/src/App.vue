<script setup lang="ts">
import { onMounted } from 'vue';
import { useAuthStore } from '@/stores/auth/loginStore';
import Toast from 'primevue/toast';

const authStore = useAuthStore();

onMounted(() => {
    authStore.initializeAuth();
});
</script>

<template>
    <router-view />
    <Toast />
</template>

<style scoped></style>
