<script setup lang="ts">
import { ref, computed } from 'vue';
import { useRegisterStore } from '@/stores/auth/registerStore';
import { useRouter } from 'vue-router';
import { useToast } from 'primevue/usetoast';
import InputMask from 'primevue/inputmask';
import { isValidEmail, isStrongPassword, isValidCpf, isValidPhone, isValidName, isValidBirthDate, passwordsMatch, cleanPhone } from '@/validation/auth/registerValidators';
import type { RegisterRequest } from '@/types/registerType';
import PrivacyTermsModal from '@/components/PrivacyTermsModal.vue';
import { genderOptions } from '@/services/genderOptionsService';

const formatDateToISO = (dateStr: string): string => {
    if (!dateStr) return '';
    const parts = dateStr.split('/');
    if (parts.length !== 3) return '';
    return `${parts[2]}-${parts[1].padStart(2, '0')}-${parts[0].padStart(2, '0')}`;
};

const name = ref('');
const cpf = ref('');
const email = ref('');
const phone = ref('');
const password = ref('');
const confirmPassword = ref('');
const birthDate = ref<string>('');
const gender = ref<string | null>(null);
const acceptTerms = ref(false);
const nameError = ref('');
const cpfError = ref('');
const emailError = ref('');
const phoneError = ref('');
const passwordError = ref('');
const confirmPasswordError = ref('');
const birthDateError = ref('');
const genderError = ref('');
const termsError = ref('');
const registerError = ref('');
const showPrivacyModal = ref(false);
const activePrivacyTab = ref<'terms' | 'privacy'>('terms');

const registerStore = useRegisterStore();
const router = useRouter();
const toast = useToast();

const loading = computed(() => registerStore.isLoading);

const validateForm = (): boolean => {
    nameError.value = '';
    cpfError.value = '';
    emailError.value = '';
    phoneError.value = '';
    passwordError.value = '';
    confirmPasswordError.value = '';
    birthDateError.value = '';
    genderError.value = '';
    termsError.value = '';
    registerError.value = '';

    let hasErrors = false;

    if (!isValidName(name.value)) {
        nameError.value = 'O nome deve ter pelo menos 2 palavras com 2 caracteres cada.';
        hasErrors = true;
    }

    if (cpf.value && !isValidCpf(cpf.value)) {
        cpfError.value = 'Por favor, insira um CPF válido.';
        hasErrors = true;
    }

    if (!isValidEmail(email.value)) {
        emailError.value = 'Por favor, insira um email válido.';
        hasErrors = true;
    }

    if (!isValidPhone(phone.value)) {
        phoneError.value = 'Por favor, insira um telefone válido.';
        hasErrors = true;
    }

    if (!isStrongPassword(password.value)) {
        passwordError.value = 'A senha deve ter pelo menos 8 caracteres, incluindo maiúsculas, minúsculas, números e símbolos.';
        hasErrors = true;
    }

    if (!passwordsMatch(password.value, confirmPassword.value)) {
        confirmPasswordError.value = 'As senhas não coincidem.';
        hasErrors = true;
    }

    if (!birthDate.value || !isValidBirthDate(formatDateToISO(birthDate.value))) {
        birthDateError.value = 'Você deve ter pelo menos 14 anos para se cadastrar.';
        hasErrors = true;
    }

    if (!gender.value) {
        genderError.value = 'Selecione seu gênero.';
        hasErrors = true;
    }

    if (!acceptTerms.value) {
        termsError.value = 'Você deve aceitar os termos de uso para continuar.';
        hasErrors = true;
    }

    return !hasErrors;
};

const handleRegister = async () => {
    if (!validateForm()) {
        return;
    }

    const registerData: RegisterRequest = {
        name: name.value.trim(),
        cpf: cpf.value,
        email: email.value.toLowerCase().trim(),
        phone: cleanPhone(phone.value),
        password: password.value,
        password_confirmation: confirmPassword.value,
        date: formatDateToISO(birthDate.value) || '',
        gender: gender.value as string
    };

    const result = await registerStore.register(registerData);

    if (result.success) {
        toast.add({
            severity: 'success',
            summary: 'Sucesso',
            detail: result.message,
            life: 5000
        });

        router.push({ name: 'verifyEmail', query: { email: email.value } });
    } else {
        registerError.value = result.message;
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: result.message,
            life: 5000
        });
    }
};

const goToLogin = () => {
    router.push({ name: 'login' });
};

const goToHome = () => {
    router.push('/');
};

const openTermsModal = () => {
    activePrivacyTab.value = 'terms';
    showPrivacyModal.value = true;
};

const openPrivacyModal = () => {
    activePrivacyTab.value = 'privacy';
    showPrivacyModal.value = true;
};
</script>

