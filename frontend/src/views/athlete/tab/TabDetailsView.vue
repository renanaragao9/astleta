<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useTabStore } from '@/stores/athlete/tabAthleteStore';
import { useFormat } from '@/utils/useFormat';
import { useToast } from 'primevue/usetoast';
import Tag from 'primevue/tag';

interface Props {
    id: string;
}

const props = defineProps<Props>();
const route = useRoute();
const router = useRouter();
const tabStore = useTabStore();
const { formatCurrency } = useFormat();
const toast = useToast();

const loading = ref(true);

onMounted(async () => {
    try {
        loading.value = true;
        const tabId = Number(props.id || route.params.id);

        if (!tabId || isNaN(tabId)) {
            toast.add({
                severity: 'error',
                summary: 'Erro',
                detail: 'ID da comanda inválido',
                life: 5000
            });
            router.push({ name: 'athleteTabs' });
            return;
        }

        await tabStore.fetchTab(tabId);

        if (tabStore.error) {
            toast.add({
                severity: 'error',
                summary: 'Erro',
                detail: tabStore.error,
                life: 5000
            });
            router.push({ name: 'athleteTabs' });
            return;
        }
    } catch {
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: 'Erro ao carregar detalhes da comanda',
            life: 5000
        });
        router.push({ name: 'athleteTabs' });
    } finally {
        loading.value = false;
    }
});

const getStatusLabel = (status: string): string => {
    switch (status) {
        case 'aberto':
            return 'Aberta';
        case 'pago':
            return 'Paga';
        case 'cancelado':
            return 'Cancelada';
        default:
            return status;
    }
};

const getStatusSeverity = (status: string): string => {
    switch (status) {
        case 'aberto':
            return 'info';
        case 'pago':
            return 'success';
        case 'cancelado':
            return 'danger';
        default:
            return 'secondary';
    }
};

const goBack = (): void => {
    router.push({ name: 'athleteTabs' });
};
</script>

<template>
    <div class="space-y-6 lg:space-y-8">
        <div class="lg:grid-cols-12 gap-6">
            <div class="lg:col-span-4 -mx-4 sm:mx-0">
                <div class="card">
                    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-6">
                        <div class="flex items-center gap-3">
                            <Button icon="pi pi-arrow-left" severity="secondary" text @click="goBack" v-tooltip.top="'Voltar'" />
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Detalhes da Comanda</h1>
                        </div>

                        <div v-if="!loading && tabStore.tab">
                            <Tag :value="getStatusLabel(tabStore.tab.status)" :severity="getStatusSeverity(tabStore.tab.status)" class="text-lg px-4 py-2" />
                        </div>
                    </div>

                    <div v-if="loading" class="flex justify-center items-center py-16">
                        <ProgressSpinner />
                        <span class="ml-3 text-lg dark:text-gray-300">Carregando detalhes da comanda...</span>
                    </div>

                    <div v-else-if="tabStore.tab && tabStore.tab.id" class="space-y-6">
                        <div class="p-4 rounded-lg border dark:border-gray-700">
                            <div class="flex items-center mb-3">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200"><i class="pi pi-info-circle text-primary mr-2"></i> Informações da Comanda</h3>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-md shadow-sm dark:shadow-lg">
                                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Código</label>
                                    <p class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ tabStore.tab.code }}</p>
                                </div>
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-md shadow-sm dark:shadow-lg">
                                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Cliente</label>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ tabStore.tab.customerName }}</p>
                                </div>
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-md shadow-sm dark:shadow-lg">
                                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Status</label>
                                    <div class="mt-1">
                                        <Tag :value="getStatusLabel(tabStore.tab.status)" :severity="getStatusSeverity(tabStore.tab.status)" class="text-sm" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="p-4 rounded-lg border dark:border-gray-700">
                            <div class="flex items-center mb-3">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200"><i class="pi pi-clock text-primary mr-2"></i> Datas</h3>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-md shadow-sm dark:shadow-lg">
                                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Aberta em</label>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ tabStore.tab.openedAt }}</p>
                                </div>
                                <div v-if="tabStore.tab.closedAt" class="bg-white dark:bg-gray-800 p-3 rounded-md shadow-sm dark:shadow-lg">
                                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Fechada em</label>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ tabStore.tab.closedAt }}</p>
                                </div>
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-md shadow-sm dark:shadow-lg">
                                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Criada em</label>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ tabStore.tab.created }}</p>
                                </div>
                            </div>
                        </div>

                        <div v-if="tabStore.tab.tabItems && tabStore.tab.tabItems.length > 0" class="p-4 rounded-lg border dark:border-gray-700">
                            <div class="flex items-center mb-3">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200"><i class="pi pi-shopping-cart text-primary mr-2"></i> Itens da Comanda</h3>
                            </div>
                            <div class="space-y-3">
                                <div v-for="item in tabStore.tab.tabItems" :key="item.id" class="bg-white dark:bg-gray-800 p-4 rounded-md shadow-sm dark:shadow-lg border dark:border-gray-700">
                                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Produto</label>
                                            <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ item.product?.name }}</p>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Preço Unitário</label>
                                            <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ formatCurrency(item.product?.price || 0) }}</p>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Quantidade</label>
                                            <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ item.quantity }}</p>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Total</label>
                                            <p class="text-lg font-bold text-green-600 dark:text-green-400">{{ formatCurrency(item.total) }}</p>
                                        </div>
                                    </div>
                                    <div class="mt-3 pt-3 border-t dark:border-gray-600 grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Criado em</label>
                                            <p class="text-sm text-gray-700 dark:text-gray-300">{{ item.created }}</p>
                                        </div>
                                        <div v-if="item.observation">
                                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Observação</label>
                                            <p class="text-sm text-gray-700 dark:text-gray-300">{{ item.observation }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="p-4 rounded-lg border dark:border-gray-700">
                            <div class="flex items-center mb-3">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200"><i class="pi pi-credit-card text-primary mr-2"></i> Informações de Pagamento</h3>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-md shadow-sm dark:shadow-lg">
                                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Total da Comanda</label>
                                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ formatCurrency(tabStore.tab.totalAmount) }}</p>
                                </div>
                                <div v-if="tabStore.tab.paymentForm" class="bg-white dark:bg-gray-800 p-3 rounded-md shadow-sm dark:shadow-lg">
                                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Forma de Pagamento</label>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ tabStore.tab.paymentForm.name }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-center pt-6 border-t border-gray-200 dark:border-gray-700">
                            <Button label="Voltar às Comandas" icon="pi pi-arrow-left" severity="secondary" @click="goBack" class="px-6 py-3" />
                        </div>
                    </div>

                    <div v-else class="text-center py-16">
                        <i class="pi pi-exclamation-triangle text-6xl text-gray-400 dark:text-gray-600 mb-4"></i>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">Comanda não encontrada</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-6">A comanda que você está procurando não existe ou você não tem permissão para acessá-la.</p>
                        <Button label="Voltar às Comandas" icon="pi pi-arrow-left" @click="goBack" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped></style>
