<script setup lang="ts">
import { onMounted, ref, watch, computed, reactive } from 'vue';
import { useTabStore } from '@/stores/company/tabStore';
import { useProductStore } from '@/stores/company/productStore';
import { usePaymentFormStore } from '@/stores/company/select/paymentFormStore';
import type { Tab, TabPayload, TabItem, TabItemPayload } from '@/types/company/tab';
import type { SendTabDataPayload } from '@/types/company/tab';
import type { TabFilters } from '@/types/company/filters/tabFilter';
import type { DataTableSortEvent, DataTablePageEvent } from 'primevue/datatable';
import { useToast } from 'primevue/usetoast';
import { useFormat } from '@/utils/useFormat';
import { formatLocalDate } from '@/utils/dateUtils';
import { getErrorMessage } from '@/utils/errorUtils';
import { useKeyboardShortcuts } from '@/utils/useKeyboardShortcuts';
import Calculator from '@/components/Calculator.vue';

const tabStore = useTabStore();
const productStore = useProductStore();
const paymentFormStore = usePaymentFormStore();
const { formatCurrency } = useFormat();
const toast = useToast();

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

const dialogState = reactive({
    tabDialog: false,
    tabItemDialog: false,
    deleteTabDialog: false,
    closeTabDialog: false,
    deleteTabItemDialog: false,
    sendTabDialog: false,
    viewTabDialog: false,
    submitted: false
});

const sendTabState = reactive({
    selectedTab: null as Tab | null,
    sendMethod: '' as 'email' | 'system' | 'whatsapp' | '',
    email: '',
    phone: ''
});

const viewTabState = reactive({
    selectedTab: null as Tab | null
});

const dt = ref<unknown>(null);
const selectedTabItem = ref<TabItem | null>(null);
const showFiltersModal = ref(false);

const statusOptions = [
    { label: 'Todas', value: '' },
    { label: 'Aberta', value: 'aberto' },
    { label: 'Paga', value: 'pago' },
    { label: 'Cancelada', value: 'cancelado' }
];

const paymentFormOptions = computed(() => {
    return [{ label: 'Todas', value: null }, ...paymentFormStore.paymentFormOptions];
});

const productOptions = computed(() => {
    return productStore.products.map((product) => ({
        label: `${product.name} - ${formatCurrency(product.price)}`,
        value: product.id
    }));
});

const groupedTabItems = computed(() => {
    if (!tabStore.tab.tabItems) return [];
    const grouped = tabStore.tab.tabItems.reduce(
        (acc, item) => {
            const productName = item.product?.name || 'Produto desconhecido';
            const unitPrice = item.product?.price || 0;
            if (!acc[productName]) {
                acc[productName] = {
                    name: productName,
                    quantity: 0,
                    unitPrice,
                    total: 0
                };
            }
            acc[productName].quantity += item.quantity;
            return acc;
        },
        {} as Record<string, { name: string; quantity: number; unitPrice: number; total: number }>
    );
    Object.values(grouped).forEach((group) => {
        group.total = group.quantity * group.unitPrice;
    });
    return Object.values(grouped);
});

useKeyboardShortcuts(openCreateTab, saveTab, () => dialogState.tabDialog, exportCSV);

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

watch([() => tabStore.tabItem.productId, () => tabStore.tabItem.quantity], () => {
    calculateItemTotal();
});

onMounted(async () => {
    try {
        await Promise.all([loadTabs(), productStore.selectProducts(), paymentFormStore.fetchPaymentForms('presencial')]);
    } catch {
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: 'Erro ao carregar dados da página',
            life: 5000
        });
    }
});

async function loadTabs(): Promise<void> {
    const filters: TabFilters = {
        search: filtersState.searchTerm || undefined,
        status: filtersState.selectedStatus ? filtersState.selectedStatus : undefined,
        paymentFormId: filtersState.selectedPaymentForm || undefined,
        startCreatedDate: filtersState.startCreatedDate ? formatLocalDate(filtersState.startCreatedDate) : undefined,
        endCreatedDate: filtersState.endCreatedDate ? formatLocalDate(filtersState.endCreatedDate) : undefined,
        sort: filtersState.sortField,
        direction: filtersState.sortOrder === 'asc' ? 'desc' : 'asc',
        perPage: filtersState.rowsPerPage,
        page: filtersState.currentPage
    };

    await tabStore.fetchTabs(filters);
}

async function onSearch(): Promise<void> {
    filtersState.currentPage = 1;
    await loadTabs();
}

async function onPageChange(event: DataTablePageEvent): Promise<void> {
    filtersState.currentPage = event.page + 1;
    filtersState.rowsPerPage = event.rows;
    await loadTabs();
}

async function onSort(event: DataTableSortEvent): Promise<void> {
    if (typeof event.sortField === 'string') {
        filtersState.sortField = event.sortField || 'code';
        filtersState.sortOrder = event.sortOrder === 1 ? 'asc' : 'desc';
        await loadTabs();
    }
}

async function onPaginatorPageChange(event: { page: number; first: number; rows: number }): Promise<void> {
    filtersState.currentPage = event.page + 1;
    filtersState.rowsPerPage = event.rows;
    await loadTabs();
}

async function saveTab(): Promise<void> {
    dialogState.submitted = true;

    if (tabStore.tab.code?.trim() && tabStore.tab.customerName?.trim()) {
        try {
            const payload: TabPayload = {
                code: tabStore.tab.code,
                customer_name: tabStore.tab.customerName,
                status: 'aberto',
                payment_form_id: undefined
            };

            if (tabStore.tab.id) {
                await tabStore.updateTab(tabStore.tab.id, payload);
                toast.add({ severity: 'success', summary: 'Sucesso', detail: 'Comanda atualizada com sucesso', life: 3000 });
            } else {
                await tabStore.createTab(payload);
                toast.add({ severity: 'success', summary: 'Sucesso', detail: 'Comanda criada com sucesso', life: 3000 });
            }

            dialogState.tabDialog = false;
            tabStore.clearTab();
            await loadTabs();
        } catch {
            toast.add({ severity: 'error', summary: 'Erro', detail: tabStore.error, life: 3000 });
        }
    }
}

async function cancelTab(): Promise<void> {
    if (tabStore.tab.id) {
        try {
            await tabStore.cancelTab(tabStore.tab.id);
            dialogState.deleteTabDialog = false;
            tabStore.clearTab();
            toast.add({ severity: 'success', summary: 'Sucesso', detail: 'Comanda cancelada com sucesso', life: 3000 });
            await loadTabs();
        } catch {
            toast.add({ severity: 'error', summary: 'Erro', detail: tabStore.error, life: 3000 });
        }
    }
}

