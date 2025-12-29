<script setup lang="ts">
import { computed } from 'vue';

const appVersion = 'v1.0';
const currentYear = computed(() => new Date().getFullYear());
</script>

<template>
    <div class="layout-footer bg-gray-100 dark:bg-black py-2 flex flex-col items-center justify-center gap-1 text-sm text-gray-600 dark:text-gray-400">
        <p class="text-center my-0">Astleta &copy; {{ currentYear }} - {{ appVersion }}</p>
        <p class="text-center my-0">
            <a href="https://wa.me/5585921674573?text=Olá, preciso de suporte" target="_blank" class="hover:text-primary-500 transition-colors">Clique aqui para obter suporte via WhatsApp</a>
        </p>
        <p class="text-center my-0">
            <a href="mailto:astletacontato@gmail.com" class="hover:text-primary-500 transition-colors">Relatar Bug: astletacontato@gmail.com</a>
        </p>
    </div>
</template>
