<script lang="ts" setup>
import type { Company } from '@/types/company/company';

const { contacts } = defineProps<{ contacts: Company['contacts'] }>();

const formatContactLink = (type: string | null | undefined, value: string): string | undefined => {
    if (type === 'WhatsApp') return `https://wa.me/${value.replace(/\D/g, '')}`;
    if (type === 'E-mail') return `mailto:${value}`;
    if (value.startsWith('http')) return value;
    return undefined;
};
</script>

<template>
    <div class="card p-4 border rounded-lg shadow-sm bg-whit border-gray-200 dark:border-gray-700">
        <div class="font-semibold text-xl mb-4 text-gray-800 dark:text-gray-100 flex items-center">
            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-green-50 dark:bg-green-900/20 flex items-center justify-center mr-3">
                <i class="pi pi-phone text-green-600 dark:text-green-400 text-sm"></i>
            </div>
            Contatos
        </div>

        <div class="max-h-80 overflow-y-auto">
            <ul v-if="contacts && contacts.length > 0" class="space-y-4">
                <li v-for="(contact, index) in contacts" :key="index" class="flex items-start space-x-3">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-green-50 dark:bg-green-900/20 flex items-center justify-center">
                        <i class="text-green-600 dark:text-green-400" :class="contact.type?.icon" style="font-size: 1.1rem"></i>
                    </div>
                    <div class="text-gray-800 dark:text-gray-100 flex-1">
                        <div class="text-sm font-semibold">{{ contact.type?.name ?? 'Comercial' }}</div>
                        <div class="text-sm break-all mt-1">
                            <template v-if="formatContactLink(contact.type?.name, contact.value)">
                                <a :href="formatContactLink(contact.type?.name, contact.value)" target="_blank" class="text-blue-600 hover:underline dark:text-blue-400">
                                    {{ contact.value }}
                                </a>
                            </template>
                            <template v-else>
                                {{ contact.value }}
                            </template>
                        </div>
                    </div>
                </li>
            </ul>
            <div v-else class="text-center py-8 text-gray-500 dark:text-gray-400">
                <i class="pi pi-phone text-4xl mb-2"></i>
                <p>Nenhum contato cadastrado</p>
            </div>
        </div>
    </div>
</template>