async function closeTab(): Promise<void> {
    dialogState.submitted = true;

    if (!tabStore.tab.paymentFormId) {
        toast.add({ severity: 'warn', summary: 'Atenção', detail: 'Selecione uma forma de pagamento', life: 3000 });
        return;
    }

    if (tabStore.tab.id) {
        try {
            const closeData = {
                status: 'pago' as const,
                payment_form_id: tabStore.tab.paymentFormId,
                closed_at: formatLocalDate(new Date())
            };
            await tabStore.updateTab(tabStore.tab.id, closeData);
            dialogState.closeTabDialog = false;
            tabStore.clearTab();
            toast.add({ severity: 'success', summary: 'Sucesso', detail: 'Comanda fechada com sucesso', life: 3000 });
            await loadTabs();
        } catch {
            toast.add({ severity: 'error', summary: 'Erro', detail: tabStore.error || 'Erro ao fechar comanda', life: 3000 });
        }
    }
    dialogState.submitted = false;
}

async function sendTab(): Promise<void> {
    dialogState.submitted = true;

    if (!sendTabState.sendMethod || !sendTabState.selectedTab) {
        return;
    }

    if (sendTabState.sendMethod === 'email') {
        if (!sendTabState.email) {
            toast.add({ severity: 'warn', summary: 'Atenção', detail: 'Endereço de email é obrigatório.', life: 3000 });
            return;
        }

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(sendTabState.email)) {
            toast.add({ severity: 'warn', summary: 'Atenção', detail: 'Endereço de email inválido.', life: 3000 });
            return;
        }
    }

    if (sendTabState.sendMethod === 'system' && !sendTabState.phone) {
        toast.add({ severity: 'warn', summary: 'Atenção', detail: 'Telefone é obrigatório.', life: 3000 });
        return;
    }

    if (sendTabState.sendMethod === 'whatsapp' && !sendTabState.phone) {
        toast.add({ severity: 'warn', summary: 'Atenção', detail: 'Número do WhatsApp é obrigatório.', life: 3000 });
        return;
    }

    if (sendTabState.sendMethod === 'whatsapp') {
        const tab = sendTabState.selectedTab!;
        const baseUrl = window.location.origin;
        const rachasUrl = `${baseUrl}/atleta/comandas`;

        let message = `Envio de Comanda\n\n`;
        message += `Código da Comanda: ${tab.code}\n\n`;
        message += `Cliente: ${tab.customerName}\n`;
        message += `Total: ${formatCurrency(tab.totalAmount)}\n\n`;

        if (tab.tabItems && tab.tabItems.length > 0) {
            message += `Itens:\n`;
            tab.tabItems.forEach((item, index) => {
                message += `${index + 1}. ${item.product?.name || 'Produto'} - Qtd: ${item.quantity} - ${formatCurrency(item.total)}\n`;
                if (item.observation) {
                    message += `   Obs: ${item.observation}\n`;
                }
            });
            message += `\n`;
        }

        message += `Mais detalhes você pode ver pelo astleta\n\n`;
        message += `Vá em "Minhas Comandas" através do link: ${rachasUrl}\n\n`;

        const phone = sendTabState.phone.replace(/\D/g, '');
        const url = `https://wa.me/+55${phone}?text=${encodeURIComponent(message)}`;
        window.open(url, '_blank');

        toast.add({
            severity: 'success',
            summary: 'Sucesso',
            detail: 'WhatsApp aberto com a mensagem da comanda',
            life: 3000
        });

        hideSendTabDialog();
        return;
    }

    try {
        const sendData: SendTabDataPayload = {
            tab_id: sendTabState.selectedTab.id,
            send_method: sendTabState.sendMethod,
            email: sendTabState.email,
            phone: sendTabState.phone.replace(/\D/g, '')
        };

        await tabStore.sendTab(sendData);

        toast.add({
            severity: 'success',
            summary: 'Sucesso',
            detail: `Comanda enviada com sucesso via ${sendTabState.sendMethod === 'email' ? 'Email' : 'Sistema'}.`,
            life: 3000
        });

        hideSendTabDialog();
    } catch (error: unknown) {
        const errorMessage = getErrorMessage(error, 'Erro ao enviar comanda. Tente novamente.');
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: errorMessage,
            life: 3000
        });
    }

    dialogState.submitted = false;
}

async function saveTabItem(): Promise<void> {
    dialogState.submitted = true;

    if (tabStore.tabItem.productId && tabStore.tabItem.quantity > 0 && tabStore.tabItem.tabId) {
        try {
            const payload: TabItemPayload = {
                quantity: tabStore.tabItem.quantity,
                total: tabStore.tabItem.total,
                observation: tabStore.tabItem.observation || undefined,
                tab_id: tabStore.tabItem.tabId,
                product_id: tabStore.tabItem.productId
            };

            if (selectedTabItem.value?.id) {
                toast.add({ severity: 'warn', summary: 'Atenção', detail: 'Edição de itens não é permitida', life: 3000 });
                return;
            } else {
                await tabStore.createTabItem(payload);
                toast.add({ severity: 'success', summary: 'Sucesso', detail: 'Item adicionado com sucesso', life: 3000 });
            }

            hideTabItemDialog();
            await loadTabs();
            await productStore.selectProducts();
        } catch {
            toast.add({ severity: 'error', summary: 'Erro', detail: tabStore.error || 'Erro ao salvar item', life: 3000 });
        }
    }
}

async function deleteTabItem(): Promise<void> {
    if (selectedTabItem.value?.id) {
        try {
            await tabStore.deleteTabItem(selectedTabItem.value.id);
            dialogState.deleteTabItemDialog = false;
            selectedTabItem.value = null;
            toast.add({ severity: 'success', summary: 'Sucesso', detail: 'Item removido com sucesso', life: 3000 });
            await loadTabs();
            await productStore.selectProducts();
        } catch {
            toast.add({ severity: 'error', summary: 'Erro', detail: tabStore.error, life: 3000 });
        }
    }
}

function generateTabCode(): string {
    const now = new Date();
    const day = now.getDate().toString().padStart(2, '0');
    const month = (now.getMonth() + 1).toString().padStart(2, '0');
    const year = now.getFullYear();
    const hours = now.getHours().toString().padStart(2, '0');
    const minutes = now.getMinutes().toString().padStart(2, '0');
    const seconds = now.getSeconds().toString().padStart(2, '0');

    const milliseconds = now.getMilliseconds().toString().padStart(3, '0');
    const randomSuffix = Math.floor(Math.random() * 1000)
        .toString()
        .padStart(3, '0');
    return `CMD${year}${month}${day}${hours}${minutes}${seconds}${milliseconds}${randomSuffix}`;
}

