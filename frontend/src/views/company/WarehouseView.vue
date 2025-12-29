<script setup lang="ts">
import { onMounted, ref, watch } from 'vue';
import { useWarehouseStore } from '@/stores/company/warehouseStore';
import type { Warehouse } from '@/types/company/warehouse';
import type { WarehousePayload } from '@/types/company/warehouse';
import type { WarehouseFilters } from '@/types/company/filters/warehouseFilter';
import type { DataTableSortEvent, DataTablePageEvent } from 'primevue/datatable';
import { useToast } from 'primevue/usetoast';
import { useKeyboardShortcuts } from '@/utils/useKeyboardShortcuts';
import { useFormat } from '@/utils/useFormat';

const warehouseStore = useWarehouseStore();

const searchTerm = ref('');
const currentPage = ref(1);
const rowsPerPage = ref(10);
const sortField = ref('id');
const sortOrder = ref<'asc' | 'desc'>('asc');
const expandedRows = ref([]);

const warehouseDialog = ref(false);
const deleteWarehouseDialog = ref(false);
const deleteWarehousesDialog = ref(false);
const submitted = ref(false);

const toast = useToast();
const { formatCurrency } = useFormat();

useKeyboardShortcuts(openCreateWarehouse, saveWarehouse, warehouseDialog, exportCSV);

watch(
    () => warehouseStore.pagination?.currentPage,
    (newPage) => {
        if (newPage && newPage !== currentPage.value) {
            currentPage.value = newPage;
        }
    }
);

onMounted(async () => {
    try {
        await loadWarehouses();
    } catch {
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: 'Erro ao carregar dados da página',
            life: 5000
        });
    }
});

async function loadWarehouses(): Promise<void> {
    const filters: WarehouseFilters = {
        search: searchTerm.value || undefined,
        sort: sortField.value,
        direction: sortOrder.value,
        perPage: rowsPerPage.value,
        page: currentPage.value
    };

    await warehouseStore.fetchWarehouses(filters);
}

async function onSearch(): Promise<void> {
    currentPage.value = 1;
    await loadWarehouses();
}

async function onPageChange(event: DataTablePageEvent): Promise<void> {
    currentPage.value = event.page + 1;
    rowsPerPage.value = event.rows;
    await loadWarehouses();
}

async function onPaginatorPageChange(event: { page: number; first: number; rows: number }): Promise<void> {
    currentPage.value = event.page + 1;
    rowsPerPage.value = event.rows;
    await loadWarehouses();
}

async function onSort(event: DataTableSortEvent): Promise<void> {
    if (typeof event.sortField === 'string') {
        sortField.value = event.sortField;
        sortOrder.value = event.sortOrder === 1 ? 'asc' : 'desc';
        await loadWarehouses();
    }
}

async function saveWarehouse(): Promise<void> {
    submitted.value = true;

    if (warehouseStore.warehouse.name?.trim()) {
        try {
            const payload: WarehousePayload = {
                name: warehouseStore.warehouse.name,
                location: warehouseStore.warehouse.location || undefined
            };

            if (warehouseStore.warehouse.id) {
                await warehouseStore.updateWarehouse(warehouseStore.warehouse.id, payload);
                toast.add({ severity: 'success', summary: 'Sucesso', detail: 'Armazém atualizado com sucesso', life: 3000 });
            } else {
                await warehouseStore.createWarehouse(payload);
                toast.add({ severity: 'success', summary: 'Sucesso', detail: 'Armazém criado com sucesso', life: 3000 });
            }
            hideDialog();
            await loadWarehouses();
        } catch {
            toast.add({ severity: 'error', summary: 'Erro', detail: warehouseStore.error, life: 3000 });
        }
    }
}

async function deleteWarehouse(): Promise<void> {
    if (warehouseStore.warehouse.id) {
        try {
            await warehouseStore.deleteWarehouse(warehouseStore.warehouse.id);
            toast.add({ severity: 'success', summary: 'Sucesso', detail: 'Armazém removido com sucesso', life: 3000 });
            deleteWarehouseDialog.value = false;
            await loadWarehouses();
        } catch {
            toast.add({ severity: 'error', summary: 'Erro', detail: warehouseStore.error, life: 3000 });
        }
    }
}

async function deleteSelectedWarehouses(): Promise<void> {
    try {
        await warehouseStore.deleteSelectedWarehouses();
        toast.add({ severity: 'success', summary: 'Sucesso', detail: 'Armazéns selecionados removidos com sucesso', life: 3000 });
        deleteWarehousesDialog.value = false;
        await loadWarehouses();
    } catch {
        toast.add({ severity: 'error', summary: 'Erro', detail: warehouseStore.error, life: 3000 });
    }
}

