<script setup lang="ts">
import { onMounted, ref, watch, computed } from 'vue';
import { useProductStore } from '@/stores/company/productStore';
import { useProductTypeStore } from '@/stores/company/select/productTypeStore';
import type { Product } from '@/types/company/product';
import type { ProductPayload } from '@/types/company/product';
import type { ProductFilters } from '@/types/company/filters/productFilter';
import type { DataTableSortEvent, DataTablePageEvent } from 'primevue/datatable';
import { useToast } from 'primevue/usetoast';
import { useFormat } from '@/utils/useFormat';
import { useKeyboardShortcuts } from '@/utils/useKeyboardShortcuts';

const productStore = useProductStore();
const productTypeStore = useProductTypeStore();
const { formatCurrency } = useFormat();

const searchTerm = ref('');
const currentPage = ref(1);
const rowsPerPage = ref(10);
const sortField = ref('id');
const sortOrder = ref<'asc' | 'desc'>('asc');
const selectedProductType = ref<number | null>(null);

const productDialog = ref(false);
const deleteProductDialog = ref(false);
const deleteProductsDialog = ref(false);
const submitted = ref(false);
const dt = ref<unknown>(null);

const statusOptions = ref<{ label: string; value: boolean }[]>([
    { label: 'Ativo', value: true },
    { label: 'Inativo', value: false }
]);

const productTypeOptions = computed(() => {
    return [{ label: 'Todos', value: null }, ...(productTypeStore.productTypeOptions || [])];
});

const toast = useToast();

useKeyboardShortcuts(openCreateProduct, saveProduct, productDialog, exportCSV);

watch(
    () => productStore.pagination?.currentPage,
    (newPage) => {
        if (newPage && newPage !== currentPage.value) {
            currentPage.value = newPage;
        }
    }
);

watch(
    () => selectedProductType.value,
    async () => {
        currentPage.value = 1;
        await loadProducts();
    }
);

onMounted(async () => {
    try {
        await Promise.all([loadProducts(), productTypeStore.fetchProductTypes()]);
    } catch {
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: 'Erro ao carregar dados da página',
            life: 5000
        });
    }
});

async function loadProducts(): Promise<void> {
    const filters: ProductFilters = {
        search: searchTerm.value || undefined,
        productTypeId: selectedProductType.value || undefined,
        sort: sortField.value,
        direction: sortOrder.value,
        perPage: rowsPerPage.value,
        page: currentPage.value
    };

    await productStore.fetchProducts(filters);
}

async function onSearch(): Promise<void> {
    currentPage.value = 1;
    await loadProducts();
}

async function onPageChange(event: DataTablePageEvent): Promise<void> {
    currentPage.value = event.page + 1;
    rowsPerPage.value = event.rows;
    await loadProducts();
}

async function onPaginatorPageChange(event: { page: number; first: number; rows: number }): Promise<void> {
    currentPage.value = event.page + 1;
    rowsPerPage.value = event.rows;
    await loadProducts();
}

async function onSort(event: DataTableSortEvent): Promise<void> {
    if (typeof event.sortField === 'string') {
        sortField.value = event.sortField || 'name';
        sortOrder.value = event.sortOrder === 1 ? 'asc' : 'desc';
        await loadProducts();
    }
}

async function saveProduct(): Promise<void> {
    submitted.value = true;

    if ((productStore.product.name?.trim(), productStore.product.price && productStore.product.price > 0 && productStore.product.productTypeId && productStore.product.productTypeId > 0)) {
        try {
            const payload: ProductPayload = {
                name: productStore.product.name,
                description: productStore.product.description || '',
                price: productStore.product.price || 0,
                product_type_id: productStore.product.productTypeId,
                is_active: productStore.product.isActive
            };

            if (productStore.product.id) {
                await productStore.updateProduct(productStore.product.id, payload);
                toast.add({ severity: 'success', summary: 'Sucesso', detail: 'Produto atualizado com sucesso', life: 3000 });
            } else {
                await productStore.createProduct(payload);
                toast.add({ severity: 'success', summary: 'Sucesso', detail: 'Produto criado com sucesso', life: 3000 });
            }

            productDialog.value = false;
            productStore.clearProduct();
            await loadProducts();
        } catch {
            toast.add({ severity: 'error', summary: 'Erro', detail: productStore.error, life: 3000 });
        }
    }
}