function openCreateTab(): void {
    tabStore.clearTab();
    tabStore.tab.code = generateTabCode();
    dialogState.submitted = false;
    dialogState.tabDialog = true;
}

function openUpdateTab(tabData: Tab): void {
    if (!tabStore.canEditTab(tabData.status)) {
        toast.add({
            severity: 'warn',
            summary: 'Atenção',
            detail: 'Comandas fechadas ou canceladas não podem ser editadas',
            life: 3000
        });
        return;
    }
    tabStore.tab = { ...tabData };
    dialogState.tabDialog = true;
}

function openCloseTab(tabData: Tab): void {
    if (!tabData.tabItems || tabData.tabItems.length === 0) {
        toast.add({
            severity: 'warn',
            summary: 'Atenção',
            detail: 'Não é possível fechar uma comanda sem itens.',
            life: 3000
        });
        return;
    }
    tabStore.tab = { ...tabData };
    dialogState.submitted = false;
    dialogState.closeTabDialog = true;
}

function openCancelTab(tabData: Tab): void {
    if (tabData.status === 'pago') {
        toast.add({
            severity: 'warn',
            summary: 'Atenção',
            detail: 'Não é possível cancelar uma comanda que já foi paga.',
            life: 3000
        });
        return;
    }
    tabStore.tab = tabData;
    dialogState.deleteTabDialog = true;
}

function openSendTab(tabData: Tab): void {
    if (tabData.status === 'cancelado') {
        toast.add({
            severity: 'warn',
            summary: 'Atenção',
            detail: 'Não é possível enviar comandas canceladas.',
            life: 3000
        });
        return;
    }
    sendTabState.selectedTab = tabData;
    sendTabState.sendMethod = '';
    sendTabState.email = '';
    sendTabState.phone = '';
    dialogState.sendTabDialog = true;
}

function hideDialog(): void {
    dialogState.tabDialog = false;
    dialogState.submitted = false;
}

function hideTabItemDialog(): void {
    dialogState.tabItemDialog = false;
    selectedTabItem.value = null;
    tabStore.clearTabItem();
    dialogState.submitted = false;
}

function hideSendTabDialog(): void {
    dialogState.sendTabDialog = false;
    sendTabState.selectedTab = null;
    sendTabState.sendMethod = '';
    sendTabState.email = '';
    sendTabState.phone = '';
}

function addTabItem(tabData: Tab): void {
    if (!tabStore.canEditTab(tabData.status)) {
        toast.add({
            severity: 'warn',
            summary: 'Atenção',
            detail: 'Não é possível adicionar itens a comandas fechadas ou canceladas',
            life: 3000
        });
        return;
    }

    tabStore.clearTabItem();
    tabStore.tabItem.tabId = tabData.id;
    selectedTabItem.value = null;
    calculateItemTotal();
    dialogState.tabItemDialog = true;
}

function confirmDeleteTabItem(item: TabItem): void {
    selectedTabItem.value = item;
    dialogState.deleteTabItemDialog = true;
}

function calculateItemTotal(): void {
    const selectedProduct = productStore.products.find((p) => p.id === tabStore.tabItem.productId);
    if (selectedProduct && tabStore.tabItem.quantity && tabStore.tabItem.quantity > 0) {
        tabStore.tabItem.total = (selectedProduct.price ?? 0) * tabStore.tabItem.quantity;
    } else {
        tabStore.tabItem.total = 0;
    }
}

function getStatusLabel(status: string): string {
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
}

function getStatusText(status: string): string {
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
}

