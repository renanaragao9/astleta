<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useToast } from 'primevue/usetoast';
import { isValidEmail } from '@/validation/auth/loginValidators';
import { ForgotPasswordService } from '@/services/auth/forgotPasswordService';
import { getErrorMessage } from '@/utils/errorUtils';

const email = ref('');
const loading = ref(false);

const router = useRouter();
const toast = useToast();

const handleForgotPassword = async () => {
    if (!isValidEmail(email.value)) {
        toast.add({
            severity: 'error',
            summary: 'Erro de validação',
            detail: 'Por favor, insira um email válido.',
            life: 3000
        });
        return;
    }

    loading.value = true;
    try {
        const result = await ForgotPasswordService.forgotPassword(email.value);
        toast.add({
            severity: 'success',
            summary: 'Sucesso',
            detail: result.message,
            life: 5000
        });
        router.push({ name: 'login' });
    } catch (error) {
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: getErrorMessage(error, 'Erro ao enviar solicitação de redefinição de senha.'),
            life: 5000
        });
    } finally {
        loading.value = false;
    }
};

const goToLogin = () => {
    router.push({ name: 'login' });
};
</script>

<template>
    <div class="w-full flex flex-col md:items-center">
        <div style="border-radius: 56px; padding: 0.3rem; margin-top: 3rem; background: linear-gradient(180deg, var(--primary-color) 10%, rgba(33, 150, 243, 0) 30%)">
            <div class="w-full md:w-96 lg:w-[42rem] bg-surface-0 dark:bg-surface-900 px-4 sm:px-20" style="border-radius: 53px">
                <div class="text-center mb-8">
                    <img src="/image/logo.png" alt="Astleta Logo" class="mb-4 w-56 h-56 object-contain mx-auto rounded-lg" />
                    <div class="text-surface-900 dark:text-surface-0 text-3xl font-medium mb-4">Esqueceu sua senha?</div>
                    <span class="text-muted-color font-medium">Digite seu email para redefinir</span>
                </div>

                <div>
                    <label for="email1" class="block text-surface-900 dark:text-surface-0 text-xl font-medium mb-2">Email</label>
                    <InputText id="email1" type="text" placeholder="Endereço de email" class="w-full mb-8" v-model="email" />

                    <Button label="Enviar" class="w-full" :loading="loading" @click="handleForgotPassword"></Button>

                    <div class="text-center mt-4 pb-10">
                        <span class="text-muted-color">Lembrou da senha? </span>
                        <Button label="Voltar ao Login" link class="font-medium text-primary p-0" @click="goToLogin" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
