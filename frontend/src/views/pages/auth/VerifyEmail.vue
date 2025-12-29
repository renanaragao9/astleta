<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { useRegisterStore } from '@/stores/auth/registerStore';
import { useRouter, useRoute } from 'vue-router';
import { useToast } from 'primevue/usetoast';
import InputOtp from 'primevue/inputotp';
import { isValidEmail } from '@/validation/auth/verifyValidators';
import { getErrorMessage } from '@/utils/errorUtils';

const email = ref('');
const verificationCode = ref('');
const isResending = ref(false);
const resendTimer = ref(0);

const registerStore = useRegisterStore();
const router = useRouter();
const route = useRoute();
const toast = useToast();

const maskedEmail = computed(() => {
    if (!email.value) return '';
    const [local, domain] = email.value.split('@');
    if (local.length <= 2) return email.value;
    return `${local[0]}${'*'.repeat(local.length - 2)}${local[local.length - 1]}@${domain}`;
});

onMounted(() => {
    const emailParam = route.query.email as string;
    if (emailParam) {
        email.value = emailParam;
    }
});

const handleVerifyEmail = async () => {
    if (!email.value || !verificationCode.value) {
        toast.add({
            severity: 'error',
            summary: 'Erro de validação',
            detail: 'Email e código de verificação são obrigatórios.',
            life: 3000
        });
        return;
    }

    if (!isValidEmail(email.value)) {
        toast.add({
            severity: 'error',
            summary: 'Erro de validação',
            detail: 'Por favor, insira um email válido.',
            life: 3000
        });
        return;
    }

    const result = await registerStore.verifyEmail(email.value, verificationCode.value);

    if (result.success) {
        toast.add({
            severity: 'success',
            summary: 'Sucesso',
            detail: `${result.message}. Você será redirecionado em breve.`,
            life: 3000
        });

        setTimeout(() => {
            router.push({ name: 'login' });
        }, 2000);
    } else {
        const err = registerStore.error;
        if (err) {
            toast.add({
                severity: 'error',
                summary: 'Erro',
                detail: getErrorMessage(err, 'Erro na verificação'),
                life: 3000
            });
        }
    }
};

const handleResendCode = async () => {
    if (!email.value) {
        toast.add({
            severity: 'error',
            summary: 'Erro de validação',
            detail: 'Email é obrigatório.',
            life: 3000
        });
        return;
    }

    if (!isValidEmail(email.value)) {
        toast.add({
            severity: 'error',
            summary: 'Erro de validação',
            detail: 'Por favor, insira um email válido.',
            life: 3000
        });
        return;
    }

    isResending.value = true;
    const result = await registerStore.resendVerificationCode(email.value);
    isResending.value = false;

    if (result.success) {
        toast.add({
            severity: 'success',
            summary: 'Sucesso',
            detail: result.message,
            life: 3000
        });
        resendTimer.value = 60;
        const interval = setInterval(() => {
            resendTimer.value--;
            if (resendTimer.value <= 0) {
                clearInterval(interval);
            }
        }, 1000);
    } else {
        const err = registerStore.error;
        if (err) {
            toast.add({
                severity: 'error',
                summary: 'Erro',
                detail: getErrorMessage(err, 'Erro ao reenviar código'),
                life: 3000
            });
        }
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
                <div class="w-full bg-surface-0 dark:bg-surface-900 py-20 px-8 sm:px-20" style="border-radius: 53px">
                    <div class="text-center mb-8">
                        <img src="/image/logo.png" alt="Astleta Logo" class="mb-4 w-24 h-24 object-contain mx-auto rounded-lg" />
                        <div class="text-surface-900 dark:text-surface-0 text-3xl font-medium mb-4">Verificação de Email</div>
                        <span class="text-muted-color font-medium">Digite o código enviado para seu email</span>
                    </div>

                    <div>
                        <div class="hidden">
                            <label for="email" class="block text-surface-900 dark:text-surface-0 text-xl font-medium mb-2">Email</label>
                            <div id="email" class="w-full md:w-[30rem] mb-6 px-3 py-2 rounded border border-gray-300 bg-gray-100 dark:bg-gray-800 text-surface-900 dark:text-surface-0 text-base cursor-not-allowed select-text">
                                {{ maskedEmail }}
                            </div>
                        </div>

                        <label for="verificationCode" class="block text-surface-900 dark:text-surface-0 font-medium text-xl mb-2">Código de verificação</label>
                        <InputOtp v-model="verificationCode" :length="6" class="w-full md:w-[30rem] mb-6 mx-auto" />

                        <div class="space-y-4">
                            <Button label="Verificar Email" class="w-full" :loading="registerStore.isLoading" @click="handleVerifyEmail" />

                            <div class="text-center">
                                <span class="text-muted-color">Não recebeu o código? </span>
                                <Button v-if="resendTimer === 0" label="Reenviar" link class="font-medium text-primary p-0" :loading="isResending" @click="handleResendCode" />
                                <span v-else class="font-medium text-muted-color">Reenviar em {{ resendTimer }}s</span>
                            </div>

                            <div class="text-center">
                                <Button label="Voltar ao login" link class="font-medium text-primary p-0" @click="goToLogin" />
                            </div>
                        </div>
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
