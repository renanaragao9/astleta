<script lang="ts" setup>
import type { Company } from '@/types/company/company';
import Tag from 'primevue/tag';
import { formatCnpj } from '@/utils/cnpjFormatter';
import { formatCpf } from '@/utils/cpfFormatter';

const { company } = defineProps<{ company: Company }>();

const getStatusTag = (status: string): { severity: string; label: string } => {
    const normalized = status.toLowerCase();

    switch (normalized) {
        case 'aprovado':
            return { severity: 'success', label: 'Aprovado' };
        case 'pendente':
            return { severity: 'info', label: 'Pendente' };
        case 'rejeitado':
            return { severity: 'danger', label: 'Rejeitado' };
        case 'inadimplente':
            return { severity: 'warn', label: 'Inadimplente' };
        default:
            return { severity: 'secondary', label: status };
    }
};
</script>

<template>
    <div class="card p-4 rounded-lg shadow-sm bg-whit border border-gray-200 dark:border-gray-700">
        <div class="font-semibold text-xl mb-4 text-gray-800 dark:text-gray-100 flex items-center">
            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-green-50 dark:bg-green-900/20 flex items-center justify-center mr-3">
                <i class="pi pi-info-circle text-green-600 dark:text-green-400 text-sm"></i>
            </div>
            Informações
        </div>

        <div class="grid grid-cols-12 gap-4 text-gray-700 dark:text-gray-100">
            <div class="col-span-12 lg:col-span-4">
                <label class="block text-sm font-semibold text-gray-500 dark:text-gray-400">Nome</label>
                <div class="text-base">{{ company.name }}</div>
            </div>

            <div class="col-span-12 lg:col-span-4" v-if="company.cnpj">
                <label class="block text-sm font-semibold text-gray-500 dark:text-gray-400">CNPJ</label>
                <div class="text-base">{{ formatCnpj(company.cnpj) }}</div>
            </div>

            <div class="col-span-12 lg:col-span-4" v-if="company.cpf">
                <label class="block text-sm font-semibold text-gray-500 dark:text-gray-400">CPF</label>
                <div class="text-base">{{ formatCpf(company.cpf) }}</div>
            </div>

            <div class="col-span-12 lg:col-span-4">
                <label class="block text-sm font-semibold text-gray-500 dark:text-gray-400">Telefone</label>
                <div class="text-base">{{ company.phone }}</div>
            </div>

            <div class="col-span-12 lg:col-span-4">
                <label class="block text-sm font-semibold text-gray-500 dark:text-gray-400">Responsável</label>
                <div class="text-base">{{ company.user.name }}</div>
            </div>

            <div class="col-span-12 lg:col-span-4">
                <label class="block text-sm font-semibold text-gray-500 dark:text-gray-400">Email</label>
                <div class="text-base">{{ company.user.email }}</div>
            </div>

            <div class="col-span-12 lg:col-span-4">
                <label class="block text-sm font-semibold text-gray-500 dark:text-gray-400">Status</label>
                <div class="text-base">
                    <Tag :value="getStatusTag(company.status).label" :severity="getStatusTag(company.status).severity" />
                </div>
            </div>

            <div class="col-span-12">
                <label class="block text-sm font-semibold text-gray-500 dark:text-gray-400">Descrição</label>
                <div class="text-base whitespace-pre-line mt-1 p-3">
                    {{ company.description }}
                </div>
            </div>
        </div>
    </div>
</template>