function openCreateWarehouse(): void {
    warehouseStore.clearWarehouse();
    submitted.value = false;
    warehouseDialog.value = true;
}

function openUpdateWarehouse(warehouseData: Warehouse): void {
    warehouseStore.warehouse = { ...warehouseData };
    warehouseDialog.value = true;
}

function openDestroyWarehouse(warehouseData: Warehouse): void {
    warehouseStore.warehouse = warehouseData;
    deleteWarehouseDialog.value = true;
}

function openDestroySelected(): void {
    deleteWarehousesDialog.value = true;
}

function hideDialog(): void {
    warehouseDialog.value = false;
    submitted.value = false;
}

function exportCSV(): void {
    const data = warehouseStore.warehouses.map((warehouse) => ({
        ID: warehouse.id,
        Nome: warehouse.name,
        Localização: warehouse.location || '',
        'Data de Criação': warehouse.created,
        'Valor Produtos em Estoque': warehouse.totalStockValue,
        'Produtos em Estoque': warehouse.totalStock,
        'Produtos Vendidos': warehouse.totalSold,
        'Produtos Indisponíveis': warehouse.totalUnavailable
    }));

    const headers = Object.keys(data[0]);
    const csvContent = [headers.join(','), ...data.map((row) => headers.map((header) => `"${row[header as keyof typeof row]}"`).join(','))].join('\n');

    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', 'armazens.csv');
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>

<template>
    <div class="space-y-6 lg:space-y-8">
        <div class="lg:grid-cols-12 gap-6">
            <div class="lg:col-span-4 -mx-4 sm:mx-0">
                <div class="card">
                    <Toolbar class="mb-6">
                        <template #start>
                            <div class="flex-wrap gap-2 items-center hidden md:flex">
                                <Button label="Adicionar Armazém (F1)" icon="pi pi-plus" severity="secondary" class="mr-2" @click="openCreateWarehouse" v-tooltip.top="'Cadastrar novo armazém (F1)'" />
                            </div>
                            <Button
                                label="Deletar"
                                icon="pi pi-trash"
                                severity="secondary"
                                @click="openDestroySelected"
                                :disabled="!warehouseStore.selectedWarehouses || !warehouseStore.selectedWarehouses.length"
                                v-tooltip.top="'Excluir armazéns selecionados'"
                            />
                        </template>

                        <template #end>
                            <Button label="Exportar (F4)" icon="pi pi-upload" severity="secondary" @click="exportCSV" v-tooltip.top="'Exportar Dados da Tabela'" />
                        </template>
                    </Toolbar>

                    <div class="block md:hidden mb-6">
                        <div class="grid grid-cols-1 gap-4">
                            <Button label="Adicionar Armazém" icon="pi pi-plus" severity="secondary" @click="openCreateWarehouse" class="w-full" />
                            <div class="mt-4 flex gap-2">
                                <IconField class="flex-1">
                                    <InputIcon>
                                        <i class="pi pi-search" />
                                    </InputIcon>
                                    <InputText v-model="searchTerm" placeholder="Buscar armazém..." @keyup.enter="onSearch" class="w-full" />
                                </IconField>
                                <Button icon="pi pi-search" severity="secondary" @click="onSearch" :loading="warehouseStore.loading" />
                            </div>
                        </div>
                    </div>

                    <div class="hidden md:block">
                        <DataTable
                            ref="dt"
                            v-model:selection="warehouseStore.selectedWarehouses"
                            :value="warehouseStore.warehouses"
                            dataKey="id"
                            :paginator="true"
                            :rows="rowsPerPage"
                            :totalRecords="warehouseStore.pagination.total"
                            :loading="warehouseStore.loading"
                            :first="(currentPage - 1) * rowsPerPage"
                            paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                            :rowsPerPageOptions="[5, 10, 25, 50]"
                            currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} armazéns"
                            :lazy="true"
                            @page="onPageChange"
                            @sort="onSort"
                            sortMode="single"
                            :sortField="sortField"
                            :sortOrder="sortOrder === 'asc' ? 1 : -1"
                            v-model:expandedRows="expandedRows"
                            class="hidden md:block"
                        >
                            <template #header>
                                <div class="flex flex-wrap gap-2 items-center justify-between">
                                    <h4 class="m-0 flex items-center gap-2">
                                        <span class="bg-gray-100 text-gray-600 rounded-full w-8 h-8 flex items-center justify-center">
                                            <i class="pi pi-warehouse"></i>
                                        </span>
                                        Armazéns
                                    </h4>
                                    <div class="flex gap-2">
                                        <IconField>
                                            <InputIcon>
                                                <i class="pi pi-search" />
                                            </InputIcon>
                                            <InputText v-model="searchTerm" placeholder="Buscar armazém..." @keyup.enter="onSearch" />
                                        </IconField>
                                        <Button icon="pi pi-search" severity="secondary" @click="onSearch" :loading="warehouseStore.loading" v-tooltip.top="'Buscar'" />
                                    </div>
                                </div>
                            </template>

                            <Column :expander="true" headerStyle="width: 3rem" />

                            <Column field="name" header="Nome" sortable style="min-width: 16rem">
                                <template #body="slotProps">
                                    {{ slotProps.data.name }}
                                </template>
                            </Column>

                            <Column field="location" header="Localização" sortable style="min-width: 20rem">
                                <template #body="slotProps">
                                    {{ slotProps.data.location || '-' }}
                                </template>
                            </Column>

                            <Column field="totalStock" header="Produtos em Estoque" style="min-width: 12rem">
                                <template #body="slotProps">
                                    {{ slotProps.data.totalStock }}
                                </template>
                            </Column>

                            <Column field="totalSold" header="Produtos Vendidos" style="min-width: 12rem">
                                <template #body="slotProps">
                                    {{ slotProps.data.totalSold }}
                                </template>
                            </Column>

                            <Column field="totalUnavailable" header="Produtos Indisponíveis" style="min-width: 12rem">
                                <template #body="slotProps"> {{ slotProps.data.totalUnavailable }} </template>
                            </Column>

                            <Column field="created_at" header="Criado em" sortable style="min-width: 12rem">
                                <template #body="slotProps">
                                    {{ slotProps.data.created }}
                                </template>
                            </Column>

                            <Column :exportable="false" style="min-width: 12rem">
                                <template #body="slotProps">
                                    <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="openUpdateWarehouse(slotProps.data)" v-tooltip.top="'Editar Armazém'" />
                                    <Button icon="pi pi-trash" outlined rounded severity="danger" @click="openDestroyWarehouse(slotProps.data)" v-tooltip.top="'Excluir Armazém'" />
                                </template>
                            </Column>

                            <template #expansion="slotProps">
                                <div class="p-4">
                                    <h5 class="mb-4">Produtos no armazém {{ slotProps.data.name }}</h5>
                                    <DataTable v-if="slotProps.data.products && slotProps.data.products.length > 0" :value="slotProps.data.products">
                                        <Column field="name" header="Nome do Produto" sortable></Column>
                                        <Column field="total" header="Produtos em Estoque" sortable>
                                            <template #body="productSlot">
                                                <Tag :value="productSlot.data.total" severity="info" />
                                            </template>
                                        </Column>
                                        <Column field="totalSold" header="Produtos Vendidos" sortable>
                                            <template #body="productSlot">
                                                <Tag :value="productSlot.data.totalSold" severity="warning" />
                                            </template>
                                        </Column>
                                        <Column field="averageCost" header="Custo Médio" sortable>
                                            <template #body="productSlot"> {{ formatCurrency(productSlot.data.averageCost) }} </template>
                                        </Column>
                                    </DataTable>
                                    <div v-else class="text-center py-4 flex flex-col items-center">
                                        <span class="pi pi-warehouse text-4xl text-gray-400 mb-2"></span>
                                        <p class="text-gray-500">Nenhum produto encontrado neste armazém</p>
                                    </div>
                                </div>
                            </template>

                            <template #empty>
                                <div class="text-center py-8 text-gray-500">
                                    <i class="pi pi-warehouse text-4xl mb-2"></i>
                                    <p>Nenhum armazém encontrado</p>
                                </div>
                            </template>
                        </DataTable>
                    </div>

                    <div class="block md:hidden">
                        <div v-if="warehouseStore.loading" class="text-center py-8">
                            <ProgressSpinner />
                        </div>
                        <div v-else-if="warehouseStore.warehouses.length === 0" class="text-center py-8 text-gray-500">
                            <i class="pi pi-warehouse text-4xl mb-2"></i>
                            <p>Nenhum armazém encontrado</p>
                        </div>
                        <div v-else class="space-y-4">
                            <Card v-for="warehouse in warehouseStore.warehouses" :key="warehouse.id" class="h-full flex flex-col rounded-none sm:rounded-xl border border-gray-200 dark:border-gray-700">
                                <template #content>
                                    <div class="p-4 space-y-4 flex-grow">
                                        <div class="flex justify-between items-start mb-3">
                                            <div class="flex-1">
                                                <h3 class="font-semibold text-lg text-gray-900 dark:text-gray-100">{{ warehouse.name }}</h3>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 gap-4 mb-4">
                                            <div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Localização</p>
                                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ warehouse.location || '-' }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Produtos em Estoque</p>
                                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ warehouse.totalStock }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Produtos Vendidos</p>
                                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ warehouse.totalSold }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Produtos Indisponíveis</p>
                                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ warehouse.totalUnavailable }}</p>
                                            </div>
                                        </div>

                                        <div class="mb-4 pb-4 border-b border-gray-100 dark:border-gray-700">
                                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Criado em</p>
                                            <p class="text-sm text-gray-700 dark:text-gray-300">{{ warehouse.created }}</p>
                                        </div>

                                        <div class="pt-3">
                                            <div class="grid grid-cols-1 gap-2 font-sans">
                                                <Button label="Editar" size="small" outlined class="w-full font-sans text-xs" @click="openUpdateWarehouse(warehouse)" />
                                                <Button label="Excluir" size="small" outlined severity="danger" class="w-full font-sans text-xs" @click="openDestroyWarehouse(warehouse)" />
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </Card>
                        </div>

                        <div v-if="warehouseStore.warehouses.length > 0" class="mt-6 flex justify-center">
                            <Paginator
                                :first="(currentPage - 1) * rowsPerPage"
                                :rows="rowsPerPage"
                                :totalRecords="warehouseStore.pagination.total"
                                @page="onPaginatorPageChange"
                                template="FirstPageLink PrevPageLink CurrentPageReport NextPageLink LastPageLink"
                                currentPageReportTemplate="Página {currentPage} de {totalPages}"
                            />
                        </div>
                    </div>
                </div>

                <Dialog v-model:visible="warehouseDialog" modal header="Detalhes do Armazém" :style="{ width: '50vw' }" :breakpoints="{ '960px': '75vw', '640px': '100vw' }" :maximizable="true">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="name" class="flex items-center justify-between font-bold mb-3">
                                <span>Nome <span class="text-red-500">*</span></span>
                                <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:block" v-tooltip.top="'Nome identificador do armazém'" />
                            </label>
                            <InputText id="name" v-model.trim="warehouseStore.warehouse.name" required autofocus :invalid="submitted && !warehouseStore.warehouse.name" placeholder="Digite o nome do armazém" fluid />
                            <small v-if="submitted && !warehouseStore.warehouse.name" class="text-red-500">Nome é obrigatório.</small>
                        </div>

                        <div>
                            <label for="location" class="flex items-center justify-between font-bold mb-3">
                                <span>Localização</span>
                                <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:block" v-tooltip.top="'Localização física do armazém'" />
                            </label>
                            <InputText id="location" v-model="warehouseStore.warehouse.location" placeholder="Digite a localização do armazém" fluid />
                        </div>
                    </div>

                    <template #footer>
                        <Button label="Cancelar" icon="pi pi-times" text @click="hideDialog" />
                        <Button label="Salvar" icon="pi pi-check" @click="saveWarehouse" />
                    </template>
                </Dialog>

                <Dialog v-model:visible="deleteWarehouseDialog" :style="{ width: '450px' }" header="Confirmar" :modal="true">
                    <div class="flex items-center gap-4">
                        <i class="pi pi-exclamation-triangle !text-3xl" />
                        <span v-if="warehouseStore.warehouse">
                            Tem certeza que deseja deletar o armazém <b>{{ warehouseStore.warehouse.name }}</b
                            >?
                        </span>
                    </div>
                    <template #footer>
                        <Button label="Não" icon="pi pi-times" text @click="deleteWarehouseDialog = false" />
                        <Button label="Sim" icon="pi pi-check" @click="deleteWarehouse" />
                    </template>
                </Dialog>

                <Dialog v-model:visible="deleteWarehousesDialog" :style="{ width: '450px' }" header="Confirmar" :modal="true">
                    <div class="flex items-center gap-4">
                        <i class="pi pi-exclamation-triangle !text-3xl" />
                        <span>Tem certeza que deseja deletar os armazéns selecionados?</span>
                    </div>
                    <template #footer>
                        <Button label="Não" icon="pi pi-times" text @click="deleteWarehousesDialog = false" />
                        <Button label="Sim" icon="pi pi-check" text @click="deleteSelectedWarehouses" />
                    </template>
                </Dialog>
            </div>
        </div>
    </div>
</template>
