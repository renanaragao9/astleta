<script setup lang="ts">
import { ref } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useToast } from 'primevue/usetoast';
import { isValidEmail, isStrongPassword, passwordsMatch } from '@/validation/auth/registerValidators';
import { ResetPasswordService } from '@/services/auth/resetPasswordService';
import { getErrorMessage } from '@/utils/errorUtils';

const email = ref('');
const token = ref('');
const password = ref('');
const confirmPassword = ref('');
const loading = ref(false);

const router = useRouter();
const route = useRoute();
const toast = useToast();

email.value = (route.query.email as string) || '';
token.value = (route.query.token as string) || '';

if (!email.value || !token.value) {
    toast.add({
        severity: 'error',
        summary: 'Erro',
        detail: 'Link de redefinição inválido. Solicite um novo link.',
        life: 5000
    });
    router.push({ name: 'forgotPassword' });
}

const handleResetPassword = async () => {
    if (!isValidEmail(email.value)) {
        toast.add({
            severity: 'error',
            summary: 'Erro de validação',
            detail: 'Por favor, insira um email válido.',
            life: 3000
        });
        return;
    }

    if (!isStrongPassword(password.value)) {
        toast.add({
            severity: 'error',
            summary: 'Erro de validação',
            detail: 'A senha deve ter pelo menos 8 caracteres, incluindo maiúsculas, minúsculas, números e símbolos.',
            life: 3000
        });
        return;
    }

    if (!passwordsMatch(password.value, confirmPassword.value)) {
        toast.add({
            severity: 'error',
            summary: 'Erro de validação',
            detail: 'As senhas não coincidem.',
            life: 3000
        });
        return;
    }

    if (!token.value) {
        toast.add({
            severity: 'error',
            summary: 'Erro de validação',
            detail: 'Token de redefinição é obrigatório.',
            life: 3000
        });
        return;
    }

    loading.value = true;
    try {
        const result = await ResetPasswordService.resetPassword({
            email: email.value,
            token: token.value,
            password: password.value,
            password_confirmation: confirmPassword.value
        });
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
            detail: getErrorMessage(error, 'Erro ao redefinir senha.'),
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
                    <img src="/image/logo.png" alt="SeuRacha Logo" class="mb-4 w-56 h-56 object-contain mx-auto rounded-lg" />
                    <div class="text-surface-900 dark:text-surface-0 text-3xl font-medium mb-4">Redefinir Senha</div>
                    <span class="text-muted-color font-medium">Redefina sua senha para continuar</span>
                </div>

                <div>
                    <label for="password" class="block text-surface-900 dark:text-surface-0 text-xl font-medium mb-2">Nova Senha</label>
                    <Password id="password" v-model="password" placeholder="Nova senha" :toggleMask="true" class="mb-4" fluid :feedback="true" promptLabel="Escolha uma senha" weakLabel="Fraca" mediumLabel="Média" strongLabel="Forte" />

                    <label for="confirmPassword" class="block text-surface-900 dark:text-surface-0 text-xl font-medium mb-2">Confirmar Senha</label>
                    <Password id="confirmPassword" v-model="confirmPassword" placeholder="Confirmar nova senha" :toggleMask="true" class="mb-8" fluid :feedback="false" />

                    <Button label="Redefinir Senha" class="w-full" :loading="loading" @click="handleResetPassword" />

                    <div class="text-center mt-4 pb-10">
                        <Button label="Voltar ao Login" link class="font-medium text-primary p-0" @click="goToLogin" />
                    </div>
                </div>
            </div>
        </div>
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
