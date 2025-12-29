<script setup lang="ts">
import { ref, reactive, computed, watch, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useToast } from 'primevue/usetoast';
import type { DataTableSortEvent, DataTablePageEvent } from 'primevue/datatable';
import { useTabStore } from '@/stores/athlete/tabAthleteStore';
import { usePaymentFormStore } from '@/stores/company/select/paymentFormStore';
import type { Tab } from '@/types/athlete/tabAthlete';
import type { TabFilters } from '@/types/athlete/filters/tabFilter';
import { useFormat } from '@/utils/useFormat';

const router = useRouter();
const toast = useToast();
const { formatCurrency } = useFormat();
const tabStore = useTabStore();
const paymentFormStore = usePaymentFormStore();

const dt = ref<unknown>(null);
const showFiltersModal = ref(false);

const filtersState = reactive({
    searchTerm: '',
    currentPage: 1,
    rowsPerPage: 10,
    sortField: 'id',
    sortOrder: 'asc' as 'asc' | 'desc',
    selectedStatus: null as 'aberto' | 'pago' | 'cancelado' | '' | null,
    selectedPaymentForm: null as number | null,
    startCreatedDate: null as Date | null,
    endCreatedDate: null as Date | null
});

const statusOptions = [
    { label: 'Todas', value: '' },
    { label: 'Aberta', value: 'aberto' },
    { label: 'Paga', value: 'pago' },
    { label: 'Cancelada', value: 'cancelado' }
];

const paymentFormOptions = computed(() => [{ label: 'Todas', value: null }, ...(paymentFormStore.paymentFormOptions || [])]);

watch(
    () => tabStore.pagination?.currentPage,
    (newPage) => {
        if (newPage && newPage !== filtersState.currentPage) {
            filtersState.currentPage = newPage;
        }
    }
);

watch([() => filtersState.selectedStatus, () => filtersState.selectedPaymentForm, () => filtersState.startCreatedDate, () => filtersState.endCreatedDate], async () => {
    filtersState.currentPage = 1;
    await loadTabs();
});

onMounted(async () => {
    try {
        await Promise.all([loadTabs(), paymentFormStore.fetchPaymentForms()]);
    } catch {
        if (tabStore.error) {
            toast.add({
                severity: 'error',
                summary: 'Erro',
                detail: tabStore.error,
                life: 5000
            });
        }
    }
});

const loadTabs = async (): Promise<void> => {
    const filters: TabFilters = {
        search: filtersState.searchTerm || undefined,
        status: filtersState.selectedStatus || undefined,
        payment_form_id: filtersState.selectedPaymentForm || undefined,
        start_created_date: filtersState.startCreatedDate ? filtersState.startCreatedDate.toISOString().split('T')[0] : undefined,
        end_created_date: filtersState.endCreatedDate ? filtersState.endCreatedDate.toISOString().split('T')[0] : undefined,
        sort: filtersState.sortField,
        direction: filtersState.sortOrder === 'asc' ? 'desc' : 'asc',
        perPage: filtersState.rowsPerPage,
        page: filtersState.currentPage
    };

    await tabStore.fetchTabs(filters);
};

const onSearch = async (): Promise<void> => {
    filtersState.currentPage = 1;
    await loadTabs();
};

const onPageChange = async (event: DataTablePageEvent): Promise<void> => {
    filtersState.currentPage = event.page + 1;
    filtersState.rowsPerPage = event.rows;
    await loadTabs();
};

const onPaginatorPageChange = async (event: { page: number; first: number; rows: number }): Promise<void> => {
    filtersState.currentPage = event.page + 1;
    filtersState.rowsPerPage = event.rows;
    await loadTabs();
};

const onSort = async (event: DataTableSortEvent): Promise<void> => {
    if (typeof event.sortField === 'string') {
        filtersState.sortField = event.sortField || 'code';
        filtersState.sortOrder = event.sortOrder === 1 ? 'asc' : 'desc';
        await loadTabs();
    }
};