async function deleteProduct(): Promise<void> {
    if (productStore.product.id) {
        try {
            await productStore.deleteProduct(productStore.product.id);
            deleteProductDialog.value = false;
            productStore.clearProduct();
            toast.add({ severity: 'success', summary: 'Sucesso', detail: 'Produto deletado com sucesso', life: 3000 });
            await loadProducts();
        } catch {
            toast.add({ severity: 'error', summary: 'Erro', detail: productStore.error, life: 3000 });
        }
    }
}

async function deleteSelectedProducts(): Promise<void> {
    try {
        await productStore.deleteSelectedProducts();
        deleteProductsDialog.value = false;
        toast.add({ severity: 'success', summary: 'Sucesso', detail: 'Produtos deletados com sucesso', life: 3000 });
        await loadProducts();
    } catch {
        toast.add({ severity: 'error', summary: 'Erro', detail: productStore.error, life: 3000 });
    }
}

function openCreateProduct(): void {
    productStore.clearProduct();
    productStore.product.price = null;
    submitted.value = false;
    productDialog.value = true;
}

function openUpdateProduct(productData: Product): void {
    productStore.product = { ...productData };
    productDialog.value = true;
}

function openDestroyProduct(productData: Product): void {
    productStore.product = productData;
    deleteProductDialog.value = true;
}

function openDestroySelected(): void {
    deleteProductsDialog.value = true;
}

function hideDialog(): void {
    productDialog.value = false;
    submitted.value = false;
}

