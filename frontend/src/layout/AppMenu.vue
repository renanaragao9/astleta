<script setup lang="ts">
import { computed, watch } from 'vue';
import { useRouter } from 'vue-router';
import AppMenuItem from '@/layout/AppMenuItem.vue';
import { useAuthStore } from '@/stores/auth/loginStore';
import { menuAthlete } from '@/constants/menu/menuAthlete';
import { menuCompany } from '@/constants/menu/menuCompany';

const authStore = useAuthStore();

const router = useRouter();

watch(
    () => authStore.user?.profile?.name,
    (newProfile) => {
        if (newProfile && newProfile !== 'athlete' && newProfile !== 'company') {
            router.push({ name: 'access-denied' });
        }
    }
);

const model = computed(() => {
    const profile = authStore.user?.profile?.name;
    if (profile === 'athlete') {
        return menuAthlete;
    }
    if (profile === 'company') {
        return menuCompany;
    }
    return [
        {
            label: 'Home',
            items: [{ label: 'Painel', icon: 'pi pi-fw pi-home', to: 'home' }]
        }
    ];
});
</script>

<template>
    <ul class="layout-menu">
        <template v-for="(item, i) in model" :key="item.label + i">
            <app-menu-item :item="item" :index="i" :root="true" />
        </template>
    </ul>
</template>

<style lang="scss" scoped></style>
