<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useAuthStore } from '@/stores/auth/loginStore';
import { useRouter, useRoute } from 'vue-router';
import { useToast } from 'primevue/usetoast';
import Message from 'primevue/message';
import { isValidEmail, isValidPassword } from '@/validation/auth/loginValidators';
import { homeByProfile } from '@/router/types';
import { getErrorMessage } from '@/utils/errorUtils';

interface BeforeInstallPromptEvent extends Event {
    prompt(): Promise<void>;
    userChoice: Promise<{ outcome: 'accepted' | 'dismissed' }>;
}

const email = ref('');
const password = ref('');
const checked = ref(false);
const loading = ref(false);
const errorMessage = ref('');

const deferredPrompt = ref<BeforeInstallPromptEvent | null>(null);
const showInstallButton = ref(false);

const authStore = useAuthStore();
const router = useRouter();
const route = useRoute();
const toast = useToast();

const handleLogin = async () => {
    errorMessage.value = '';
    if (!isValidEmail(email.value)) {
        toast.add({
            severity: 'error',
            summary: 'Erro de validação',
            detail: 'Por favor, insira um email válido.',
            life: 3000
        });
        return;
    }

    if (!isValidPassword(password.value)) {
        toast.add({
            severity: 'error',
            summary: 'Erro de validação',
            detail: 'A senha deve ter pelo menos 6 caracteres.',
            life: 3000
        });
        return;
    }

    loading.value = true;
    const result = await authStore.login({ email: email.value, password: password.value });
    loading.value = false;

    if (result.success) {
        toast.add({ severity: 'success', summary: 'Sucesso', detail: 'Login realizado com sucesso!', life: 3000 });

        const redirectPath = route.query.redirect as string;
        if (redirectPath && typeof redirectPath === 'string') {
            router.push(redirectPath);
            return;
        }

        const userProfile = authStore.user?.profile?.name;
        let redirectRoute = homeByProfile.company;

        if (userProfile === 'athlete') {
            redirectRoute = homeByProfile.athlete;
        } else if (userProfile === 'admin') {
            redirectRoute = homeByProfile.admin;
        }

        router.push(redirectRoute);
    } else {
        const err = authStore.error;
        if (err) {
            toast.add({
                severity: 'error',
                summary: 'Erro',
                detail: getErrorMessage(err, 'Erro no login'),
                life: 3000
            });
            errorMessage.value = err.errors?.password?.[0] || err.message;
        }
    }
};

const goToRegister = () => {
    router.push({ name: 'register' });
};

onMounted(() => {
    window.addEventListener('beforeinstallprompt', (e: Event) => {
        e.preventDefault();
        deferredPrompt.value = e as BeforeInstallPromptEvent;
        showInstallButton.value = true;
    });

    window.addEventListener('appinstalled', () => {
        showInstallButton.value = false;
        deferredPrompt.value = null;
    });
});

const installPWA = async () => {
    if (deferredPrompt.value) {
        deferredPrompt.value.prompt();
        const { outcome } = await deferredPrompt.value.userChoice;
        if (outcome === 'accepted') {
            showInstallButton.value = false;
        }
        deferredPrompt.value = null;
    }
};
</script>

<template>
    <div class="w-full flex flex-col md:items-center">
        <div style="border-radius: 56px; padding: 0.3rem; margin-top: 3rem; background: linear-gradient(180deg, var(--primary-color) 10%, rgba(33, 150, 243, 0) 30%)">
            <div class="w-full md:w-96 lg:w-[42rem] bg-surface-0 dark:bg-surface-900 px-4 sm:px-20" style="border-radius: 53px">
                <div class="text-center mb-8">
                    <img src="/image/logo.png" alt="Astleta Logo" class="mb-4 w-56 h-56 object-contain mx-auto rounded-lg" />
                    <div class="text-surface-900 dark:text-surface-0 text-3xl font-medium mb-4">Bem-vindo ao Astleta!</div>
                    <span class="text-muted-color font-medium">Faça login para continuar</span>
                </div>

                <form @submit.prevent="handleLogin">
                    <label for="email1" class="block text-surface-900 dark:text-surface-0 text-xl font-medium mb-2">Email</label>
                    <InputText id="email1" class="w-full mb-8" type="email" placeholder="Endereço de email" v-model="email" />

                    <label for="password1" class="block text-surface-900 dark:text-surface-0 font-medium text-xl mb-2">Senha</label>
                    <Password id="password1" class="mb-4" type="password" v-model="password" placeholder="Senha" :toggleMask="true" fluid :feedback="false"></Password>

                    <div class="flex items-center justify-between mt-2 mb-8 gap-8">
                        <div class="flex items-center">
                            <Checkbox v-model="checked" id="rememberme1" binary class="mr-2"></Checkbox>
                            <label for="rememberme1">Lembrar-me</label>
                        </div>
                        <router-link :to="{ name: 'forgotPassword' }" class="font-medium no-underline ml-2 text-right cursor-pointer text-primary">Esqueceu a senha?</router-link>
                    </div>
                    <Message severity="error" v-if="errorMessage" class="mb-2" icon="pi pi-exclamation-triangle">{{ errorMessage }}</Message>
                    <Button label="Entrar" type="submit" class="w-full" :loading="loading"></Button>

                    <div class="text-center mt-4 pb-10">
                        <span class="text-muted-color">Não tem conta? </span>
                        <Button label="Registre-se" link class="font-medium text-primary p-0" @click="goToRegister" />
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div v-if="showInstallButton" class="fixed bottom-4 right-4 z-50">
        <button @click="installPWA" class="bg-primary text-white px-4 py-2 rounded-lg shadow-lg hover:bg-primary-600 transition-colors flex items-center gap-2">
            <i class="pi pi-download"></i>
            Instalar App
        </button>
    </div>
</template>

<style scoped>
.pi-eye {
    transform: scale(1.6);
    margin-right: 1rem;
}

.pi-eye-slash {
    transform: scale(1.6);
    margin-right: 1rem;
}
</style>
