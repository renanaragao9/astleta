<script setup lang="ts">
import { onMounted, ref, watch } from 'vue';
import { useSupplierStore } from '@/stores/company/supplierStore';
import type { Supplier } from '@/types/company/supplier';
import type { SupplierPayload } from '@/types/company/supplier';
import type { SupplierFilters } from '@/types/company/filters/supplierFilter';
import type { DataTableSortEvent, DataTablePageEvent } from 'primevue/datatable';
import { useToast } from 'primevue/usetoast';
import { useKeyboardShortcuts } from '@/utils/useKeyboardShortcuts';

const supplierStore = useSupplierStore();

const searchTerm = ref('');
const currentPage = ref(1);
const rowsPerPage = ref(10);
const sortField = ref('id');
const sortOrder = ref<'asc' | 'desc'>('asc');

const supplierDialog = ref(false);
const deleteSupplierDialog = ref(false);
const deleteSuppliersDialog = ref(false);
const submitted = ref(false);
const emailInvalid = ref(false);

const toast = useToast();

useKeyboardShortcuts(openCreateSupplier, saveSupplier, supplierDialog, exportCSV);

watch(
    () => supplierStore.pagination?.currentPage,
    (newPage) => {
        if (newPage && newPage !== currentPage.value) {
            currentPage.value = newPage;
        }
    }
);

onMounted(async () => {
    try {
        await loadSuppliers();
    } catch {
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: 'Erro ao carregar dados da página',
            life: 5000
        });
    }
});

async function loadSuppliers(): Promise<void> {
    const filters: SupplierFilters = {
        search: searchTerm.value || undefined,
        sort: sortField.value,
        direction: sortOrder.value,
        perPage: rowsPerPage.value,
        page: currentPage.value
    };

    await supplierStore.fetchSuppliers(filters);
}

async function onSearch(): Promise<void> {
    currentPage.value = 1;
    await loadSuppliers();
}

async function onPageChange(event: DataTablePageEvent): Promise<void> {
    currentPage.value = event.page + 1;
    rowsPerPage.value = event.rows;
    await loadSuppliers();
}

async function onPaginatorPageChange(event: { page: number; first: number; rows: number }): Promise<void> {
    currentPage.value = event.page + 1;
    rowsPerPage.value = event.rows;
    await loadSuppliers();
}

async function onSort(event: DataTableSortEvent): Promise<void> {
    if (typeof event.sortField === 'string') {
        sortField.value = event.sortField;
        sortOrder.value = event.sortOrder === 1 ? 'asc' : 'desc';
        await loadSuppliers();
    }
}

async function saveSupplier(): Promise<void> {
    submitted.value = true;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    emailInvalid.value = supplierStore.supplier.email ? !emailRegex.test(supplierStore.supplier.email) : false;

    if (supplierStore.supplier.name?.trim() && !emailInvalid.value) {
        try {
            const payload: SupplierPayload = {
                name: supplierStore.supplier.name,
                email: supplierStore.supplier.email || undefined,
                phone: supplierStore.supplier.phone || undefined,
                address: supplierStore.supplier.address || undefined
            };

            if (supplierStore.supplier.id) {
                await supplierStore.updateSupplier(supplierStore.supplier.id, payload);
                toast.add({ severity: 'success', summary: 'Sucesso', detail: 'Fornecedor atualizado com sucesso', life: 3000 });
            } else {
                await supplierStore.createSupplier(payload);
                toast.add({ severity: 'success', summary: 'Sucesso', detail: 'Fornecedor criado com sucesso', life: 3000 });
            }
            hideDialog();
            await loadSuppliers();
        } catch {
            toast.add({ severity: 'error', summary: 'Erro', detail: supplierStore.error, life: 3000 });
        }
    }
}

async function deleteSupplier(): Promise<void> {
    if (supplierStore.supplier.id) {
        try {
            await supplierStore.deleteSupplier(supplierStore.supplier.id);
            toast.add({ severity: 'success', summary: 'Sucesso', detail: 'Fornecedor removido com sucesso', life: 3000 });
            deleteSupplierDialog.value = false;
            await loadSuppliers();
        } catch {
            toast.add({ severity: 'error', summary: 'Erro', detail: supplierStore.error, life: 3000 });
        }
    }
}

