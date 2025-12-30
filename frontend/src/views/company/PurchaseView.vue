<script setup lang="ts">
import { onMounted, ref, watch, computed, reactive } from 'vue';
import { usePurchaseStore } from '@/stores/company/purchaseStore';
import { useSupplierStore } from '@/stores/company/supplierStore';
import { useProductStore } from '@/stores/company/productStore';
import { useWarehouseStore } from '@/stores/company/warehouseStore';
import type { Purchase } from '@/types/company/purchase';
import type { PurchasePayload } from '@/types/company/purchase';
import type { DataTableSortEvent, DataTablePageEvent } from 'primevue/datatable';
import { useToast } from 'primevue/usetoast';
import { useFormat } from '@/utils/useFormat';
import { formatLocalDate } from '@/utils/dateUtils';
import { useKeyboardShortcuts } from '@/utils/useKeyboardShortcuts';

const purchaseStore = usePurchaseStore();
const supplierStore = useSupplierStore();
const productStore = useProductStore();
const warehouseStore = useWarehouseStore();
const { formatCurrency } = useFormat();

const searchTerm = ref('');
const currentPage = ref(1);
const rowsPerPage = ref(10);
const sortField = ref('id');
const sortOrder = ref<'asc' | 'desc'>('asc');

const purchaseDialog = ref(false);
const editPurchaseDialog = ref(false);
const viewPurchaseDialog = ref(false);
const deletePurchaseDialog = ref(false);
const deletePurchasesDialog = ref(false);
const submitted = ref(false);
const showFiltersModal = ref(false);
const expandedRows = ref<Record<number, boolean>>({});

const supplierOptions = ref<{ label: string; value: number }[]>([]);
const productOptions = ref<{ label: string; value: number }[]>([]);
const warehouseOptions = ref<{ label: string; value: number }[]>([]);

const statusOptions = ref<{ label: string; value: string }[]>([
    { label: 'Concluído', value: 'concluido' },
    { label: 'Cancelado', value: 'cancelado' }
]);

const supplierFilterOptions = computed(() => {
    return [{ label: 'Todos', value: null }, ...(supplierOptions.value || [])];
});

const statusFilterOptions = computed(() => {
    return [{ label: 'Todos', value: null }, ...statusOptions.value.map((opt) => ({ label: opt.label, value: opt.value }))];
});

const filtersObj = reactive({
    supplier: null as number | null,
    status: null as string | null,
    startPurchaseDate: null as Date | null,
    endPurchaseDate: null as Date | null
});

const purchaseItems = ref<Array<{ productId: number | null; warehouseId: number | null; quantity: number | null }>>([{ productId: null, warehouseId: null, quantity: null }]);

const toast = useToast();

useKeyboardShortcuts(openCreatePurchase, savePurchase, purchaseDialog, exportCSV);

watch(
    () => purchaseStore.pagination?.currentPage,
    (newPage) => {
        if (newPage && newPage !== currentPage.value) {
            currentPage.value = newPage;
        }
    }
);

watch(
    () => filtersObj,
    async () => {
        currentPage.value = 1;
        await loadPurchases();
    },
    { deep: true }
);

onMounted(async () => {
    try {
        await Promise.all([loadPurchases(), fetchSupplierOptions(), fetchProductOptions(), fetchWarehouseOptions()]);
    } catch {
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: 'Erro ao carregar dados da página',
            life: 5000
        });
    }
});

async function fetchSupplierOptions(): Promise<void> {
    try {
        await supplierStore.selectSuppliers();
        supplierOptions.value = supplierStore.suppliers.map((supplier) => ({
            label: supplier.name,
            value: supplier.id
        }));
    } catch {
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: 'Erro ao carregar fornecedores',
            life: 5000
        });
    }
}

async function fetchProductOptions(): Promise<void> {
    try {
        const products = await productStore.selectProducts();
        productOptions.value = products.map((product) => ({
            label: product.name,
            value: product.id
        }));
    } catch {
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: 'Erro ao carregar produtos',
            life: 5000
        });
    }
}

async function fetchWarehouseOptions(): Promise<void> {
    try {
        const warehouses = await warehouseStore.selectWarehouses();
        warehouseOptions.value = warehouses.map((warehouse) => ({
            label: warehouse.name,
            value: warehouse.id
        }));
    } catch {
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: 'Erro ao carregar armazéns',
            life: 5000
        });
    }
}

async function loadPurchases(): Promise<void> {
    const filters = {
        search: searchTerm.value || undefined,
        sort: sortField.value,
        direction: sortOrder.value,
        perPage: rowsPerPage.value,
        page: currentPage.value,
        supplierId: filtersObj.supplier || undefined,
        status: filtersObj.status || undefined,
        startPurchaseDate: filtersObj.startPurchaseDate ? formatLocalDate(filtersObj.startPurchaseDate) : undefined,
        endPurchaseDate: filtersObj.endPurchaseDate ? formatLocalDate(filtersObj.endPurchaseDate) : undefined
    };

    await purchaseStore.fetchPurchases(filters);
}

async function onSearch(): Promise<void> {
    currentPage.value = 1;
    await loadPurchases();
}

