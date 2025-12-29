<script lang="ts" setup>
import ProgressBar from 'primevue/progressbar';
import { useFormat } from '@/utils/useFormat';

interface Props {
    title: string;
    value: number;
    subtitle: string;
    color: 'green' | 'blue' | 'red';
    showProgressBar?: boolean;
    progressValue?: number;
    progressLabel?: string;
    tooltip?: string;
    icon?: string;
}

const { title, value, subtitle, color, showProgressBar = false, progressValue = 0, progressLabel = '', tooltip, icon } = defineProps<Props>();

const colorClasses = {
    green: 'text-green-600',
    blue: 'text-blue-600',
    red: 'text-red-600'
};

const iconBgClasses = {
    green: 'bg-green-100 text-green-600',
    blue: 'bg-blue-100 text-blue-600',
    red: 'bg-red-100 text-red-600'
};

const formatCurrency = useFormat().formatCurrency;
</script>

<template>
    <Card class="group hover:shadow-lg transition-all duration-300 hover:-translate-y-1 border border-gray-200 dark:border-gray-700" v-tooltip.top="tooltip">
        <template #header>
            <div class="p-4 pb-0">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                    <span v-if="icon" :class="`rounded-full w-8 h-8 flex items-center justify-center ${iconBgClasses[color]}`">
                        <i :class="icon"></i>
                    </span>
                    {{ title }}
                </h3>
            </div>
        </template>
        <template #content>
            <div class="px-4 pb-4">
                <div class="text-3xl font-bold mb-2" :class="colorClasses[color]">{{ formatCurrency(value) }}</div>

                <div v-if="showProgressBar" class="flex items-center justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">{{ progressLabel }}</span>
                    <ProgressBar :value="progressValue" class="w-20" />
                </div>
                <div v-else class="text-sm text-gray-600 dark:text-gray-400">{{ subtitle }}</div>
            </div>
        </template>
    </Card>
</template>