function exportCSV(): void {
    const data = productStore.products.map((product) => ({
        Nome: product.name,
        Descrição: product.description || '-',
        Preço: formatCurrency(product.price),
        Tipo: product.productType?.name || '-',
        Status: getStatusText(product.isActive)
    }));

    const headers = Object.keys(data[0]);
    const csvContent = [headers.join(','), ...data.map((row) => headers.map((header) => `"${row[header as keyof typeof row]}"`).join(','))].join('\n');

    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', 'produtos.csv');
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function getStatusLabel(status: boolean): string {
    return status ? 'success' : 'danger';
}

function getStatusText(status: boolean): string {
    return status ? 'Ativo' : 'Inativo';
}

function resetFilters(): void {
    selectedProductType.value = null;
    searchTerm.value = '';
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
                                <Button label="Adicionar Produto (F1)" icon="pi pi-plus" severity="secondary" class="mr-2" @click="openCreateProduct" v-tooltip.top="'Cadastrar novo produto (F1)'" />
                                <Select v-model="selectedProductType" :options="productTypeOptions" optionLabel="label" optionValue="value" placeholder="Tipo" class="mr-2" style="width: 140px" v-tooltip.top="'Filtrar por tipo de produto'" />
                            </div>
                            <Button label="Resetar Filtros" icon="pi pi-refresh" severity="secondary" @click="resetFilters" class="mr-2" v-tooltip.top="'Limpar todos os filtros aplicados'" />
                            <Button
                                label="Deletar"
                                icon="pi pi-trash"
                                severity="secondary"
                                @click="openDestroySelected"
                                :disabled="!productStore.selectedProducts || !productStore.selectedProducts.length"
                                v-tooltip.top="'Excluir produtos selecionados'"
                            />
                        </template>

                        <template #end>
                            <Button label="Exportar (F4)" icon="pi pi-upload" severity="secondary" @click="exportCSV" v-tooltip.top="'Exportar Dados da Tabela'" />
                        </template>
                    </Toolbar>

                    <div class="block md:hidden mb-6">
                        <div class="grid grid-cols-1 gap-4">
                            <Button label="Adicionar Produto" icon="pi pi-plus" severity="secondary" @click="openCreateProduct" class="w-full" />
                            <Select v-model="selectedProductType" :options="productTypeOptions" optionLabel="label" optionValue="value" placeholder="Tipo" class="w-full" v-tooltip.top="'Filtrar por tipo de produto'" />
                            <div class="mt-4 flex gap-2">
                                <IconField class="flex-1">
                                    <InputIcon>
                                        <i class="pi pi-search" />
                                    </InputIcon>
                                    <InputText v-model="searchTerm" placeholder="Buscar produto..." @keyup.enter="onSearch" class="w-full" />
                                </IconField>
                                <Button icon="pi pi-search" severity="secondary" @click="onSearch" :loading="productStore.loading" />
                            </div>
                        </div>
                    </div>

                    <div class="hidden md:block">
                        <DataTable
                            ref="dt"
                            v-model:selection="productStore.selectedProducts"
                            :value="productStore.products"
                            dataKey="id"
                            :paginator="true"
                            :rows="rowsPerPage"
                            :totalRecords="productStore.pagination.total"
                            :loading="productStore.loading"
                            :first="(currentPage - 1) * rowsPerPage"
                            paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                            :rowsPerPageOptions="[5, 10, 25, 50]"
                            currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} produtos"
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
                                            <i class="pi pi-fw pi-box"></i>
                                        </span>
                                        Produtos
                                    </h4>
                                    <div class="flex gap-2">
                                        <IconField>
                                            <InputIcon>
                                                <i class="pi pi-search" />
                                            </InputIcon>
                                            <InputText v-model="searchTerm" placeholder="Buscar produto..." @keyup.enter="onSearch" />
                                        </IconField>
                                        <Button icon="pi pi-search" severity="secondary" @click="onSearch" :loading="productStore.loading" v-tooltip.top="'Buscar'" />
                                    </div>
                                </div>
                            </template>

                            <Column selectionMode="multiple" style="width: 3rem" :exportable="false"></Column>

                            <Column field="name" header="Nome" sortable style="min-width: 16rem">
                                <template #body="slotProps">
                                    {{ slotProps.data.name }}
                                </template>
                            </Column>

                            <Column field="description" header="Descrição" sortable style="min-width: 20rem">
                                <template #body="slotProps">
                                    {{ slotProps.data.description || '-' }}
                                </template>
                            </Column>

                            <Column field="price" header="Preço" sortable style="min-width: 12rem">
                                <template #body="slotProps">
                                    {{ formatCurrency(slotProps.data.price) }}
                                </template>
                            </Column>

                            <Column field="product_type_id" header="Tipo" sortable style="min-width: 10rem">
                                <template #body="slotProps">
                                    {{ slotProps.data.productType?.name || '-' }}
                                </template>
                            </Column>

                            <Column field="is_active" header="Status" sortable style="min-width: 8rem">
                                <template #body="slotProps">
                                    <Tag :severity="getStatusLabel(slotProps.data.isActive)" :value="getStatusText(slotProps.data.isActive)" />
                                </template>
                            </Column>

                            <Column field="created_at" header="Criado Em" sortable style="min-width: 16rem">
                                <template #body="slotProps">
                                    {{ slotProps.data.created }}
                                </template>
                            </Column>

                            <Column :exportable="false" style="min-width: 12rem">
                                <template #body="slotProps">
                                    <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="openUpdateProduct(slotProps.data)" v-tooltip.top="'Editar Produto'" />
                                    <Button icon="pi pi-trash" outlined rounded severity="danger" @click="openDestroyProduct(slotProps.data)" v-tooltip.top="'Excluir Produto'" />
                                </template>
                            </Column>

                            <template #empty>
                                <div class="text-center py-8 text-gray-500">
                                    <i class="pi pi-fw pi-box text-4xl mb-2"></i>
                                    <p>Nenhum produto encontrado</p>
                                </div>
                            </template>
                        </DataTable>
                    </div>

                    <div class="block md:hidden">
                        <div v-if="productStore.loading" class="text-center py-8">
                            <ProgressSpinner />
                        </div>
                        <div v-else-if="productStore.products.length === 0" class="text-center py-8 text-gray-500">
                            <i class="pi pi-fw pi-box text-4xl mb-2"></i>
                            <p>Nenhum produto encontrado</p>
                        </div>
                        <div v-else class="space-y-4">
                            <Card v-for="product in productStore.products" :key="product.id" class="h-full flex flex-col rounded-none sm:rounded-xl border border-gray-200 dark:border-gray-700">
                                <template #content>
                                    <div class="p-4 space-y-4 flex-grow">
                                        <div class="flex justify-between items-start mb-3">
                                            <div class="flex-1">
                                                <h3 class="font-semibold text-lg text-gray-900 dark:text-gray-100">{{ product.name }}</h3>
                                                <p class="font-semibold text-gray-900 dark:text-gray-400">{{ product.productType?.name || '-' }}</p>
                                            </div>
                                            <Tag :value="getStatusText(product.isActive)" :severity="getStatusLabel(product.isActive)" />
                                        </div>

                                        <div class="grid grid-cols-1 gap-4 mb-4">
                                            <div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Preço</p>
                                                <p class="font-medium text-gray-900 dark:text-gray-100 text-lg">{{ formatCurrency(product.price) }}</p>
                                            </div>
                                        </div>

                                        <div v-if="product.description" class="mb-4 pb-4 border-b border-gray-100 dark:border-gray-700">
                                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Descrição</p>
                                            <p class="text-sm text-gray-700 dark:text-gray-300">{{ product.description }}</p>
                                        </div>

                                        <div class="pt-3">
                                            <div class="grid grid-cols-1 gap-2 font-sans">
                                                <Button label="Editar Produto" size="small" outlined class="w-full font-sans text-xs" @click="openUpdateProduct(product)" />
                                                <Button label="Excluir Produto" size="small" outlined severity="danger" class="w-full font-sans text-xs" @click="openDestroyProduct(product)" />
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </Card>
                        </div>

                        <div v-if="productStore.products.length > 0" class="mt-6 flex justify-center">
                            <Paginator
                                :first="(currentPage - 1) * rowsPerPage"
                                :rows="rowsPerPage"
                                :totalRecords="productStore.pagination.total"
                                @page="onPaginatorPageChange"
                                template="FirstPageLink PrevPageLink CurrentPageReport NextPageLink LastPageLink"
                                currentPageReportTemplate="Página {currentPage} de {totalPages}"
                            />
                        </div>
                    </div>
                </div>

                <Dialog v-model:visible="productDialog" modal header="Detalhes do Produto" :style="{ width: '50vw' }" :breakpoints="{ '960px': '75vw', '640px': '100vw' }" :maximizable="true">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="name" class="flex items-center justify-between font-bold mb-3">
                                <span>Nome <span class="text-red-500">*</span></span>
                                <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:block" v-tooltip.top="'Nome identificador do produto'" />
                            </label>
                            <InputText id="name" v-model.trim="productStore.product.name" required autofocus :invalid="submitted && !productStore.product.name" placeholder="Digite o nome do produto" fluid />
                            <small v-if="submitted && !productStore.product.name" class="text-red-500">Nome é obrigatório.</small>
                        </div>

                        <div>
                            <label for="price" class="flex items-center justify-between font-bold mb-3">
                                <span>Preço <span class="text-red-500">*</span></span>
                                <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:block" v-tooltip.top="'Valor de venda do produto'" />
                            </label>
                            <InputNumber
                                id="price"
                                v-model="productStore.product.price"
                                mode="currency"
                                currency="BRL"
                                locale="pt-BR"
                                :minFractionDigits="2"
                                placeholder="0,00"
                                :invalid="submitted && (!productStore.product.price || productStore.product.price <= 0)"
                                showButtons
                                buttonLayout="horizontal"
                                :step="0.25"
                                fluid
                            >
                                <template #incrementicon>
                                    <span class="pi pi-plus" />
                                </template>
                                <template #decrementicon>
                                    <span class="pi pi-minus" />
                                </template>
                            </InputNumber>
                            <small v-if="submitted && (!productStore.product.price || productStore.product.price <= 0)" class="text-red-500">Preço é obrigatório e deve ser maior que zero.</small>
                            <small class="text-gray-500 block mt-1 md:hidden">Se tiver dificuldade em digitar o valor, apague tudo e digite novamente.</small>
                        </div>

                        <div>
                            <label for="productTypeId" class="flex items-center justify-between font-bold mb-3">
                                <span>Tipo do Produto <span class="text-red-500">*</span></span>
                                <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:block" v-tooltip.top="'Categoria ou classificação do produto'" />
                            </label>
                            <Select
                                id="productTypeId"
                                v-model="productStore.product.productTypeId"
                                :options="productTypeStore.productTypeOptions"
                                optionLabel="label"
                                optionValue="value"
                                placeholder="Selecione o tipo"
                                :invalid="submitted && (!productStore.product.productTypeId || productStore.product.productTypeId <= 0)"
                                fluid
                            />
                            <small v-if="submitted && (!productStore.product.productTypeId || productStore.product.productTypeId <= 0)" class="text-red-500">Tipo do produto é obrigatório.</small>
                        </div>

                        <div>
                            <label for="isActive" class="flex items-center justify-between font-bold mb-3">
                                <span>Status</span>
                                <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:block" v-tooltip.top="'Define se o produto está disponível para venda ou não'" />
                            </label>
                            <Select id="isActive" v-model="productStore.product.isActive" :options="statusOptions" optionLabel="label" optionValue="value" placeholder="Selecione o status" fluid />
                        </div>

                        <div class="col-span-1 md:col-span-3">
                            <label for="description" class="flex items-center justify-between font-bold mb-3">
                                <span>Descrição</span>
                                <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:block" v-tooltip.top="'Descrição detalhada do produto (opcional)'" />
                            </label>
                            <Textarea id="description" v-model="productStore.product.description" rows="3" placeholder="Digite a descrição do produto" fluid />
                        </div>
                    </div>

                    <template #footer>
                        <Button label="Cancelar" icon="pi pi-times" text @click="hideDialog" />
                        <Button label="Salvar" icon="pi pi-check" @click="saveProduct" />
                    </template>
                </Dialog>

                <Dialog v-model:visible="deleteProductDialog" :style="{ width: '450px' }" header="Confirmar" :modal="true">
                    <div class="flex items-center gap-4">
                        <i class="pi pi-exclamation-triangle !text-3xl" />
                        <span v-if="productStore.product">
                            Tem certeza que deseja deletar o produto <b>{{ productStore.product.name }}</b
                            >?
                        </span>
                    </div>
                    <template #footer>
                        <Button label="Não" icon="pi pi-times" text @click="deleteProductDialog = false" />
                        <Button label="Sim" icon="pi pi-check" @click="deleteProduct" />
                    </template>
                </Dialog>

                <Dialog v-model:visible="deleteProductsDialog" :style="{ width: '450px' }" header="Confirmar" :modal="true">
                    <div class="flex items-center gap-4">
                        <i class="pi pi-exclamation-triangle !text-3xl" />
                        <span>Tem certeza que deseja deletar os produtos selecionados?</span>
                    </div>
                    <template #footer>
                        <Button label="Não" icon="pi pi-times" text @click="deleteProductsDialog = false" />
                        <Button label="Sim" icon="pi pi-check" text @click="deleteSelectedProducts" />
                    </template>
                </Dialog>
            </div>
        </div>
    </div>
</template>