async function onPageChange(event: DataTablePageEvent): Promise<void> {
    currentPage.value = event.page + 1;
    rowsPerPage.value = event.rows;
    await loadPurchases();
}

async function onPaginatorPageChange(event: { page: number; first: number; rows: number }): Promise<void> {
    currentPage.value = event.page + 1;
    rowsPerPage.value = event.rows;
    await loadPurchases();
}

async function onSort(event: DataTableSortEvent): Promise<void> {
    if (typeof event.sortField === 'string') {
        sortField.value = event.sortField || 'created';
        sortOrder.value = event.sortOrder === 1 ? 'asc' : 'desc';
        await loadPurchases();
    }
}

async function savePurchase(): Promise<void> {
    submitted.value = true;

    if (purchaseStore.purchase.invoiceNumber?.trim() && purchaseItems.value.length > 0) {
        try {
            let purchaseDateString = '';
            if (purchaseStore.purchase.purchaseDate) {
                if (purchaseStore.purchase.purchaseDate instanceof Date) {
                    purchaseDateString = purchaseStore.purchase.purchaseDate.toISOString().split('T')[0];
                } else {
                    purchaseDateString = new Date(purchaseStore.purchase.purchaseDate).toISOString().split('T')[0];
                }
            }

            const payload: PurchasePayload = {
                invoice_number: purchaseStore.purchase.invoiceNumber,
                purchase_date: purchaseDateString,
                total_amount: purchaseStore.purchase.totalAmount || 0,
                supplier_id: purchaseStore.purchase.supplierId,
                items: purchaseItems.value.map((item) => ({
                    product_id: item.productId || 0,
                    warehouse_id: item.warehouseId || 0,
                    quantity: item.quantity || 0
                }))
            };

            if (purchaseStore.purchase.id) {
                await purchaseStore.updatePurchase(purchaseStore.purchase.id, payload);
                toast.add({ severity: 'success', summary: 'Sucesso', detail: 'Compra atualizada com sucesso', life: 3000 });
            } else {
                await purchaseStore.createPurchase(payload);
                toast.add({ severity: 'success', summary: 'Sucesso', detail: 'Compra criada com sucesso', life: 3000 });
            }

            purchaseDialog.value = false;
            purchaseStore.clearPurchase();
            purchaseItems.value = [{ productId: null, warehouseId: null, quantity: null }];
            await loadPurchases();
        } catch {
            toast.add({ severity: 'error', summary: 'Erro', detail: purchaseStore.error, life: 3000 });
        }
    }
}

async function saveEditPurchase(): Promise<void> {
    submitted.value = true;

    if (purchaseStore.purchase.invoiceNumber?.trim() && purchaseStore.purchase.totalAmount && purchaseStore.purchase.supplierId) {
        try {
            let purchaseDateString = '';
            if (purchaseStore.purchase.purchaseDate) {
                if (purchaseStore.purchase.purchaseDate instanceof Date) {
                    purchaseDateString = purchaseStore.purchase.purchaseDate.toISOString().split('T')[0];
                } else {
                    purchaseDateString = new Date(purchaseStore.purchase.purchaseDate).toISOString().split('T')[0];
                }
            }

            const payload: PurchasePayload = {
                invoice_number: purchaseStore.purchase.invoiceNumber,
                purchase_date: purchaseDateString,
                total_amount: purchaseStore.purchase.totalAmount,
                supplier_id: purchaseStore.purchase.supplierId
            };

            await purchaseStore.updatePurchase(purchaseStore.purchase.id!, payload);
            toast.add({ severity: 'success', summary: 'Sucesso', detail: 'Compra atualizada com sucesso', life: 3000 });

            editPurchaseDialog.value = false;
            purchaseStore.clearPurchase();
            await loadPurchases();
        } catch {
            toast.add({ severity: 'error', summary: 'Erro', detail: purchaseStore.error, life: 3000 });
        }
    }
}

async function deletePurchase(): Promise<void> {
    if (purchaseStore.purchase.id) {
        try {
            await purchaseStore.deletePurchase(purchaseStore.purchase.id);
            deletePurchaseDialog.value = false;
            purchaseStore.clearPurchase();
            toast.add({ severity: 'success', summary: 'Sucesso', detail: 'Compra cancelada com sucesso', life: 3000 });
            await loadPurchases();
        } catch {
            toast.add({ severity: 'error', summary: 'Erro', detail: purchaseStore.error, life: 3000 });
        }
    }
}

async function deleteSelectedPurchases(): Promise<void> {
    try {
        await purchaseStore.deleteSelectedPurchases();
        deletePurchasesDialog.value = false;
        toast.add({ severity: 'success', summary: 'Sucesso', detail: 'Compras deletadas com sucesso', life: 3000 });
        await loadPurchases();
    } catch {
        toast.add({ severity: 'error', summary: 'Erro', detail: purchaseStore.error, life: 3000 });
    }
}

function openCreatePurchase(): void {
    purchaseStore.clearPurchase();
    purchaseStore.purchase.totalAmount = null;
    purchaseItems.value = [{ productId: null, warehouseId: null, quantity: null }];
    submitted.value = false;
    purchaseDialog.value = true;
}

