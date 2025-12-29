<script lang="ts" setup>
import type { Company } from '@/types/company/company';

const { address } = defineProps<{ address: Company['address'] }>();

const generateMapLink = (address: Company['address']): string => {
    if (!address) return '';
    if (address.latitude && address.longitude) {
        return `https://www.google.com/maps?q=${address.latitude},${address.longitude}`;
    }

    const queryParts = [address.street, address.number, address.district, address.city, address.state, address.zipcode, address.country].filter(Boolean).join(', ');

    return `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(queryParts)}`;
};
</script>

<template>
    <div class="card p-4 border rounded-lg shadow-sm bg-white border-gray-200 dark:border-gray-700">
        <div class="font-semibold text-xl mb-4 text-gray-800 dark:text-gray-100 flex items-center">
            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-green-50 dark:bg-green-900/20 flex items-center justify-center mr-3">
                <i class="pi pi-map-marker text-green-600 dark:text-green-400 text-sm"></i>
            </div>
            Endereço
        </div>

        <div v-if="address" class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-800 dark:text-gray-100">
            <template
                v-for="(label, key, index) in {
                    CEP: address.zipcode,
                    Rua: address.street,
                    Número: address.number,
                    Bairro: address.district,
                    Cidade: address.city,
                    Estado: address.state,
                    País: address.country
                }"
                :key="index"
            >
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-green-50 dark:bg-green-900/20 flex items-center justify-center">
                        <i class="pi pi-map-marker text-green-600 dark:text-green-400" style="font-size: 1.1rem"></i>
                    </div>
                    <div class="flex-1">
                        <div class="text-sm font-semibold">{{ label }}</div>
                        <div class="text-sm mt-1">{{ key }}</div>
                    </div>
                </div>
            </template>

            <div v-if="address.complement" class="flex items-start space-x-3 md:col-span-2">
                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-green-50 dark:bg-green-900/20 flex items-center justify-center">
                    <i class="pi pi-info-circle text-green-600 dark:text-green-400" style="font-size: 1.1rem"></i>
                </div>
                <div class="flex-1">
                    <div class="text-sm font-semibold">Complemento</div>
                    <div class="text-sm mt-1">{{ address.complement }}</div>
                </div>
            </div>

            <div class="mt-4">
                <a :href="generateMapLink(address)" target="_blank" class="inline-flex items-center gap-2 text-sm font-medium text-green-600 dark:text-green-400 hover:text-green-700 dark:hover:text-green-300 transition-colors">
                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-green-50 dark:bg-green-900/20 flex items-center justify-center">
                        <i class="pi pi-directions" style="font-size: 0.9rem"></i>
                    </div>
                    Ver no mapa
                </a>
            </div>
        </div>
        <div v-else class="text-center py-8 text-gray-500 dark:text-gray-400">
            <i class="pi pi-map-marker text-4xl mb-2"></i>
            <p>Nenhum endereço cadastrado</p>
        </div>
    </div>
</template>