<template>
    <div class="flex items-center justify-center">
        <div class="w-full max-w-none sm:max-w-5xl">
            <div style="border-radius: 56px; padding: 0.3rem; margin-top: 3rem; background: linear-gradient(180deg, var(--primary-color) 10%, rgba(33, 150, 243, 0) 30%)">
                <div class="w-full max-w-none sm:max-w-5xl bg-surface-0 dark:bg-surface-900 px-4 sm:px-10" style="border-radius: 53px">
                    <div class="text-center">
                        <img src="/image/logo.png" alt="SeuRacha Logo" class="w-24 h-24 object-contain mx-auto rounded-lg" />

                        <span class="text-surface-900 dark:text-surface-0 text-3xl font-medium mb-4 block">Cadastre-se no SeuRacha!</span>
                        <span class="text-muted-color font-medium">Crie sua conta para começar</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-5">
                        <div class="md:col-span-1">
                            <label for="name" class="block text-surface-900 dark:text-surface-0 text-xl font-medium mb-2">Nome completo <span class="text-red-500">*</span></label>
                            <InputText id="name" type="text" placeholder="Digite seu nome completo" class="w-full mb-4 py-3" v-model="name" />
                            <span v-if="nameError" class="text-red-500 text-sm">{{ nameError }}</span>
                        </div>

                        <div>
                            <label for="phone" class="block text-surface-900 dark:text-surface-0 text-xl font-medium mb-2">Telefone <span class="text-red-500">*</span></label>
                            <InputMask id="phone" v-model="phone" mask="99 99999-9999" placeholder="00 00000-0000" class="w-full mb-4" type="tel" />
                            <span v-if="phoneError" class="text-red-500 text-sm">{{ phoneError }}</span>
                        </div>

                        <div class="md:col-span-2">
                            <label for="email" class="block text-surface-900 dark:text-surface-0 text-xl font-medium mb-2">Email <span class="text-red-500">*</span></label>
                            <InputText id="email" type="email" placeholder="seu@email.com" class="w-full mb-4" v-model="email" />
                            <span v-if="emailError" class="text-red-500 text-sm">{{ emailError }}</span>
                        </div>

                        <div>
                            <label for="birthDate" class="block text-surface-900 dark:text-surface-0 text-xl font-medium mb-2">Data de nascimento <span class="text-red-500">*</span></label>
                            <InputMask id="birthDate" v-model="birthDate" mask="99/99/9999" placeholder="dd/mm/aaaa" class="w-full mb-4" type="tel" />
                            <span v-if="birthDateError" class="text-red-500 text-sm">{{ birthDateError }}</span>
                        </div>

                        <div>
                            <label for="gender" class="block text-surface-900 dark:text-surface-0 text-xl font-medium mb-2">Gênero <span class="text-red-500">*</span></label>
                            <Select id="gender" v-model="gender" :options="genderOptions" optionLabel="label" optionValue="value" placeholder="Selecione seu gênero" class="w-full mb-4" />
                            <span v-if="genderError" class="text-red-500 text-sm">{{ genderError }}</span>
                        </div>

                        <div>
                            <label for="password" class="block text-surface-900 dark:text-surface-0 font-medium text-xl mb-2">Senha <span class="text-red-500">*</span></label>
                            <Password id="password" v-model="password" placeholder="Digite sua senha" :toggleMask="true" class="mb-4" fluid :feedback="true" promptLabel="Escolha uma senha" weakLabel="Fraca" mediumLabel="Média" strongLabel="Forte" />
                            <span v-if="passwordError" class="text-red-500 text-sm">{{ passwordError }}</span>
                        </div>

                        <div>
                            <label for="confirmPassword" class="block text-surface-900 dark:text-surface-0 font-medium text-xl mb-2">Confirmar senha <span class="text-red-500">*</span></label>
                            <Password id="confirmPassword" v-model="confirmPassword" placeholder="Confirme sua senha" :toggleMask="true" class="mb-4" fluid :feedback="false" />
                            <span v-if="confirmPasswordError" class="text-red-500 text-sm">{{ confirmPasswordError }}</span>
                        </div>

                        <div class="md:col-span-2 flex items-center mt-2 mb-8">
                            <Checkbox v-model="acceptTerms" id="terms" binary class="mr-2"></Checkbox>
                            <label for="terms" class="text-sm">
                                Aceito os
                                <span class="font-medium cursor-pointer text-primary" @click="openTermsModal">termos de uso</span>
                                e
                                <span class="font-medium cursor-pointer text-primary" @click="openPrivacyModal">política de privacidade</span>
                                <span class="text-red-500">*</span>
                            </label>
                        </div>
                        <span v-if="termsError" class="text-red-500 text-sm">{{ termsError }}</span>
                        <span v-if="registerError" class="text-red-500 text-sm">{{ registerError }}</span>

                        <div class="md:col-span-2 space-y-4">
                            <Button label="Criar conta" icon="pi pi-user-plus" class="w-full" :loading="loading" @click="handleRegister" />

                            <div class="text-center mt-4 pb-10">
                                <span class="text-muted-color">Já tem uma conta? </span>
                                <Button label="Fazer login" link class="font-medium text-primary p-0" @click="goToLogin" />
                                <br />
                                <Tag icon="pi pi-home" value="Ir para o Início" @click="goToHome" class="cursor-pointer font-medium text-primary mt-8" style="background-color: transparent; border: none" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <PrivacyTermsModal v-model:visible="showPrivacyModal" :activeTab="activePrivacyTab" />
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