function openUpdatePurchase(purchaseData: Purchase): void {
    const editedPurchase = { ...purchaseData };
    if (editedPurchase.purchaseDate && typeof editedPurchase.purchaseDate === 'string') {
        const dateStr = editedPurchase.purchaseDate as string;
        const [day, month, year] = dateStr.split('/').map(Number);
        editedPurchase.purchaseDate = new Date(year, month - 1, day);
    }
    purchaseStore.purchase = editedPurchase;
    submitted.value = false;
    editPurchaseDialog.value = true;
}

function openViewPurchase(purchaseData: Purchase): void {
    purchaseStore.purchase = purchaseData;
    viewPurchaseDialog.value = true;
}

function openDestroyPurchase(purchaseData: Purchase): void {
    purchaseStore.purchase = purchaseData;
    deletePurchaseDialog.value = true;
}

function openDestroySelectedPurchase(): void {
    deletePurchasesDialog.value = true;
}

function addItem(): void {
    purchaseItems.value.push({ productId: null, warehouseId: null, quantity: null });
}

function removeItem(index: number): void {
    if (purchaseItems.value.length > 1) {
        purchaseItems.value.splice(index, 1);
    }
}

function hideDialog(): void {
    purchaseDialog.value = false;
    submitted.value = false;
    purchaseItems.value = [{ productId: null, warehouseId: null, quantity: null }];
}

function setToday(): void {
    filtersObj.startPurchaseDate = new Date();
    filtersObj.endPurchaseDate = new Date();
}

function resetFilters(): void {
    filtersObj.supplier = null;
    filtersObj.status = null;
    filtersObj.startPurchaseDate = null;
    filtersObj.endPurchaseDate = null;
    searchTerm.value = '';
}

function applyFilters(): void {
    showFiltersModal.value = false;
}