function exportCSV(): void {
    const data: Record<string, string | number | undefined>[] = [];
    tabStore.tabs.forEach((tab) => {
        if (tab.tabItems && tab.tabItems.length > 0) {
            tab.tabItems.forEach((item) => {
                data.push({
                    'Código da Comanda': tab.code,
                    Cliente: tab.customerName,
                    Status: getStatusText(tab.status),
                    Produto: item.product?.name || 'Produto desconhecido',
                    'Preço Unitário': formatCurrency(item.product?.price || 0),
                    Quantidade: item.quantity,
                    'Total do Item': formatCurrency(item.total),
                    Observação: item.observation || '',
                    'Criado em': item.created,
                    'Aberta em': tab.openedAt,
                    'Fechada em': tab.closedAt || '-',
                    'Criada em': tab.created,
                    'Total da Comanda': formatCurrency(tab.totalAmount),
                    'Forma de Pagamento': tab.paymentForm?.name || ''
                });
            });
        } else {
            data.push({
                'Código da Comanda': tab.code,
                Cliente: tab.customerName,
                Status: getStatusText(tab.status),
                Produto: '',
                'Preço Unitário': '',
                Quantidade: '',
                'Total do Item': '',
                Observação: '',
                'Criado em': '',
                'Aberta em': tab.openedAt,
                'Fechada em': tab.closedAt || '-',
                'Criada em': tab.created,
                'Total da Comanda': formatCurrency(tab.totalAmount),
                'Forma de Pagamento': tab.paymentForm?.name || ''
            });
        }
    });

    const headers = Object.keys(data[0]);
    const csvContent = [headers.join(','), ...data.map((row) => headers.map((header) => `"${row[header as keyof typeof row]}"`).join(','))].join('\n');

    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', 'comandas.csv');
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function viewTabDetails(tabData: Tab): void {
    viewTabState.selectedTab = tabData;
    dialogState.viewTabDialog = true;
}

function hideViewTabDialog(): void {
    dialogState.viewTabDialog = false;
    viewTabState.selectedTab = null;
}

function setToday(): void {
    filtersState.startCreatedDate = new Date();
    filtersState.endCreatedDate = new Date();
}

function resetFilters(): void {
    filtersState.selectedStatus = null;
    filtersState.selectedPaymentForm = null;
    filtersState.startCreatedDate = null;
    filtersState.endCreatedDate = null;
    filtersState.searchTerm = '';
}

function applyFilters(): void {
    showFiltersModal.value = false;
}
</script>

<template>
    <div class="space-y-6 lg:space-y-8">
        <div class="lg:grid-cols-12 gap-6">
            <div class="lg:col-span-4 -mx-4 sm:mx-0">
                <div class="card">
                    <Toolbar class="mb-6">
                        <template #start>
                            <div class="hidden md:flex flex-wrap gap-2 items-center">
                                <Button label="Nova Comanda (F1)" icon="pi pi-plus" severity="secondary" class="mr-2" @click="openCreateTab" v-tooltip.top="'Cadastrar nova comanda'" />
                                <DatePicker v-model="filtersState.startCreatedDate" dateFormat="dd/mm/yy" placeholder="Criação Início" class="mr-2" fluid style="width: 140px" v-tooltip.top="'Filtrar por data de criação inicial'" />
                                <DatePicker v-model="filtersState.endCreatedDate" dateFormat="dd/mm/yy" placeholder="Criação Fim" class="mr-2" fluid style="width: 140px" v-tooltip.top="'Filtrar por data de criação final'" />
                                <Button label="Hoje" @click="setToday" severity="secondary" class="mr-2" v-tooltip.top="'Definir filtros para data de hoje'" />
                                <Select v-model="filtersState.selectedStatus" :options="statusOptions" optionLabel="label" optionValue="value" placeholder="Status" class="mr-2" style="width: 140px" v-tooltip.top="'Filtrar por status da comanda'" />
                                <Select
                                    v-model="filtersState.selectedPaymentForm"
                                    :options="paymentFormOptions"
                                    optionLabel="label"
                                    optionValue="value"
                                    placeholder="Forma de Pagamento"
                                    class="mr-2"
                                    style="width: 140px"
                                    v-tooltip.top="'Filtrar por forma de pagamento'"
                                />
                            </div>

                            <div class="block md:hidden">
                                <Button label="Filtros" icon="pi pi-filter" @click="showFiltersModal = true" />
                            </div>

                            <Button label="Resetar Filtros" icon="pi pi-refresh" severity="secondary" @click="resetFilters" class="mr-2 ml-1" v-tooltip.top="'Limpar todos os filtros aplicados'" />
                        </template>

                        <template #end>
                            <Button label="Exportar (F4)" icon="pi pi-upload" severity="secondary" @click="exportCSV" v-tooltip.top="'Exportar Dados da Tabela'" />
                        </template>
                    </Toolbar>

                    <Dialog v-model:visible="showFiltersModal" modal header="Filtros" :style="{ width: '50vw' }" :breakpoints="{ '960px': '75vw', '640px': '100vw' }" :maximizable="true">
                        <div class="flex flex-col gap-4">
                            <div class="flex flex-col gap-2">
                                <label for="startDate">Data de Criação - Início</label>
                                <DatePicker v-model="filtersState.startCreatedDate" fluid dateFormat="dd/mm/yy" placeholder="Criação Início" class="w-full" />
                            </div>
                            <div class="flex flex-col gap-2">
                                <label for="endDate">Data de Criação - Fim</label>
                                <DatePicker v-model="filtersState.endCreatedDate" fluid dateFormat="dd/mm/yy" placeholder="Criação Fim" class="w-full" />
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

                    <div class="block md:hidden mb-6">
                        <div class="grid grid-cols-1 gap-4">
                            <Button label="Nova Comanda" icon="pi pi-plus" severity="secondary" @click="openCreateTab" class="w-full" />
                            <div class="flex gap-2">
                                <IconField class="flex-1">
                                    <InputIcon>
                                        <i class="pi pi-search" />
                                    </InputIcon>
                                    <InputText v-model="filtersState.searchTerm" placeholder="Buscar comanda..." @keyup.enter="onSearch" class="w-full" />
                                </IconField>
                                <Button icon="pi pi-search" severity="secondary" @click="onSearch" :loading="tabStore.loading" />
                            </div>
                        </div>
                    </div>

                    <div class="hidden md:block">
                        <DataTable
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
                                <div class="flex flex-wrap gap-2 items-center justify-between">
                                    <h4 class="m-0 flex items-center gap-2">
                                        <span class="bg-gray-100 text-gray-600 rounded-full w-8 h-8 flex items-center justify-center">
                                            <i class="pi pi-fw pi-receipt"></i>
                                        </span>
                                        Comandas
                                    </h4>
                                    <div class="flex gap-2">
                                        <IconField>
                                            <InputIcon>
                                                <i class="pi pi-search" />
                                            </InputIcon>
                                            <InputText v-model="filtersState.searchTerm" placeholder="Buscar comanda..." />
                                        </IconField>
                                        <Button icon="pi pi-search" severity="secondary" @click="onSearch" :loading="tabStore.loading" v-tooltip.top="'Buscar'" />
                                    </div>
                                </div>
                            </template>

                            <Column expander style="width: 5rem" />

                            <Column field="code" header="Código" sortable style="min-width: 12rem"></Column>

                            <Column field="customer_name" header="Cliente" sortable style="min-width: 16rem">
                                <template #body="slotProps">
                                    {{ slotProps.data.customerName }}
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
                                    {{ slotProps.data.openedAt }}
                                </template>
                            </Column>

                            <Column field="closed_at" header="Fechada em" sortable style="min-width: 12rem">
                                <template #body="slotProps">
                                    {{ slotProps.data.closedAt || '-' }}
                                </template>
                            </Column>

                            <Column :exportable="false" style="min-width: 16rem">
                                <template #body="slotProps">
                                    <Button icon="pi pi-eye" v-tooltip.top="'Ver Detalhes'" rounded outlined severity="info" class="mr-2" @click="viewTabDetails(slotProps.data)" />
                                    <Button icon="pi pi-plus" v-tooltip.top="'Adicionar Item'" rounded outlined class="mr-2" @click="addTabItem(slotProps.data)" :disabled="!tabStore.canEditTab(slotProps.data.status)" />
                                    <Button icon="pi pi-pencil" v-tooltip.top="'Editar'" rounded outlined class="mr-2" @click="openUpdateTab(slotProps.data)" :disabled="!tabStore.canEditTab(slotProps.data.status)" />
                                    <Button
                                        icon="pi pi-check"
                                        v-tooltip.top="'Fechar Comanda'"
                                        rounded
                                        outlined
                                        severity="info"
                                        class="mr-2"
                                        @click="openCloseTab(slotProps.data)"
                                        :disabled="slotProps.data.status !== 'aberto' || !slotProps.data.tabItems || slotProps.data.tabItems.length === 0"
                                    />
                                    <Button icon="pi pi-send" v-tooltip.top="'Enviar Comanda'" rounded outlined severity="success" class="mr-2" @click="openSendTab(slotProps.data)" :disabled="slotProps.data.status === 'cancelado'" />
                                    <Button
                                        icon="pi pi-times"
                                        v-tooltip.top="'Cancelar'"
                                        rounded
                                        outlined
                                        severity="danger"
                                        @click="openCancelTab(slotProps.data)"
                                        :disabled="slotProps.data.status === 'cancelado' || slotProps.data.status === 'pago'"
                                    />
                                </template>
                            </Column>

                            <template #expansion="slotProps">
                                <div class="p-4">
                                    <h5>Itens da Comanda {{ slotProps.data.code }}</h5>
                                    <div v-if="slotProps.data.tabItems && slotProps.data.tabItems.length > 0">
                                        <DataTable :value="slotProps.data.tabItems" class="p-datatable-sm">
                                            <Column field="product.name" header="Produto" style="min-width: 16rem">
                                                <template #body="itemProps">
                                                    {{ itemProps.data.product?.name || 'Produto não encontrado' }}
                                                </template>
                                            </Column>

                                            <Column field="quantity" header="Quantidade" style="min-width: 8rem">
                                                <template #body="itemProps">
                                                    {{ itemProps.data.quantity }}
                                                </template>
                                            </Column>

                                            <Column field="product.price" header="Preço Unit." style="min-width: 10rem">
                                                <template #body="itemProps">
                                                    {{ formatCurrency(itemProps.data.product?.price || 0) }}
                                                </template>
                                            </Column>

                                            <Column field="total" header="Total" style="min-width: 10rem">
                                                <template #body="itemProps">
                                                    {{ formatCurrency((itemProps.data.quantity || 0) * (itemProps.data.product?.price || 0)) }}
                                                </template>
                                            </Column>

                                            <Column field="observation" header="Observação" style="min-width: 16rem">
                                                <template #body="itemProps">
                                                    {{ itemProps.data.observation || '-' }}
                                                </template>
                                            </Column>

                                            <Column field="created" header="Adicionado em" style="min-width: 12rem">
                                                <template #body="itemProps">
                                                    {{ itemProps.data.created }}
                                                </template>
                                            </Column>

                                            <Column headerStyle="width:8rem">
                                                <template #body="itemProps">
                                                    <Button
                                                        icon="pi pi-trash"
                                                        v-tooltip.top="'Excluir Item'"
                                                        rounded
                                                        outlined
                                                        severity="danger"
                                                        size="small"
                                                        @click="confirmDeleteTabItem(itemProps.data)"
                                                        :disabled="!tabStore.canEditTab(slotProps.data.status)"
                                                    />
                                                </template>
                                            </Column>
                                        </DataTable>
                                    </div>
                                    <div v-else class="flex flex-col items-center justify-center text-center text-gray-500 py-4">
                                        <i class="pi pi-fw pi-receipt text-4xl mb-2"></i>
                                        Nenhum item encontrado para esta comanda.
                                    </div>
                                </div>
                            </template>

                            <template #empty>
                                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                                    <i class="pi pi-fw pi-receipt text-4xl mb-2"></i>
                                    <p>Nenhuma comanda encontrada</p>
                                </div>
                            </template>
                        </DataTable>
                    </div>

                    <div class="block md:hidden">
                        <div v-if="tabStore.loading" class="text-center py-8">
                            <ProgressSpinner />
                        </div>
                        <div v-else-if="tabStore.tabs.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400">
                            <i class="pi pi-fw pi-receipt text-4xl mb-2"></i>
                            <p>Nenhuma comanda encontrada</p>
                        </div>
                        <div v-else class="space-y-4">
                            <Card v-for="tab in tabStore.tabs" :key="tab.id" class="h-full flex flex-col rounded-none sm:rounded-xl border border-gray-200 dark:border-gray-700">
                                <template #content>
                                    <div class="p-4 space-y-4 flex-grow">
                                        <div class="grid grid-cols-1 gap-4 mb-4">
                                            <div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Código</p>
                                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ tab.code }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Status</p>
                                                <Tag :value="getStatusText(tab.status)" :severity="getStatusLabel(tab.status)" />
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Cliente</p>
                                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ tab.customerName }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total</p>
                                                <p class="font-medium text-green-600 dark:text-green-400">{{ formatCurrency(tab.totalAmount) }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Aberta em</p>
                                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ tab.openedAt }}</p>
                                            </div>
                                            <div v-if="tab.closedAt">
                                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Fechada em</p>
                                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ tab.closedAt }}</p>
                                            </div>
                                        </div>

                                        <div class="pt-3 border-t border-gray-100 dark:border-gray-700">
                                            <div class="text-xs font-sans text-gray-500 dark:text-gray-400 mb-2 text-center">{{ tab.tabItems?.length || 0 }} item(s)</div>
                                            <div class="grid grid-cols-1 gap-2 font-sans">
                                                <Button label="Ver Detalhes" size="small" outlined severity="info" class="w-full font-sans text-xs" @click="viewTabDetails(tab)" />
                                                <Button label="Adicionar Item" size="small" outlined class="w-full font-sans text-xs" @click="addTabItem(tab)" :disabled="!tabStore.canEditTab(tab.status)" />
                                                <Button label="Editar" size="small" outlined class="w-full font-sans text-xs" @click="openUpdateTab(tab)" :disabled="!tabStore.canEditTab(tab.status)" />
                                                <Button
                                                    label="Fechar Comanda"
                                                    size="small"
                                                    outlined
                                                    severity="info"
                                                    class="w-full font-sans text-xs"
                                                    @click="openCloseTab(tab)"
                                                    :disabled="tab.status !== 'aberto' || !tab.tabItems || tab.tabItems.length === 0"
                                                />
                                                <Button label="Enviar Comanda" size="small" outlined severity="success" class="w-full font-sans text-xs" @click="openSendTab(tab)" :disabled="tab.status === 'cancelado'" />
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

                <Dialog v-model:visible="dialogState.tabDialog" modal header="Detalhes da Comanda" :style="{ width: '30vw' }" :breakpoints="{ '960px': '75vw', '640px': '100vw' }" :maximizable="true">
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label for="code" class="flex items-center justify-between font-bold mb-3">
                                <span>Código <span class="text-red-500">*</span></span>
                                <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:inline" v-tooltip.top="'Código único gerado automaticamente para identificação da comanda'" />
                            </label>
                            <InputText id="code" v-model="tabStore.tab.code" required placeholder="Código da comanda" class="w-full" disabled />
                            <small v-if="dialogState.submitted && !tabStore.tab.code" class="text-red-500">Código é obrigatório.</small>
                        </div>

                        <div>
                            <label for="customerName" class="flex items-center justify-between font-bold mb-3">
                                <span>Nome do Cliente <span class="text-red-500">*</span></span>
                                <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:inline" v-tooltip.top="'Nome da pessoa responsável pela comanda'" />
                            </label>
                            <InputText id="customerName" v-model="tabStore.tab.customerName" required placeholder="Nome do cliente" class="w-full" />
                            <small v-if="dialogState.submitted && !tabStore.tab.customerName" class="text-red-500">Nome do cliente é obrigatório.</small>
                        </div>
                    </div>

                    <template #footer>
                        <Button label="Cancelar" icon="pi pi-times" text @click="hideDialog" />
                        <Button label="Salvar" icon="pi pi-check" @click="saveTab" />
                    </template>
                </Dialog>

                <Dialog v-model:visible="dialogState.tabItemDialog" :style="{ width: '600px' }" header="Item da Comanda" :modal="true">
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label for="product" class="block text-900 font-medium mb-2">Produto *</label>
                            <Select id="product" v-model="tabStore.tabItem.productId" :options="productOptions" optionLabel="label" optionValue="value" placeholder="Selecione o produto" class="w-full" />
                            <small v-if="dialogState.submitted && !tabStore.tabItem.productId" class="text-red-500">Produto é obrigatório.</small>
                        </div>

                        <div>
                            <label for="quantity" class="block font-bold mb-3">Quantidade *</label>
                            <InputNumber id="quantity" v-model="tabStore.tabItem.quantity" :min="1" placeholder="Quantidade" showButtons buttonLayout="horizontal" :step="1" fluid>
                                <template #incrementicon>
                                    <span class="pi pi-plus" />
                                </template>
                                <template #decrementicon>
                                    <span class="pi pi-minus" />
                                </template>
                            </InputNumber>
                            <small v-if="dialogState.submitted && tabStore.tabItem.quantity <= 0" class="text-red-500">Quantidade deve ser maior que zero.</small>
                        </div>

                        <div>
                            <label for="total" class="block text-900 font-medium mb-2">Total</label>
                            <InputNumber id="total" v-model="tabStore.tabItem.total" mode="currency" currency="BRL" locale="pt-BR" disabled class="w-full" />
                        </div>

                        <div class="col-span-2">
                            <label for="observation" class="block text-900 font-medium mb-2">Observação</label>
                            <Textarea id="observation" v-model="tabStore.tabItem.observation" rows="3" placeholder="Observações do item" class="w-full" />
                        </div>
                    </div>

                    <template #footer>
                        <Button label="Cancelar" icon="pi pi-times" text @click="hideTabItemDialog" />
                        <Button label="Salvar" icon="pi pi-check" @click="saveTabItem" />
                    </template>
                </Dialog>

                <Dialog v-model:visible="dialogState.deleteTabDialog" :style="{ width: '450px' }" header="Confirmar" :modal="true">
                    <div class="flex items-center gap-4">
                        <i class="pi pi-exclamation-triangle !text-3xl" />
                        <span v-if="tabStore.tab">
                            Tem certeza que deseja cancelar a comanda <b>{{ tabStore.tab.code }}</b
                            >?
                        </span>
                    </div>
                    <template #footer>
                        <Button label="Não" icon="pi pi-times" text @click="dialogState.deleteTabDialog = false" />
                        <Button label="Sim" icon="pi pi-check" @click="cancelTab" />
                    </template>
                </Dialog>

                <Dialog v-model:visible="dialogState.closeTabDialog" :style="{ width: '1000px' }" header="Fechar Comanda" :modal="true" :maximizable="true">
                    <div class="space-y-6 text-base">
                        <div class="flex flex-col items-center gap-2 mb-6">
                            <i class="pi pi-info-circle text-blue-500" style="font-size: 2rem" />
                            <span class="text-lg text-center">
                                Tem certeza que deseja fechar a comanda <b>{{ tabStore.tab.code }}</b
                                >? Após fechada, não será possível adicionar ou editar itens.
                            </span>
                        </div>

                        <div v-if="tabStore.tab" class="grid grid-cols-1 gap-6">
                            <div>
                                <label class="flex items-center justify-between text-900 font-medium mb-3 text-base">
                                    <span>Total da Comanda</span>
                                    <i class="pi pi-info-circle text-blue-500 cursor-help text-xl hidden md:inline" v-tooltip.top="'Valor total da comanda que pode ser editado se necessário'" />
                                </label>
                                <InputNumber v-model="tabStore.tab.totalAmount" mode="currency" currency="BRL" locale="pt-BR" class="w-full text-lg p-inputtext-lg" />
                                <small class="text-gray-500 text-base block mt-2">Você pode editar este valor se necessário</small>
                            </div>

                            <div>
                                <label for="closePaymentForm" class="flex items-center justify-between text-900 font-medium mb-3 text-base">
                                    <span>Forma de Pagamento <span class="text-red-500">*</span></span>
                                    <i class="pi pi-info-circle text-blue-500 cursor-help text-xl hidden md:inline" v-tooltip.top="'Selecione a forma de pagamento para fechar a comanda'" />
                                </label>
                                <Select
                                    id="closePaymentForm"
                                    v-model="tabStore.tab.paymentFormId"
                                    :options="paymentFormOptions"
                                    optionLabel="label"
                                    optionValue="value"
                                    placeholder="Selecione a forma de pagamento"
                                    class="w-full text-base p-dropdown-lg"
                                />
                                <small v-if="dialogState.submitted && !tabStore.tab.paymentFormId" class="text-red-500 text-base block mt-2">Forma de pagamento é obrigatória.</small>
                            </div>
                        </div>

                        <div v-if="groupedTabItems.length > 0" class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
                            <div class="order-2 lg:order-1">
                                <div class="mt-4 lg:mt-0">
                                    <Calculator />
                                </div>
                            </div>

                            <div class="order-1 lg:order-2">
                                <label class="text-900 font-bold mb-4 block text-xl">Resumo dos Itens</label>
                                <div class="bg-white border-2 border-gray-300 rounded-lg overflow-hidden shadow-sm">
                                    <div class="bg-gray-100 border-b-2 border-gray-300 px-6 py-4 text-center">
                                        <p class="text-gray-700 font-bold text-lg tracking-wider">NOTA NÃO FISCAL</p>
                                        <p class="text-gray-600 text-base mt-2">Comanda {{ tabStore.tab.code }}</p>
                                    </div>

                                    <table class="w-full">
                                        <thead>
                                            <tr class="border-b border-gray-300 bg-gray-50">
                                                <th class="px-4 py-3 text-left text-sm font-bold text-gray-700 uppercase">Descrição</th>
                                                <th class="px-4 py-3 text-center text-sm font-bold text-gray-700 uppercase">Qtd</th>
                                                <th class="px-4 py-3 text-right text-sm font-bold text-gray-700 uppercase">V.Un</th>
                                                <th class="px-4 py-3 text-right text-sm font-bold text-gray-700 uppercase">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="item in groupedTabItems" :key="item.name" class="border-b border-gray-200 hover:bg-gray-50">
                                                <td class="px-4 py-3 text-base font-medium text-gray-800">{{ item.name }}</td>
                                                <td class="px-4 py-3 text-center text-base text-gray-700">{{ item.quantity }}</td>
                                                <td class="px-4 py-3 text-right text-base text-gray-700">{{ formatCurrency(item.unitPrice) }}</td>
                                                <td class="px-4 py-3 text-right text-base font-bold text-gray-900">{{ formatCurrency(item.total) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <div class="border-t-2 border-gray-300 bg-gray-50 px-6 py-4">
                                        <div class="flex justify-between items-center">
                                            <span class="text-base font-bold text-gray-700 tracking-wider uppercase">Total</span>
                                            <span class="text-2xl font-bold text-gray-900">{{ formatCurrency(tabStore.tab.totalAmount) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <template #footer>
                        <Button label="Cancelar" icon="pi pi-times" text @click="dialogState.closeTabDialog = false" class="p-button-lg text-base" />
                        <Button label="Fechar Comanda" icon="pi pi-check" @click="closeTab" class="p-button-lg text-base" />
                    </template>
                </Dialog>

                <Dialog v-model:visible="dialogState.deleteTabItemDialog" :style="{ width: '450px' }" header="Confirmar Remoção" :modal="true">
                    <div class="flex items-center gap-4">
                        <i class="pi pi-exclamation-triangle !text-3xl" />
                        <span v-if="selectedTabItem"> Tem certeza que deseja remover este item da comanda? </span>
                    </div>
                    <template #footer>
                        <Button label="Não" icon="pi pi-times" text @click="dialogState.deleteTabItemDialog = false" />
                        <Button label="Sim" icon="pi pi-check" @click="deleteTabItem" />
                    </template>
                </Dialog>

                <Dialog v-model:visible="dialogState.tabItemDialog" modal header="Adicionar Item à Comanda" :style="{ width: '40vw' }" :breakpoints="{ '960px': '75vw', '640px': '100vw' }" :maximizable="true">
                    <div class="grid grid-cols-1 md:grid-cols-1 gap-4">
                        <div class="col-span-12">
                            <label for="product" class="flex items-center justify-between font-medium mb-2">
                                <span>Produto <span class="text-red-500">*</span></span>
                                <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:inline" v-tooltip.top="'Selecione o produto a ser adicionado à comanda'" />
                            </label>
                            <Select v-model="tabStore.tabItem.productId" :options="productOptions" filter optionLabel="label" optionValue="value" placeholder="Selecione o produto" class="w-full" />
                            <small v-if="dialogState.submitted && !tabStore.tabItem.productId" class="text-red-500">Produto é obrigatório.</small>
                        </div>

                        <div class="col-span-12">
                            <label for="quantity" class="flex items-center justify-between font-bold mb-3">
                                <span>Quantidade <span class="text-red-500">*</span></span>
                                <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:inline" v-tooltip.top="'Informe a quantidade do produto'" />
                            </label>
                            <InputNumber id="quantity" v-model="tabStore.tabItem.quantity" :min="1" placeholder="Quantidade" showButtons buttonLayout="horizontal" :step="1" fluid>
                                <template #incrementicon>
                                    <span class="pi pi-plus" />
                                </template>
                                <template #decrementicon>
                                    <span class="pi pi-minus" />
                                </template>
                            </InputNumber>
                            <small v-if="dialogState.submitted && tabStore.tabItem.quantity <= 0" class="text-red-500">Quantidade deve ser maior que zero.</small>
                        </div>

                        <div class="col-span-12">
                            <label for="total" class="flex items-center justify-between text-900 font-medium mb-2">
                                <span>Total</span>
                                <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:inline" v-tooltip.top="'Valor total calculado automaticamente baseado na quantidade e preço do produto'" />
                            </label>
                            <InputNumber id="total" v-model="tabStore.tabItem.total" mode="currency" currency="BRL" locale="pt-BR" disabled class="w-full" />
                        </div>

                        <div class="col-span-12">
                            <label for="observation" class="flex items-center justify-between text-900 font-medium mb-2">
                                <span>Observação</span>
                                <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:inline" v-tooltip.top="'Adicione observações opcionais sobre o item'" />
                            </label>
                            <Textarea id="observation" v-model="tabStore.tabItem.observation" rows="3" placeholder="Observações do item" class="w-full" />
                        </div>
                    </div>

                    <template #footer>
                        <Button label="Cancelar" icon="pi pi-times" text @click="hideTabItemDialog" />
                        <Button label="Salvar" icon="pi pi-check" @click="saveTabItem" />
                    </template>
                </Dialog>

                <Dialog v-model:visible="dialogState.deleteTabDialog" modal header="Confirmar Cancelamento" :style="{ width: '30vw' }" :breakpoints="{ '960px': '75vw', '640px': '100vw' }" :maximizable="true">
                    <div class="flex flex-col items-center gap-2">
                        <i class="pi pi-exclamation-triangle !text-3xl text-red-500 mb-2" />
                        <span v-if="tabStore.tab" class="text-center">
                            Tem certeza que deseja cancelar a comanda <b>{{ tabStore.tab.code }}</b
                            >?
                        </span>
                    </div>

                    <template #footer>
                        <Button label="Não" icon="pi pi-times" text @click="dialogState.deleteTabDialog = false" />
                        <Button label="Sim" icon="pi pi-check" @click="cancelTab" />
                    </template>
                </Dialog>

                <Dialog v-model:visible="dialogState.deleteTabItemDialog" modal header="Confirmar Remoção" :style="{ width: '30vw' }" :breakpoints="{ '960px': '75vw', '640px': '100vw' }" :maximizable="true">
                    <div class="flex flex-col items-center gap-2">
                        <i class="pi pi-exclamation-triangle !text-3xl text-red-500 mb-2" />
                        <span v-if="selectedTabItem" class="text-center">
                            Tem certeza que deseja remover o item <b>{{ selectedTabItem.product?.name }}</b> da comanda?
                        </span>
                    </div>
                    <template #footer>
                        <Button label="Não" icon="pi pi-times" text @click="dialogState.deleteTabItemDialog = false" />
                        <Button label="Sim" icon="pi pi-check" @click="deleteTabItem" />
                    </template>
                </Dialog>

                <Dialog v-model:visible="dialogState.sendTabDialog" modal header="Enviar Comanda" :style="{ width: '30vw' }" :breakpoints="{ '960px': '75vw', '640px': '100vw' }" :maximizable="true">
                    <div class="space-y-4">
                        <div>
                            <label for="sendMethod" class="flex items-center justify-between text-900 font-medium mb-2">
                                <span>Método de Envio <span class="text-red-500">*</span></span>
                                <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:inline" v-tooltip.top="'Escolha como enviar a comanda: por email, para usuário do sistema ou WhatsApp'" />
                            </label>
                            <Select
                                id="sendMethod"
                                v-model="sendTabState.sendMethod"
                                :options="[
                                    { label: 'Email', value: 'email' },
                                    { label: 'Sistema', value: 'system' },
                                    { label: 'WhatsApp', value: 'whatsapp' }
                                ]"
                                optionLabel="label"
                                optionValue="value"
                                placeholder="Selecione o método"
                                class="w-full"
                            />
                        </div>

                        <div v-if="sendTabState.sendMethod === 'email'">
                            <label for="email" class="flex items-center justify-between text-900 font-medium mb-2">
                                <span>Endereço de Email <span class="text-red-500">*</span></span>
                                <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:inline" v-tooltip.top="'Digite o endereço de email para onde a comanda será enviada'" />
                            </label>
                            <InputText id="email" v-model="sendTabState.email" placeholder="exemplo@email.com" class="w-full" type="email" />
                        </div>

                        <div v-if="sendTabState.sendMethod === 'system'">
                            <label for="phone" class="flex items-center justify-between text-900 font-medium mb-2">
                                <span>Telefone do Usuário <span class="text-red-500">*</span></span>
                                <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:inline" v-tooltip.top="'Digite o telefone do usuário do sistema astleta para enviar e atribuir a comanda.'" />
                            </label>
                            <InputMask id="phone" v-model="sendTabState.phone" mask="99 99999-9999" placeholder="00 00000-0000" class="w-full" type="tel" />
                            <div class="flex flex-col items-center mt-4 text-center border border-orange-400 dark:border-orange-500 bg-orange-50 dark:bg-orange-900/40 rounded-lg p-4 shadow-sm">
                                <i class="pi pi-exclamation-triangle text-orange-600 dark:text-orange-400 text-5xl mb-2"></i>
                                <span class="text-orange-700 dark:text-orange-300 text-base font-medium">
                                    <strong>Atenção:</strong> Ao enviar pelo sistema, o usuário será automaticamente definido como responsável pelo racha e receberá um e-mail de confirmação.<br />
                                    Envios por <b>e-mail</b> ou <b>WhatsApp</b> não atribuem responsabilidade ao usuário.
                                </span>
                            </div>
                        </div>

                        <div v-if="sendTabState.sendMethod === 'whatsapp'">
                            <label for="whatsappPhone" class="flex items-center justify-between text-900 font-medium mb-2">
                                <span>Número do WhatsApp <span class="text-red-500">*</span></span>
                                <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:inline" v-tooltip.top="'Digite o número do WhatsApp para onde a comanda será enviada'" />
                            </label>
                            <InputMask id="whatsappPhone" v-model="sendTabState.phone" mask="99 99999-9999" placeholder="00 00000-0000" class="w-full" type="tel" />
                        </div>
                    </div>

                    <template #footer>
                        <Button label="Cancelar" icon="pi pi-times" text @click="hideSendTabDialog" />
                        <Button
                            label="Enviar"
                            icon="pi pi-send"
                            @click="sendTab"
                            :disabled="
                                !sendTabState.sendMethod ||
                                (sendTabState.sendMethod === 'email' && !sendTabState.email) ||
                                (sendTabState.sendMethod === 'system' && !sendTabState.phone) ||
                                (sendTabState.sendMethod === 'whatsapp' && !sendTabState.phone)
                            "
                        />
                    </template>
                </Dialog>

                <Dialog v-model:visible="dialogState.viewTabDialog" modal header="Detalhes da Comanda" :style="{ width: '50vw' }" :breakpoints="{ '960px': '75vw', '640px': '100vw' }" :maximizable="true">
                    <div v-if="viewTabState.selectedTab" class="space-y-6">
                        <div class="p-4 rounded-lg border dark:border-gray-700">
                            <div class="flex items-center mb-3">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200"><i class="pi pi-info-circle text-primary mr-2"></i> Informações da Comanda</h3>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-md shadow-sm dark:shadow-lg">
                                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Código</label>
                                    <p class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ viewTabState.selectedTab.code }}</p>
                                </div>
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-md shadow-sm dark:shadow-lg">
                                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Cliente</label>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ viewTabState.selectedTab.customerName }}</p>
                                </div>
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-md shadow-sm dark:shadow-lg">
                                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Status</label>
                                    <div class="mt-1">
                                        <Tag :value="getStatusText(viewTabState.selectedTab.status)" :severity="getStatusLabel(viewTabState.selectedTab.status)" class="text-sm" />
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
                                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ viewTabState.selectedTab.openedAt }}</p>
                                </div>
                                <div v-if="viewTabState.selectedTab.closedAt" class="bg-white dark:bg-gray-800 p-3 rounded-md shadow-sm dark:shadow-lg">
                                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Fechada em</label>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ viewTabState.selectedTab.closedAt }}</p>
                                </div>
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-md shadow-sm dark:shadow-lg">
                                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Criada em</label>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ viewTabState.selectedTab.created }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-4 rounded-lg border dark:border-gray-700">
                            <div class="flex items-center mb-3">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200"><i class="pi pi-shopping-cart text-primary mr-2"></i> Itens da Comanda</h3>
                            </div>
                            <div class="space-y-3">
                                <div v-for="item in viewTabState.selectedTab.tabItems" :key="item.id" class="bg-white dark:bg-gray-800 p-4 rounded-md shadow-sm dark:shadow-lg border dark:border-gray-700">
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
                                <div v-if="!viewTabState.selectedTab.tabItems || viewTabState.selectedTab.tabItems.length === 0" class="text-center text-gray-500 py-4"><i class="pi pi-triangle"></i> Nenhum item encontrado para esta comanda.</div>
                            </div>
                        </div>

                        <div class="p-4 rounded-lg border dark:border-gray-700">
                            <div class="flex items-center mb-3">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200"><i class="pi pi-credit-card text-primary mr-2"></i> Informações de Pagamento</h3>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-md shadow-sm dark:shadow-lg">
                                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Total da Comanda</label>
                                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ formatCurrency(viewTabState.selectedTab.totalAmount) }}</p>
                                </div>
                                <div v-if="viewTabState.selectedTab.paymentForm" class="bg-white dark:bg-gray-800 p-3 rounded-md shadow-sm dark:shadow-lg">
                                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Forma de Pagamento</label>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ viewTabState.selectedTab.paymentForm.name }}</p>
                                </div>
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-md shadow-sm dark:shadow-lg">
                                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Empresa</label>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ viewTabState.selectedTab.company?.name }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <template #footer>
                        <Button label="Fechar" icon="pi pi-times" @click="hideViewTabDialog" class="p-button-secondary" />
                    </template>
                </Dialog>
            </div>
        </div>
    </div>
</template>