const downloadReceipt = async (tab: Tab): Promise<void> => {
    try {
        const blob = await tabStore.downloadReceipt(tab.id);
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = `comprovante_comanda_${tab.code}.pdf`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
        toast.add({
            severity: 'success',
            summary: 'Sucesso',
            detail: 'Recibo baixado com sucesso!',
            life: 3000
        });
    } catch {
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: tabStore.error,
            life: 3000
        });
    }
};

const openDetailTab = (tabData: Tab): void => {
    router.push({ name: 'athleteTabDetails', params: { id: tabData.id.toString() } });
};

const getStatusLabel = (status: string): string => {
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

const getStatusText = (status: string): string => {
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

const setToday = (): void => {
    filtersState.startCreatedDate = new Date();
    filtersState.endCreatedDate = new Date();
};

const applyFilters = (): void => {
    showFiltersModal.value = false;
};

const resetFilters = (): void => {
    filtersState.selectedStatus = null;
    filtersState.selectedPaymentForm = null;
    filtersState.startCreatedDate = null;
    filtersState.endCreatedDate = null;
    filtersState.searchTerm = '';
};
</script>

<template>
    <div class="space-y-6 lg:space-y-8">
        <div class="lg:grid-cols-12 gap-6">
            <div class="lg:col-span-4 -mx-4 sm:mx-0">
                <div class="card">
                    <Toolbar class="mb-6">
                        <template #start>
                            <div class="hidden md:flex flex-wrap gap-2 items-center">
                                <DatePicker v-model="filtersState.startCreatedDate" dateFormat="dd/mm/yy" placeholder="Criação Início" class="mr-2" style="width: 140px" fluid />
                                <DatePicker v-model="filtersState.endCreatedDate" dateFormat="dd/mm/yy" placeholder="Criação Fim" class="mr-2" style="width: 140px" fluid />
                                <Button label="Hoje" @click="setToday" severity="secondary" class="mr-2" />
                                <Select v-model="filtersState.selectedStatus" :options="statusOptions" optionLabel="label" optionValue="value" placeholder="Status" class="mr-2" style="width: 140px" />
                                <Select v-model="filtersState.selectedPaymentForm" :options="paymentFormOptions" optionLabel="label" optionValue="value" placeholder="Forma de Pagamento" class="mr-2" style="width: 140px" />
                                <Button label="Limpar Filtros" icon="pi pi-refresh" severity="secondary" @click="resetFilters" class="mr-2" v-tooltip.top="'Limpar todos os filtros aplicados'" />
                            </div>
                            <div class="block md:hidden">
                                <Button label="Filtros" icon="pi pi-filter" @click="showFiltersModal = true" />
                            </div>
                        </template>
                    </Toolbar>

                    <Dialog v-model:visible="showFiltersModal" modal header="Filtros" :style="{ width: '50vw' }" :breakpoints="{ '960px': '75vw', '640px': '100vw' }" :maximizable="true">
                        <div class="flex flex-col gap-4">
                            <div class="flex flex-col gap-2">
                                <label for="startDate">Data de Criação - Início</label>
                                <DatePicker v-model="filtersState.startCreatedDate" dateFormat="dd/mm/yy" placeholder="Criação Início" class="w-full" fluid />
                            </div>
                            <div class="flex flex-col gap-2">
                                <label for="endDate">Data de Criação - Fim</label>
                                <DatePicker v-model="filtersState.endCreatedDate" dateFormat="dd/mm/yy" placeholder="Criação Fim" class="w-full" fluid />
                            </div>
                            <Button label="Hoje" @click="setToday" severity="secondary" class="w-full" />
                            <div class="flex flex-col gap-2">
                                <label for="status">Status</label>
                                <Select v-model="filtersState.selectedStatus" :options="statusOptions" optionLabel="label" optionValue="value" placeholder="Status" class="w-full" />
                            </div>
                            <div class="flex flex-col gap-2">
                                <label for="paymentForm">Forma de Pagamento</label>
                                <Select v-model="filtersState.selectedPaymentForm" :options="paymentFormOptions" optionLabel="label" optionValue="value" placeholder="Forma de Pagamento" class="w-full" />
                            </div>
                        </div>
                        <template #footer>
                            <Button label="Cancelar" icon="pi pi-times" text @click="showFiltersModal = false" />
                            <Button label="Limpar Filtros" icon="pi pi-refresh" severity="secondary" @click="resetFilters" />
                            <Button label="Aplicar" icon="pi pi-check" @click="applyFilters" />
                        </template>
                    </Dialog>

                    <div class="hidden md:block">
                        <div v-if="tabStore.loading" class="text-center py-8">
                            <ProgressSpinner />
                        </div>

                        <div v-else-if="tabStore.tabs.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400">
                            <i class="pi pi-receipt text-4xl mb-4 text-gray-400"></i>
                            <p class="mb-4">Nenhuma comanda encontrada</p>
                            <p class="text-sm text-600 mt-1">Aqui você pode visualizar suas comandas. As comandas são atribuídas pelo operador do estabelecimento.</p>
                        </div>

                        <DataTable
                            v-else
                            ref="dt"
                            v-model:expandedRows="tabStore.expandedRows"
                            :value="tabStore.tabs"
                            dataKey="id"
                            :paginator="true"
                            :rows="filtersState.rowsPerPage"
                            :totalRecords="tabStore.pagination.total"
                            :loading="tabStore.loading"
                            :first="(filtersState.currentPage - 1) * filtersState.rowsPerPage"
                            paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                            :rowsPerPageOptions="[5, 10, 25, 50]"
                            currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} comandas"
                            :lazy="true"
                            @page="onPageChange"
                            @sort="onSort"
                            sortMode="single"
                            :sortField="filtersState.sortField"
                            :sortOrder="filtersState.sortOrder === 'asc' ? 1 : -1"
                            tableStyle="min-width: 60rem"
                        >
                            <template #header>
                                <div class="flex flex-col gap-2">
                                    <div class="flex flex-wrap gap-2 items-center justify-between">
                                        <div>
                                            <span class="text-xl text-900 font-bold"><i class="pi pi-receipt mr-2"></i>Minhas Comandas</span>
                                        </div>
                                        <div class="flex gap-2">
                                            <IconField>
                                                <InputIcon>
                                                    <i class="pi pi-search" />
                                                </InputIcon>
                                                <InputText v-model="filtersState.searchTerm" placeholder="Buscar comanda..." />
                                            </IconField>
                                            <Button icon="pi pi-search" severity="secondary" @click="onSearch" :loading="tabStore.loading" />
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <template #expansion="slotProps">
                                <div class="p-4">
                                    <h5>Itens da Comanda: {{ slotProps.data.code }}</h5>
                                    <DataTable :value="slotProps.data.tabItems" responsiveLayout="scroll">
                                        <Column field="product.name" header="Produto"></Column>

                                        <Column field="quantity" header="Quantidade"></Column>

                                        <Column field="product.price" header="Preço Unitário">
                                            <template #body="itemProps">
                                                {{ formatCurrency(itemProps.data.product?.price || 0) }}
                                            </template>
                                        </Column>

                                        <Column field="total" header="Total">
                                            <template #body="itemProps">
                                                {{ formatCurrency(itemProps.data.total) }}
                                            </template>
                                        </Column>

                                        <Column field="observation" header="Observação"></Column>
                                    </DataTable>
                                </div>
                            </template>

                            <Column expander style="width: 5rem" />

                            <Column field="code" header="Código" sortable style="min-width: 12rem"></Column>

                            <Column field="customer_name" header="Cliente" sortable style="min-width: 16rem">
                                <template #body="slotProps">
                                    {{ slotProps.data.customerName || '-' }}
                                </template>
                            </Column>

                            <Column field="status" header="Status" sortable style="min-width: 10rem">
                                <template #body="slotProps">
                                    <Tag :value="getStatusText(slotProps.data.status)" :severity="getStatusLabel(slotProps.data.status)" />
                                </template>
                            </Column>

                            <Column field="total_amount" header="Total" sortable style="min-width: 12rem">
                                <template #body="slotProps">
                                    {{ formatCurrency(slotProps.data.totalAmount) }}
                                </template>
                            </Column>

                            <Column field="opened_at" header="Aberta em" sortable style="min-width: 12rem">
                                <template #body="slotProps">
                                    {{ slotProps.data.openedAt ? new Date(slotProps.data.openedAt).toLocaleString('pt-BR') : '-' }}
                                </template>
                            </Column>

                            <Column field="payment_form" header="Forma de Pagamento" style="min-width: 14rem">
                                <template #body="slotProps">
                                    {{ slotProps.data.paymentForm?.name || '-' }}
                                </template>
                            </Column>

                            <Column :exportable="false" style="min-width: 12rem">
                                <template #body="slotProps">
                                    <div class="flex gap-2">
                                        <Button icon="pi pi-eye" class="p-button-rounded p-button-primary p-button-text" @click="openDetailTab(slotProps.data)" v-tooltip.top="'Ver Detalhes'" />
                                        <Button icon="pi pi-download" class="p-button-rounded p-button-primary p-button-text" @click="downloadReceipt(slotProps.data)" v-tooltip.top="'Baixar Recibo'" />
                                    </div>
                                </template>
                            </Column>
                        </DataTable>
                    </div>

                    <div class="block md:hidden">
                        <div v-if="tabStore.loading" class="text-center py-8">
                            <ProgressSpinner />
                        </div>
                        <div v-else-if="tabStore.tabs.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400">
                            <i class="pi pi-receipt text-4xl mb-4 text-gray-400"></i>
                            <p class="mb-4">Nenhuma comanda encontrada</p>
                            <p class="text-sm text-600 mt-1">Aqui você pode visualizar suas comandas. As comandas são atribuídas pelo operador do estabelecimento.</p>
                        </div>
                        <div v-else class="space-y-4">
                            <Card v-for="tab in tabStore.tabs" :key="tab.id" class="h-full flex flex-col rounded-none sm:rounded-xl border border-gray-200 dark:border-gray-700">
                                <template #content>
                                    <div class="p-4 space-y-4 flex-grow">
                                        <div class="flex justify-between items-start mb-3">
                                            <div>
                                                <h3 class="font-semibold text-sm text-gray-900 dark:text-gray-100">{{ tab.code }}</h3>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ tab.customerName }}</p>
                                            </div>
                                            <Tag :value="getStatusText(tab.status)" :severity="getStatusLabel(tab.status)" />
                                        </div>

                                        <div class="grid grid-cols-2 gap-4 mb-4">
                                            <div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total</p>
                                                <p class="font-medium text-green-600 dark:text-green-400">{{ formatCurrency(tab.totalAmount) }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Aberta em</p>
                                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ new Date(tab.openedAt).toLocaleDateString('pt-BR') }}</p>
                                            </div>
                                            <div v-if="tab.paymentForm">
                                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Pagamento</p>
                                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ tab.paymentForm.name }}</p>
                                            </div>
                                            <div v-if="tab.closedAt">
                                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Fechada em</p>
                                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ new Date(tab.closedAt).toLocaleDateString('pt-BR') }}</p>
                                            </div>
                                        </div>

                                        <div class="flex justify-between items-center pt-3 border-t border-gray-100 dark:border-gray-700">
                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ tab.tabItems?.length || 0 }} item(s)</div>
                                            <div class="flex gap-2">
                                                <Button label="Recibo" icon="pi pi-download" size="small" severity="warning" @click="downloadReceipt(tab)" />
                                                <Button label="Ver Detalhes" icon="pi pi-eye" size="small" outlined @click="openDetailTab(tab)" />
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </Card>
                        </div>

                        <div v-if="tabStore.tabs.length > 0" class="mt-6 flex justify-center">
                            <Paginator
                                :first="(filtersState.currentPage - 1) * filtersState.rowsPerPage"
                                :rows="filtersState.rowsPerPage"
                                :totalRecords="tabStore.pagination?.total || 0"
                                @page="onPaginatorPageChange"
                                template="FirstPageLink PrevPageLink CurrentPageReport NextPageLink LastPageLink"
                                currentPageReportTemplate="Página {currentPage} de {totalPages}"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