async function deleteSelectedSuppliers(): Promise<void> {
    try {
        await supplierStore.deleteSelectedSuppliers();
        toast.add({ severity: 'success', summary: 'Sucesso', detail: 'Fornecedores selecionados removidos com sucesso', life: 3000 });
        deleteSuppliersDialog.value = false;
        await loadSuppliers();
    } catch {
        toast.add({ severity: 'error', summary: 'Erro', detail: supplierStore.error, life: 3000 });
    }
}

function openCreateSupplier(): void {
    supplierStore.clearSupplier();
    submitted.value = false;
    emailInvalid.value = false;
    supplierDialog.value = true;
}

function openUpdateSupplier(supplierData: Supplier): void {
    supplierStore.supplier = { ...supplierData };
    emailInvalid.value = false;
    supplierDialog.value = true;
}

function openDestroySupplier(supplierData: Supplier): void {
    supplierStore.supplier = supplierData;
    deleteSupplierDialog.value = true;
}

function openDestroySelected(): void {
    deleteSuppliersDialog.value = true;
}

function hideDialog(): void {
    supplierDialog.value = false;
    submitted.value = false;
    emailInvalid.value = false;
}

function exportCSV(): void {
    const data = supplierStore.suppliers.map((supplier) => ({
        Nome: supplier.name,
        Email: supplier.email || '',
        Telefone: supplier.phone || '',
        Endereço: supplier.address || '',
        'Data de Criação': supplier.created
    }));

    const headers = Object.keys(data[0]);
    const csvContent = [headers.join(','), ...data.map((row) => headers.map((header) => `"${row[header as keyof typeof row]}"`).join(','))].join('\n');

    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', 'fornecedores.csv');
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
                                <Button label="Adicionar Fornecedor (F1)" icon="pi pi-plus" severity="secondary" class="mr-2" @click="openCreateSupplier" v-tooltip.top="'Cadastrar novo fornecedor (F1)'" />
                            </div>
                            <Button
                                label="Deletar"
                                icon="pi pi-trash"
                                severity="secondary"
                                @click="openDestroySelected"
                                :disabled="!supplierStore.selectedSuppliers || !supplierStore.selectedSuppliers.length"
                                v-tooltip.top="'Excluir fornecedores selecionados'"
                            />
                        </template>

                        <template #end>
                            <Button label="Exportar (F4)" icon="pi pi-upload" severity="secondary" @click="exportCSV" v-tooltip.top="'Exportar Dados da Tabela'" />
                        </template>
                    </Toolbar>

                    <div class="block md:hidden mb-6">
                        <div class="grid grid-cols-1 gap-4">
                            <Button label="Adicionar Fornecedor" icon="pi pi-plus" severity="secondary" @click="openCreateSupplier" class="w-full" />
                            <div class="mt-4 flex gap-2">
                                <IconField class="flex-1">
                                    <InputIcon>
                                        <i class="pi pi-search" />
                                    </InputIcon>
                                    <InputText v-model="searchTerm" placeholder="Buscar fornecedor..." @keyup.enter="onSearch" class="w-full" />
                                </IconField>
                                <Button icon="pi pi-search" severity="secondary" @click="onSearch" :loading="supplierStore.loading" />
                            </div>
                        </div>
                    </div>

                    <div class="hidden md:block">
                        <DataTable
                            ref="dt"
                            v-model:selection="supplierStore.selectedSuppliers"
                            :value="supplierStore.suppliers"
                            dataKey="id"
                            :paginator="true"
                            :rows="rowsPerPage"
                            :totalRecords="supplierStore.pagination.total"
                            :loading="supplierStore.loading"
                            :first="(currentPage - 1) * rowsPerPage"
                            paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                            :rowsPerPageOptions="[5, 10, 25, 50]"
                            currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} fornecedores"
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
                                            <i class="pi pi-fw pi-truck"></i>
                                        </span>
                                        Fornecedores
                                    </h4>
                                    <div class="flex gap-2">
                                        <IconField>
                                            <InputIcon>
                                                <i class="pi pi-search" />
                                            </InputIcon>
                                            <InputText v-model="searchTerm" placeholder="Buscar fornecedor..." @keyup.enter="onSearch" />
                                        </IconField>
                                        <Button icon="pi pi-search" severity="secondary" @click="onSearch" :loading="supplierStore.loading" v-tooltip.top="'Buscar'" />
                                    </div>
                                </div>
                            </template>

                            <Column selectionMode="multiple" style="width: 3rem" :exportable="false"></Column>

                            <Column field="name" header="Nome" sortable style="min-width: 16rem">
                                <template #body="slotProps">
                                    {{ slotProps.data.name }}
                                </template>
                            </Column>

                            <Column field="email" header="Email" sortable style="min-width: 20rem">
                                <template #body="slotProps">
                                    {{ slotProps.data.email || '-' }}
                                </template>
                            </Column>

                            <Column field="phone" header="Telefone" sortable style="min-width: 14rem">
                                <template #body="slotProps">
                                    {{ slotProps.data.phone || '-' }}
                                </template>
                            </Column>

                            <Column field="address" header="Endereço" sortable style="min-width: 20rem">
                                <template #body="slotProps">
                                    {{ slotProps.data.address || '-' }}
                                </template>
                            </Column>

                            <Column field="created_at" header="Criado em" sortable style="min-width: 12rem">
                                <template #body="slotProps">
                                    {{ slotProps.data.created }}
                                </template>
                            </Column>

                            <Column :exportable="false" style="min-width: 12rem">
                                <template #body="slotProps">
                                    <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="openUpdateSupplier(slotProps.data)" v-tooltip.top="'Editar Fornecedor'" />
                                    <Button icon="pi pi-trash" outlined rounded severity="danger" @click="openDestroySupplier(slotProps.data)" v-tooltip.top="'Excluir Fornecedor'" />
                                </template>
                            </Column>

                            <template #empty>
                                <div class="text-center py-8 text-gray-500">
                                    <i class="pi pi-fw pi-truck text-4xl mb-2"></i>
                                    <p>Nenhum fornecedor encontrado</p>
                                </div>
                            </template>
                        </DataTable>
                    </div>

                    <div class="block md:hidden">
                        <div v-if="supplierStore.loading" class="text-center py-8">
                            <ProgressSpinner />
                        </div>
                        <div v-else-if="supplierStore.suppliers.length === 0" class="text-center py-8 text-gray-500">
                            <i class="pi pi-fw pi-truck text-4xl mb-2"></i>
                            <p>Nenhum fornecedor encontrado</p>
                        </div>
                        <div v-else class="space-y-4">
                            <Card v-for="supplier in supplierStore.suppliers" :key="supplier.id" class="h-full flex flex-col rounded-none sm:rounded-xl border border-gray-200 dark:border-gray-700">
                                <template #content>
                                    <div class="p-4 space-y-4 flex-grow">
                                        <div class="flex justify-between items-start mb-3">
                                            <div class="flex-1">
                                                <h3 class="font-semibold text-lg text-gray-900 dark:text-gray-100">{{ supplier.name }}</h3>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 gap-4 mb-4">
                                            <div v-if="supplier.email">
                                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Email</p>
                                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ supplier.email }}</p>
                                            </div>
                                            <div v-if="supplier.phone">
                                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Telefone</p>
                                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ supplier.phone }}</p>
                                            </div>
                                            <div v-if="supplier.address">
                                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Endereço</p>
                                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ supplier.address }}</p>
                                            </div>
                                        </div>

                                        <div class="mb-4 pb-4 border-b border-gray-100 dark:border-gray-700">
                                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Criado em</p>
                                            <p class="text-sm text-gray-700 dark:text-gray-300">{{ supplier.created }}</p>
                                        </div>

                                        <div class="pt-3 border-t border-gray-100 dark:border-gray-700">
                                            <div class="grid grid-cols-1 gap-2 font-sans">
                                                <Button label="Editar" size="small" outlined class="w-full font-sans text-xs" @click="openUpdateSupplier(supplier)" />
                                                <Button label="Excluir" size="small" outlined severity="danger" class="w-full font-sans text-xs" @click="openDestroySupplier(supplier)" />
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </Card>
                        </div>

                        <div v-if="supplierStore.suppliers.length > 0" class="mt-6 flex justify-center">
                            <Paginator
                                :first="(currentPage - 1) * rowsPerPage"
                                :rows="rowsPerPage"
                                :totalRecords="supplierStore.pagination.total"
                                @page="onPaginatorPageChange"
                                template="FirstPageLink PrevPageLink CurrentPageReport NextPageLink LastPageLink"
                                currentPageReportTemplate="Página {currentPage} de {totalPages}"
                            />
                        </div>
                    </div>
                </div>

                <Dialog v-model:visible="supplierDialog" modal header="Detalhes do Fornecedor" :style="{ width: '50vw' }" :breakpoints="{ '960px': '75vw', '640px': '100vw' }" :maximizable="true">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="name" class="flex items-center justify-between font-bold mb-3">
                                <span>Nome <span class="text-red-500">*</span></span>
                                <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:block" v-tooltip.top="'Nome identificador do fornecedor'" />
                            </label>
                            <InputText id="name" v-model.trim="supplierStore.supplier.name" required autofocus :invalid="submitted && !supplierStore.supplier.name" placeholder="Digite o nome do fornecedor" fluid />
                            <small v-if="submitted && !supplierStore.supplier.name" class="text-red-500">Nome é obrigatório.</small>
                        </div>

                        <div>
                            <label for="email" class="flex items-center justify-between font-bold mb-3">
                                <span>Email</span>
                                <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:block" v-tooltip.top="'Email de contato do fornecedor'" />
                            </label>
                            <InputText id="email" v-model="supplierStore.supplier.email" type="email" placeholder="Digite o email do fornecedor" fluid :invalid="emailInvalid" />
                            <small v-if="emailInvalid" class="text-red-500">Email inválido.</small>
                        </div>

                        <div>
                            <label for="phone" class="flex items-center justify-between font-bold mb-3">
                                <span>Telefone</span>
                                <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:block" v-tooltip.top="'Telefone de contato do fornecedor'" />
                            </label>
                            <InputMask id="phone" v-model="supplierStore.supplier.phone" mask="99 99999-9999" placeholder="00 00000-0000" class="w-full" required type="tel" />
                        </div>

                        <div>
                            <label for="address" class="flex items-center justify-between font-bold mb-3">
                                <span>Endereço</span>
                                <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:block" v-tooltip.top="'Endereço do fornecedor'" />
                            </label>
                            <InputText id="address" v-model="supplierStore.supplier.address" placeholder="Digite o endereço do fornecedor" fluid />
                        </div>
                    </div>

                    <template #footer>
                        <Button label="Cancelar" icon="pi pi-times" text @click="hideDialog" />
                        <Button label="Salvar" icon="pi pi-check" @click="saveSupplier" />
                    </template>
                </Dialog>

                <Dialog v-model:visible="deleteSupplierDialog" :style="{ width: '450px' }" header="Confirmar" :modal="true">
                    <div class="flex items-center gap-4">
                        <i class="pi pi-exclamation-triangle !text-3xl" />
                        <span v-if="supplierStore.supplier">
                            Tem certeza que deseja deletar o fornecedor <b>{{ supplierStore.supplier.name }}</b
                            >?
                        </span>
                    </div>
                    <template #footer>
                        <Button label="Não" icon="pi pi-times" text @click="deleteSupplierDialog = false" />
                        <Button label="Sim" icon="pi pi-check" @click="deleteSupplier" />
                    </template>
                </Dialog>

                <Dialog v-model:visible="deleteSuppliersDialog" :style="{ width: '450px' }" header="Confirmar" :modal="true">
                    <div class="flex items-center gap-4">
                        <i class="pi pi-exclamation-triangle !text-3xl" />
                        <span>Tem certeza que deseja deletar os fornecedores selecionados?</span>
                    </div>
                    <template #footer>
                        <Button label="Não" icon="pi pi-times" text @click="deleteSuppliersDialog = false" />
                        <Button label="Sim" icon="pi pi-check" text @click="deleteSelectedSuppliers" />
                    </template>
                </Dialog>
            </div>
        </div>
    </div>
</template>
