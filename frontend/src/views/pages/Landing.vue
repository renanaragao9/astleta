<script setup lang="ts">
import { useAuthStore } from '@/stores/auth/loginStore';
import { useRouter } from 'vue-router';

const authStore = useAuthStore();
const router = useRouter();

const logout = async () => {
    await authStore.logout();
    router.push({ name: 'login' });
};
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-blue-500 to-indigo-700 flex flex-col items-center justify-center text-white">
        <header class="mb-10 text-center">
            <h1 class="text-5xl font-bold mb-4">Astleta</h1>
            <p class="text-xl max-w-2xl mx-auto">Acompanhe o futebol de forma simples, divertida e moderna. Resultados, estatísticas e muito mais em um só lugar!</p>
        </header>
        <main class="flex flex-col items-center w-full max-w-2xl">
            <section class="bg-white bg-opacity-10 rounded-xl p-8 shadow-lg mb-8 w-full">
                <h2 class="text-2xl font-semibold mb-4">Por que usar o astleta?</h2>
                <ul class="list-disc list-inside text-lg space-y-2 text-white text-opacity-90">
                    <li>Acesso rápido aos resultados dos jogos</li>
                    <li>Estatísticas detalhadas dos times e jogadores</li>
                    <li>Interface intuitiva e responsiva</li>
                    <li>Atualizações em tempo real</li>
                </ul>

                <button type="button" class="p-link flex align-items-center px-3 py-2 hover:bg-gray-100" @click="logout">
                    <i class="pi pi-sign-out mr-2"></i>
                    <span>Sair</span>
                </button>
            </section>
            <a href="#" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 px-8 rounded transition text-lg shadow-lg">Comece Agora</a>
        </main>
        <footer class="mt-10 text-white text-opacity-70 text-sm">&copy; 2025 Astleta. Todos os direitos reservados.</footer>
    </div>
</template>