function exportCSV(): void {
    const data = purchaseStore.purchases.flatMap((purchase) => {
        if (purchase.products && purchase.products.length > 0) {
            return purchase.products.map((product) => ({
                'Número da Nota': purchase.invoiceNumber,
                'Data da Compra': purchase.purchaseDate,
                'Valor Total': formatCurrency(purchase.totalAmount),
                Fornecedor: purchase.supplier?.name || '-',
                'Criado em': purchase.created,
                'Nome do Produto': product.name,
                Quantidade: product.total.toString(),
                'Custo Médio': formatCurrency(product.average_cost)
            }));
        } else {
            return [
                {
                    'Número da Nota': purchase.invoiceNumber,
                    'Data da Compra': purchase.purchaseDate,
                    'Valor Total': formatCurrency(purchase.totalAmount),
                    Fornecedor: purchase.supplier?.name || '-',
                    'Criado em': purchase.created,
                    'Nome do Produto': '',
                    Quantidade: '',
                    'Custo Médio': ''
                }
            ];
        }
    });

    const headers = Object.keys(data[0]);
    const csvContent = [headers.join(','), ...data.map((row) => headers.map((header) => `"${row[header as keyof typeof row]}"`).join(','))].join('\n');

    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', 'compras.csv');
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function getStatusTag(status: string | null | undefined): { label: string; severity: 'success' | 'danger'; icon: string } {
    if (status === 'cancelado') {
        return { label: 'Cancelado', severity: 'danger', icon: 'pi pi-times' };
    } else if (status === 'concluido') {
        return { label: 'Concluído', severity: 'success', icon: 'pi pi-check' };
    } else {
        return { label: 'Ativo', severity: 'success', icon: 'pi pi-check' };
    }
}

const sendToWhatsApp = (purchase: Purchase): void => {
    let message = `*Lista de Compra*\n\n`;
    message += `*N° da Nota:* ${purchase.invoiceNumber}\n`;
    message += `*Data da Compra:* ${purchase.purchaseDate || ''}\n`;
    message += `*Valor Total:* ${formatCurrency(purchase.totalAmount)}\n`;
    message += `*Fornecedor:* ${purchase.supplier?.name || ''}\n`;
    message += `*Status:* ${purchase.status === 'cancelado' ? 'Cancelado' : purchase.status || 'Ativo'}\n\n`;

    const products = purchase.products;
    if (products && products.length > 0) {
        message += `*Produtos:*\n`;
        products.forEach((product: { name: string; total: number }, index: number) => {
            message += `${index + 1}. ${product.name} - Qtd: ${product.total}\n`;
        });
    }

    message += `\n*Enviado via SeuRacha*`;

    const url = `https://wa.me/?text=${encodeURIComponent(message)}`;
    window.open(url, '_blank');
};
</script>

<template>
    <div class="space-y-6 lg:space-y-8">
        <div class="lg:grid-cols-12 gap-6">
            <div class="lg:col-span-4 -mx-4 sm:mx-0">
                <div class="card">
                    <Toolbar class="mb-6">
                        <template #start>
                            <div class="flex-wrap gap-2 items-center hidden md:flex">
                                <Button label="Adicionar Compra (F1)" icon="pi pi-plus" severity="secondary" class="mr-2" @click="openCreatePurchase" v-tooltip.top="'Cadastrar nova compra'" />
                                <DatePicker v-model="filtersObj.startPurchaseDate" dateFormat="dd/mm/yy" placeholder="Compra Início" class="mr-2" fluid style="width: 140px" v-tooltip.top="'Filtrar por data de compra inicial'" />
                                <DatePicker v-model="filtersObj.endPurchaseDate" dateFormat="dd/mm/yy" placeholder="Compra Fim" class="mr-2" fluid style="width: 140px" v-tooltip.top="'Filtrar por data de compra final'" />
                                <Button label="Hoje" @click="setToday" severity="secondary" class="mr-2" v-tooltip.top="'Definir filtros para data de hoje'" />
                                <Select v-model="filtersObj.supplier" :options="supplierFilterOptions" optionLabel="label" optionValue="value" placeholder="Fornecedor" class="mr-2" style="width: 140px" v-tooltip.top="'Filtrar por fornecedor'" />
                                <Select v-model="filtersObj.status" :options="statusFilterOptions" optionLabel="label" optionValue="value" placeholder="Status" class="mr-2" style="width: 140px" v-tooltip.top="'Filtrar por status'" />
                            </div>
                            <Button label="Resetar Filtros" icon="pi pi-refresh" severity="secondary" @click="resetFilters" class="mr-2" v-tooltip.top="'Limpar todos os filtros aplicados'" />
                            <Button
                                label="Deletar"
                                icon="pi pi-trash"
                                severity="secondary"
                                @click="openDestroySelectedPurchase"
                                :disabled="!purchaseStore.selectedPurchases || !purchaseStore.selectedPurchases.length"
                                v-tooltip.top="'Excluir compras selecionadas'"
                            />
                        </template>

                        <template #end>
                            <Button label="Exportar (F4)" icon="pi pi-upload" severity="secondary" @click="exportCSV" v-tooltip.top="'Exportar Dados da Tabela'" />
                        </template>
                    </Toolbar>

                    <Dialog v-model:visible="showFiltersModal" modal header="Filtros" :style="{ width: '30vw' }" :breakpoints="{ '960px': '75vw', '640px': '100vw' }" :maximizable="true">
                        <div class="flex flex-col gap-4">
                            <div class="flex flex-col gap-2 w-full">
                                <label for="startPurchaseDate">Compra Início</label>
                                <DatePicker v-model="filtersObj.startPurchaseDate" dateFormat="dd/mm/yy" placeholder="Compra Início" class="w-full" fluid />
                            </div>
                            <div class="flex flex-col gap-2 w-full">
                                <label for="endPurchaseDate">Compra Fim</label>
                                <DatePicker v-model="filtersObj.endPurchaseDate" dateFormat="dd/mm/yy" placeholder="Compra Fim" class="w-full" fluid />
                            </div>
                            <Button label="Hoje" @click="setToday" severity="secondary" class="w-full" />
                            <div class="flex flex-col gap-2">
                                <label for="supplier">Fornecedor</label>
                                <Select v-model="filtersObj.supplier" :options="supplierFilterOptions" optionLabel="label" optionValue="value" placeholder="Fornecedor" class="w-full" />
                            </div>
                            <div class="flex flex-col gap-2">
                                <label for="status">Status</label>
                                <Select v-model="filtersObj.status" :options="statusFilterOptions" optionLabel="label" optionValue="value" placeholder="Status" class="w-full" />
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
                            <Button label="Adicionar Compra" icon="pi pi-plus" severity="secondary" @click="openCreatePurchase" class="w-full" />
                            <Button label="Filtros" icon="pi pi-filter" @click="showFiltersModal = true" class="w-full" />
                            <div class="mt-4 flex gap-2">
                                <IconField class="flex-1">
                                    <InputIcon>
                                        <i class="pi pi-search" />
                                    </InputIcon>
                                    <InputText v-model="searchTerm" placeholder="Buscar compra..." @keyup.enter="onSearch" class="w-full" />
                                </IconField>
                                <Button icon="pi pi-search" severity="secondary" @click="onSearch" :loading="purchaseStore.loading" />
                            </div>
                        </div>
                    </div>

                    <div class="hidden md:block">
                        <DataTable
                            ref="dt"
                            v-model:selection="purchaseStore.selectedPurchases"
                            v-model:expandedRows="expandedRows"
                            :value="purchaseStore.purchases"
                            dataKey="id"
                            :paginator="true"
                            :rows="rowsPerPage"
                            :totalRecords="purchaseStore.pagination.total"
                            :loading="purchaseStore.loading"
                            :first="(currentPage - 1) * rowsPerPage"
                            paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                            :rowsPerPageOptions="[5, 10, 25, 50]"
                            currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} compras"
                            :lazy="true"
                            @page="onPageChange"
                            @sort="onSort"
                            sortMode="single"
                            :sortField="sortField"
                            :sortOrder="sortOrder === 'asc' ? 1 : -1"
                            class="hidden md:block"
                        >
                            <template #header>
                                <div class="flex flex-wrap gap-2 items-center justify-between">
                                    <h4 class="m-0 flex items-center gap-2">
                                        <span class="bg-gray-100 text-gray-600 rounded-full w-8 h-8 flex items-center justify-center">
                                            <i class="pi pi-fw pi-shopping-cart"></i>
                                        </span>
                                        Compras
                                    </h4>
                                    <div class="flex gap-2">
                                        <IconField>
                                            <InputIcon>
                                                <i class="pi pi-search" />
                                            </InputIcon>
                                            <InputText v-model="searchTerm" placeholder="Buscar compra..." @keyup.enter="onSearch" />
                                        </IconField>
                                        <Button icon="pi pi-search" severity="secondary" @click="onSearch" :loading="purchaseStore.loading" v-tooltip.top="'Buscar'" />
                                    </div>
                                </div>
                            </template>
                            <Column expander style="width: 5rem" />

                            <Column field="invoice_number" header="Número da Nota" sortable style="min-width: 16rem">
                                <template #body="slotProps">
                                    {{ slotProps.data.invoiceNumber }}
                                </template>
                            </Column>

                            <Column field="purchase_date" header="Data da Compra" sortable style="min-width: 12rem">
                                <template #body="slotProps">
                                    {{ slotProps.data.purchaseDate }}
                                </template>
                            </Column>

                            <Column field="total_amount" header="Valor Total" sortable style="min-width: 12rem">
                                <template #body="slotProps">
                                    {{ formatCurrency(slotProps.data.totalAmount) }}
                                </template>
                            </Column>

                            <Column field="supplier_id" header="Fornecedor" sortable style="min-width: 10rem">
                                <template #body="slotProps">
                                    {{ slotProps.data.supplier?.name }}
                                </template>
                            </Column>

                            <Column field="status" header="Status" sortable style="min-width: 8rem">
                                <template #body="slotProps">
                                    <Tag :value="getStatusTag(slotProps.data.status).label" :severity="getStatusTag(slotProps.data.status).severity" />
                                </template>
                            </Column>

                            <Column field="created_at" header="Criado em" sortable style="min-width: 8rem">
                                <template #body="slotProps">
                                    {{ slotProps.data.created }}
                                </template>
                            </Column>

                            <Column header="Ações" :exportable="false" style="min-width: 12rem">
                                <template #body="slotProps">
                                    <Button icon="pi pi-eye" outlined rounded class="mr-2" severity="info" @click="openViewPurchase(slotProps.data)" v-tooltip.top="'Ver Detalhes'" />
                                    <Button v-if="slotProps.data.status !== 'cancelado'" icon="pi pi-whatsapp" outlined rounded severity="success" class="mr-2" @click="sendToWhatsApp(slotProps.data)" v-tooltip.top="'Enviar Lista via WhatsApp'" />
                                    <Button v-if="slotProps.data.status !== 'cancelado'" icon="pi pi-pencil" outlined rounded class="mr-2" @click="openUpdatePurchase(slotProps.data)" v-tooltip.top="'Editar Compra'" />
                                    <Button v-if="slotProps.data.status !== 'cancelado'" icon="pi pi-trash" outlined rounded severity="danger" class="mr-2" @click="openDestroyPurchase(slotProps.data)" v-tooltip.top="'Cancelar Compra'" />
                                </template>
                            </Column>

                            <template #expansion="slotProps">
                                <div class="p-4">
                                    <h5 class="mb-4">Produtos da compra {{ slotProps.data.invoiceNumber }}</h5>
                                    <DataTable v-if="slotProps.data.products && slotProps.data.products.length > 0" :value="slotProps.data.products">
                                        <Column field="name" header="Nome do Produto" sortable></Column>

                                        <Column field="total" header="Quantidade" sortable>
                                            <template #body="productSlot">
                                                <Tag :value="productSlot.data.total" severity="info" />
                                            </template>
                                        </Column>

                                        <Column field="average_cost" header="Custo Médio" sortable>
                                            <template #body="productSlot">
                                                {{ formatCurrency(productSlot.data.average_cost) }}
                                            </template>
                                        </Column>
                                    </DataTable>
                                    <div v-else class="text-center py-4">
                                        <i class="pi pi-inbox text-4xl text-gray-400 mb-2"></i>
                                        <p class="text-gray-500">Nenhum produto encontrado nesta compra</p>
                                    </div>
                                </div>
                            </template>

                            <template #empty>
                                <div class="text-center py-8 text-gray-500">
                                    <i class="pi pi-fw pi-shopping-cart text-4xl mb-2"></i>
                                    <p>Nenhuma compra encontrada</p>
                                </div>
                            </template>
                        </DataTable>
                    </div>

                    <div class="block md:hidden">
                        <div v-if="purchaseStore.loading" class="text-center py-8">
                            <ProgressSpinner />
                        </div>

                        <div v-else-if="purchaseStore.purchases.length === 0" class="text-center py-8 text-gray-500">
                            <i class="pi pi-fw pi-shopping-cart text-4xl mb-2"></i>
                            <p>Nenhuma compra encontrada</p>
                        </div>

                        <div v-else class="space-y-4">
                            <Card v-for="purchase in purchaseStore.purchases" :key="purchase.id" class="h-full flex flex-col rounded-none sm:rounded-xl border border-gray-200 dark:border-gray-700">
                                <template #content>
                                    <div class="p-4 space-y-4 flex-grow">
                                        <div class="flex justify-between items-start mb-3">
                                            <div class="flex-1">
                                                <h3 class="font-semibold text-lg text-gray-900 dark:text-gray-100">{{ purchase.invoiceNumber }}</h3>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ purchase.supplier?.name }}</p>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-2 gap-4 mb-4">
                                            <div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Valor Total</p>
                                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ formatCurrency(purchase.totalAmount) }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Data da Compra</p>
                                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ purchase.purchaseDate || '' }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Status</p>
                                                <Tag :value="getStatusTag(purchase.status).label" :severity="getStatusTag(purchase.status).severity" />
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Criado em</p>
                                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ purchase.created }}</p>
                                            </div>
                                        </div>

                                        <div class="pt-3 border-t border-gray-100 dark:border-gray-700">
                                            <div class="grid grid-cols-1 gap-2 font-sans">
                                                <Button label="Ver Detalhes" size="small" outlined class="w-full font-sans text-xs" severity="info" @click="openViewPurchase(purchase)" />
                                                <Button label="Enviar Lista via WhatsApp" size="small" outlined severity="success" class="w-full font-sans text-xs" @click="sendToWhatsApp(purchase)" />
                                                <Button v-if="purchase.status !== 'cancelado'" label="Editar" size="small" outlined class="w-full font-sans text-xs" @click="openUpdatePurchase(purchase)" />
                                                <Button v-if="purchase.status !== 'cancelado'" label="Cancelar Compra" size="small" outlined severity="danger" class="w-full font-sans text-xs" @click="openDestroyPurchase(purchase)" />
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </Card>
                        </div>

                        <div v-if="purchaseStore.purchases.length > 0" class="mt-6 flex justify-center">
                            <Paginator
                                :first="(currentPage - 1) * rowsPerPage"
                                :rows="rowsPerPage"
                                :totalRecords="purchaseStore.pagination.total"
                                @page="onPaginatorPageChange"
                                template="FirstPageLink PrevPageLink CurrentPageReport NextPageLink LastPageLink"
                                currentPageReportTemplate="Página {currentPage} de {totalPages}"
                            />
                        </div>
                    </div>
                </div>

                <Dialog v-model:visible="purchaseDialog" modal header="Detalhes da Compra" :style="{ width: '50vw' }" :breakpoints="{ '960px': '75vw', '640px': '100vw' }" :maximizable="true">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="col-span-1 md:col-span-3">
                            <label for="invoiceNumber" class="flex items-center justify-between font-bold mb-3">
                                <span>Número da Nota <span class="text-red-500">*</span></span>
                                <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:inline" v-tooltip.top="'Número identificador da nota fiscal'" />
                            </label>
                            <InputText id="invoiceNumber" v-model.trim="purchaseStore.purchase.invoiceNumber" required autofocus :invalid="submitted && !purchaseStore.purchase.invoiceNumber" placeholder="Digite o número da nota" fluid />
                            <small v-if="submitted && !purchaseStore.purchase.invoiceNumber" class="text-red-500">Número da nota é obrigatório.</small>
                        </div>

                        <div>
                            <label for="totalAmount" class="flex items-center justify-between font-bold mb-3">
                                <span>Valor Total <span class="text-red-500">*</span></span>
                                <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:inline" v-tooltip.top="'Valor monetário total da compra'" />
                            </label>
                            <InputNumber
                                id="totalAmount"
                                v-model="purchaseStore.purchase.totalAmount"
                                mode="currency"
                                currency="BRL"
                                locale="pt-BR"
                                :invalid="submitted && !purchaseStore.purchase.totalAmount"
                                placeholder="0,00"
                                showButtons
                                buttonLayout="horizontal"
                                :step="1"
                                fluid
                            >
                                <template #incrementicon>
                                    <span class="pi pi-plus" />
                                </template>
                                <template #decrementicon>
                                    <span class="pi pi-minus" />
                                </template>
                            </InputNumber>
                            <small v-if="submitted && !purchaseStore.purchase.totalAmount" class="text-red-500">Valor total é obrigatório.</small>
                            <small class="text-gray-500 block mt-1 md:hidden">Se tiver dificuldade em digitar o valor, apague tudo e digite novamente.</small>
                        </div>

                        <div>
                            <label for="supplierId" class="flex items-center justify-between font-bold mb-3">
                                <span>Fornecedor <span class="text-red-500">*</span></span>
                                <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:inline" v-tooltip.top="'Fornecedor da compra'" />
                            </label>
                            <Select
                                id="supplierId"
                                v-model="purchaseStore.purchase.supplierId"
                                :options="supplierOptions"
                                optionLabel="label"
                                optionValue="value"
                                placeholder="Selecione o fornecedor"
                                :invalid="submitted && !purchaseStore.purchase.supplierId"
                                filter
                                fluid
                            />
                            <small v-if="submitted && !purchaseStore.purchase.supplierId" class="text-red-500">Fornecedor é obrigatório.</small>
                        </div>

                        <div>
                            <label for="purchaseDate" class="flex items-center justify-between font-bold mb-3">
                                <span>Data da Compra <span class="text-red-500">*</span></span>
                                <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:inline" v-tooltip.top="'Data em que a compra foi realizada'" />
                            </label>
                            <DatePicker id="purchaseDate" v-model="purchaseStore.purchase.purchaseDate" dateFormat="dd/mm/yy" placeholder="Selecione a data" :invalid="submitted && !purchaseStore.purchase.purchaseDate" fluid />
                            <small v-if="submitted && !purchaseStore.purchase.purchaseDate" class="text-red-500">Data da compra é obrigatória.</small>
                        </div>
                    </div>

                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold">Itens da Compra</h3>
                            <Button label="Adicionar Item" icon="pi pi-plus" size="small" @click="addItem" />
                        </div>

                        <div v-for="(item, index) in purchaseItems" :key="index" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end mb-4 p-4 border rounded">
                            <div>
                                <label :for="`product-${index}`" class="font-bold mb-3">Produto <span class="text-red-500">*</span></label>
                                <Select :id="`product-${index}`" v-model="item.productId" :options="productOptions" optionLabel="label" optionValue="value" :invalid="submitted && !item.productId" placeholder="Selecione um produto" fluid />
                            </div>

                            <div>
                                <label :for="`warehouse-${index}`" class="font-bold mb-3">Armazém <span class="text-red-500">*</span></label>
                                <Select :id="`warehouse-${index}`" v-model="item.warehouseId" :options="warehouseOptions" optionLabel="label" optionValue="value" :invalid="submitted && !item.warehouseId" placeholder="Selecione um armazém" fluid />
                            </div>

                            <div>
                                <label :for="`quantity-${index}`" class="font-bold mb-3">Quantidade <span class="text-red-500">*</span></label>
                                <InputNumber :id="`quantity-${index}`" v-model="item.quantity" :min="1" :invalid="submitted && (!item.quantity || item.quantity <= 0)" placeholder="Quantidade" fluid />
                            </div>

                            <div>
                                <Button icon="pi pi-trash" severity="danger" outlined @click="removeItem(index)" :disabled="purchaseItems.length === 1" />
                            </div>
                        </div>

                        <small v-if="submitted && purchaseItems.length === 0" class="text-red-500">Adicione pelo menos um item à compra.</small>
                    </div>

                    <template #footer>
                        <Button label="Cancelar" icon="pi pi-times" text @click="hideDialog" />
                        <Button label="Salvar" icon="pi pi-check" @click="savePurchase" />
                    </template>
                </Dialog>

                <Dialog v-model:visible="editPurchaseDialog" modal header="Editar Compra" :style="{ width: '50vw' }" :breakpoints="{ '960px': '75vw', '640px': '100vw' }" :maximizable="true">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="col-span-1 md:col-span-3">
                            <label for="editInvoiceNumber" class="flex items-center justify-between font-bold mb-3">
                                <span>Número da Nota <span class="text-red-500">*</span></span>
                                <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:inline" v-tooltip.top="'Número identificador da nota fiscal'" />
                            </label>
                            <InputText id="editInvoiceNumber" v-model.trim="purchaseStore.purchase.invoiceNumber" required autofocus :invalid="submitted && !purchaseStore.purchase.invoiceNumber" placeholder="Digite o número da nota" fluid />
                            <small v-if="submitted && !purchaseStore.purchase.invoiceNumber" class="text-red-500">Número da nota é obrigatório.</small>
                        </div>

                        <div>
                            <label for="editTotalAmount" class="flex items-center justify-between font-bold mb-3">
                                <span>Valor Total <span class="text-red-500">*</span></span>
                                <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:inline" v-tooltip.top="'Valor monetário total da compra'" />
                            </label>
                            <InputNumber
                                id="editTotalAmount"
                                v-model="purchaseStore.purchase.totalAmount"
                                mode="currency"
                                currency="BRL"
                                locale="pt-BR"
                                :invalid="submitted && !purchaseStore.purchase.totalAmount"
                                placeholder="0,00"
                                showButtons
                                buttonLayout="horizontal"
                                :step="1"
                                fluid
                            >
                                <template #incrementicon>
                                    <span class="pi pi-plus" />
                                </template>
                                <template #decrementicon>
                                    <span class="pi pi-minus" />
                                </template>
                            </InputNumber>
                            <small v-if="submitted && !purchaseStore.purchase.totalAmount" class="text-red-500">Valor total é obrigatório.</small>
                            <small class="text-gray-500 block mt-1 md:hidden">Se tiver dificuldade em digitar o valor, apague tudo e digite novamente.</small>
                        </div>

                        <div>
                            <label for="editSupplierId" class="flex items-center justify-between font-bold mb-3">
                                <span>Fornecedor <span class="text-red-500">*</span></span>
                                <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:inline" v-tooltip.top="'Fornecedor da compra'" />
                            </label>
                            <Select
                                id="editSupplierId"
                                v-model="purchaseStore.purchase.supplierId"
                                :options="supplierOptions"
                                optionLabel="label"
                                optionValue="value"
                                placeholder="Selecione o fornecedor"
                                :invalid="submitted && !purchaseStore.purchase.supplierId"
                                filter
                                fluid
                            />
                            <small v-if="submitted && !purchaseStore.purchase.supplierId" class="text-red-500">Fornecedor é obrigatório.</small>
                        </div>

                        <div>
                            <label for="editPurchaseDate" class="flex items-center justify-between font-bold mb-3">
                                <span>Data da Compra <span class="text-red-500">*</span></span>
                                <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:inline" v-tooltip.top="'Data em que a compra foi realizada'" />
                            </label>
                            <DatePicker id="editPurchaseDate" v-model="purchaseStore.purchase.purchaseDate" dateFormat="dd/mm/yy" placeholder="Selecione a data" :invalid="submitted && !purchaseStore.purchase.purchaseDate" fluid />
                            <small v-if="submitted && !purchaseStore.purchase.purchaseDate" class="text-red-500">Data da compra é obrigatória.</small>
                        </div>
                    </div>

                    <template #footer>
                        <Button label="Cancelar" icon="pi pi-times" text @click="editPurchaseDialog = false" />
                        <Button label="Salvar" icon="pi pi-check" @click="saveEditPurchase" />
                    </template>
                </Dialog>

                <Dialog v-model:visible="viewPurchaseDialog" modal header="Detalhes da Compra" :style="{ width: '50vw' }" :breakpoints="{ '960px': '75vw', '640px': '100vw' }" :maximizable="true">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="font-bold">Número da Nota</label>
                            <p class="mt-1 text-gray-700">{{ purchaseStore.purchase.invoiceNumber }}</p>
                        </div>

                        <div>
                            <label class="font-bold">Valor Total</label>
                            <p class="mt-1 text-gray-700">{{ formatCurrency(purchaseStore.purchase.totalAmount) }}</p>
                        </div>

                        <div>
                            <label class="font-bold">Fornecedor</label>
                            <p class="mt-1 text-gray-700">{{ purchaseStore.purchase.supplier?.name }}</p>
                        </div>

                        <div>
                            <label class="font-bold">Data da Compra</label>
                            <p class="mt-1 text-gray-700">{{ purchaseStore.purchase.purchaseDate }}</p>
                        </div>

                        <div>
                            <label class="font-bold">Status</label>
                            <div class="mt-1">
                                <Tag :value="getStatusTag(purchaseStore.purchase.status).label" :severity="getStatusTag(purchaseStore.purchase.status).severity" />
                            </div>
                        </div>

                        <div>
                            <label class="font-bold">Criado em</label>
                            <p class="mt-1 text-gray-700">{{ purchaseStore.purchase.created }}</p>
                        </div>
                    </div>

                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-semibold mb-4">Produtos da Compra</h3>
                        <DataTable v-if="purchaseStore.purchase.products && purchaseStore.purchase.products.length > 0" :value="purchaseStore.purchase.products" class="p-datatable-sm">
                            <Column field="name" header="Nome do Produto" sortable></Column>

                            <Column field="total" header="Quantidade" sortable>
                                <template #body="productSlot">
                                    <Tag :value="productSlot.data.total" severity="info" />
                                </template>
                            </Column>

                            <Column field="average_cost" header="Custo Médio" sortable>
                                <template #body="productSlot">
                                    {{ formatCurrency(productSlot.data.average_cost) }}
                                </template>
                            </Column>
                        </DataTable>
                        <div v-else class="text-center py-4">
                            <i class="pi pi-inbox text-4xl text-gray-400 mb-2"></i>
                            <p class="text-gray-500">Nenhum produto encontrado nesta compra</p>
                        </div>
                    </div>

                    <template #footer>
                        <Button label="Fechar" icon="pi pi-times" @click="viewPurchaseDialog = false" />
                    </template>
                </Dialog>

                <Dialog v-model:visible="deletePurchaseDialog" :style="{ width: '450px' }" header="Confirmar" :modal="true">
                    <div class="flex items-center gap-4">
                        <i class="pi pi-exclamation-triangle !text-3xl" />
                        <span v-if="purchaseStore.purchase">
                            Tem certeza de que deseja deletar a compra <b>{{ purchaseStore.purchase.invoiceNumber }}</b
                            >?
                        </span>
                    </div>

                    <template #footer>
                        <Button label="Não" icon="pi pi-times" text @click="deletePurchaseDialog = false" />
                        <Button label="Sim" icon="pi pi-check" @click="deletePurchase" />
                    </template>
                </Dialog>

                <Dialog v-model:visible="deletePurchasesDialog" :style="{ width: '450px' }" header="Confirmar" :modal="true">
                    <div class="flex items-center gap-4">
                        <i class="pi pi-exclamation-triangle !text-3xl" />
                        <span v-if="purchaseStore.selectedPurchases"> Tem certeza de que deseja deletar as compras selecionadas? </span>
                    </div>

                    <template #footer>
                        <Button label="Não" icon="pi pi-times" text @click="deletePurchasesDialog = false" />
                        <Button label="Sim" icon="pi pi-check" @click="deleteSelectedPurchases" />
                    </template>
                </Dialog>
            </div>
        </div>
    </div>
</template>
