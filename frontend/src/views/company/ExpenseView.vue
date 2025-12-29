<script setup lang="ts">
import { onMounted, ref, watch, computed, reactive } from 'vue';
import { useExpenseStore } from '@/stores/company/expenseStore';
import { useExpenseTypeStore } from '@/stores/company/select/expenseTypeStore';
import type { Expense } from '@/types/company/expense';
import type { ExpensePayload } from '@/types/company/expense';
import type { ExpenseFilters } from '@/types/company/filters/expenseFilter';
import type { DataTableSortEvent, DataTablePageEvent } from 'primevue/datatable';
import { useToast } from 'primevue/usetoast';
import { useFormat } from '@/utils/useFormat';
import { formatLocalDate } from '@/utils/dateUtils';
import { useKeyboardShortcuts } from '@/utils/useKeyboardShortcuts';

const expenseStore = useExpenseStore();
const expenseTypeStore = useExpenseTypeStore();
const { formatCurrency } = useFormat();

const searchTerm = ref('');
const currentPage = ref(1);
const rowsPerPage = ref(10);
const sortField = ref('id');
const sortOrder = ref<'asc' | 'desc'>('asc');

const expenseDialog = ref(false);
const deleteExpenseDialog = ref(false);
const deleteExpensesDialog = ref(false);
const submitted = ref(false);
const dt = ref<unknown>(null);
const showFiltersModal = ref(false);

const typeOptions = ref<{ label: string; value: 'entrada' | 'saida' }[]>([
    { label: 'Entrada', value: 'entrada' },
    { label: 'Saída', value: 'saida' }
]);

const paidOptions = ref<{ label: string; value: boolean }[]>([
    { label: 'Pago', value: true },
    { label: 'Pendente', value: false }
]);

const expenseTypeOptions = computed(() => {
    return [{ label: 'Todos', value: null }, ...(expenseTypeStore.expenseTypeOptions || [])];
});

const typeFilterOptions = computed(() => {
    return [{ label: 'Todos', value: null }, ...typeOptions.value.map((opt) => ({ label: opt.label, value: opt.value }))];
});

const statusFilterOptions = computed(() => {
    return [{ label: 'Todos', value: null }, ...paidOptions.value.map((opt) => ({ label: opt.label, value: opt.value }))];
});

const filtersObj = reactive({
    expenseType: null as number | null,
    type: null as 'entrada' | 'saida' | null,
    status: null as boolean | null,
    startDueDate: null as Date | null,
    endDueDate: null as Date | null
});

const toast = useToast();

useKeyboardShortcuts(openCreateExpense, saveExpense, expenseDialog, exportCSV);

watch(
    () => expenseStore.pagination?.currentPage,
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
        await loadExpenses();
    },
    { deep: true }
);

onMounted(async () => {
    try {
        await Promise.all([loadExpenses(), expenseTypeStore.fetchExpenseTypes()]);
    } catch {
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: 'Erro ao carregar dados da página',
            life: 5000
        });
    }
});

async function loadExpenses(): Promise<void> {
    const filters: ExpenseFilters = {
        search: searchTerm.value || undefined,
        expenseTypeId: filtersObj.expenseType || undefined,
        type: filtersObj.type || undefined,
        isPaid: filtersObj.status !== null ? filtersObj.status : undefined,
        startDueDate: filtersObj.startDueDate ? formatLocalDate(filtersObj.startDueDate) : undefined,
        endDueDate: filtersObj.endDueDate ? formatLocalDate(filtersObj.endDueDate) : undefined,
        sort: sortField.value,
        direction: sortOrder.value,
        perPage: rowsPerPage.value,
        page: currentPage.value
    };

    await expenseStore.fetchExpenses(filters);
}

async function onSearch(): Promise<void> {
    currentPage.value = 1;
    await loadExpenses();
}

async function onPageChange(event: DataTablePageEvent): Promise<void> {
    currentPage.value = event.page + 1;
    rowsPerPage.value = event.rows;
    await loadExpenses();
}

async function onPaginatorPageChange(event: { page: number; first: number; rows: number }): Promise<void> {
    currentPage.value = event.page + 1;
    rowsPerPage.value = event.rows;
    await loadExpenses();
}

async function onSort(event: DataTableSortEvent): Promise<void> {
    if (typeof event.sortField === 'string') {
        sortField.value = event.sortField || 'created';
        sortOrder.value = event.sortOrder === 1 ? 'asc' : 'desc';
        await loadExpenses();
    }
}

async function saveExpense(): Promise<void> {
    submitted.value = true;

    if (expenseStore.expense.name?.trim()) {
        try {
            let dueDateString = '';
            if (expenseStore.expense.dueDate) {
                if (expenseStore.expense.dueDate instanceof Date) {
                    dueDateString = expenseStore.expense.dueDate.toISOString().split('T')[0];
                } else {
                    dueDateString = new Date(expenseStore.expense.dueDate).toISOString().split('T')[0];
                }
            }

            const payload: ExpensePayload = {
                name: expenseStore.expense.name,
                type: expenseStore.expense.type,
                amount: expenseStore.expense.amount || 0,
                description: expenseStore.expense.description || '',
                expense_type_id: expenseStore.expense.expenseTypeId,
                due_date: dueDateString,
                is_paid: expenseStore.expense.isPaid
            };

            if (expenseStore.expense.id) {
                await expenseStore.updateExpense(expenseStore.expense.id, payload);
                toast.add({ severity: 'success', summary: 'Sucesso', detail: 'Despesa/Receita atualizada com sucesso', life: 3000 });
            } else {
                await expenseStore.createExpense(payload);
                toast.add({ severity: 'success', summary: 'Sucesso', detail: 'Despesa/Receita criada com sucesso', life: 3000 });
            }

            expenseDialog.value = false;
            expenseStore.clearExpense();
            await loadExpenses();
        } catch {
            toast.add({ severity: 'error', summary: 'Erro', detail: expenseStore.error, life: 3000 });
        }
    }
}

async function deleteExpense(): Promise<void> {
    if (expenseStore.expense.id) {
        try {
            await expenseStore.deleteExpense(expenseStore.expense.id);
            deleteExpenseDialog.value = false;
            expenseStore.clearExpense();
            toast.add({ severity: 'success', summary: 'Sucesso', detail: 'Despesa/Receita deletada com sucesso', life: 3000 });
            await loadExpenses();
        } catch {
            toast.add({ severity: 'error', summary: 'Erro', detail: expenseStore.error, life: 3000 });
        }
    }
}

async function deleteSelectedExpenses(): Promise<void> {
    try {
        await expenseStore.deleteSelectedExpenses();
        deleteExpensesDialog.value = false;
        toast.add({ severity: 'success', summary: 'Sucesso', detail: 'Despesas/Receitas deletadas com sucesso', life: 3000 });
        await loadExpenses();
    } catch {
        toast.add({ severity: 'error', summary: 'Erro', detail: expenseStore.error, life: 3000 });
    }
}

function openCreateExpense(): void {
    expenseStore.clearExpense();
    expenseStore.expense.amount = null;
    submitted.value = false;
    expenseDialog.value = true;
}

function openUpdateExpense(expenseData: Expense): void {
    const editedExpense = { ...expenseData };
    if (editedExpense.dueDate && typeof editedExpense.dueDate === 'string') {
        const dateStr = editedExpense.dueDate as string;
        const [day, month, year] = dateStr.split('/').map(Number);
        editedExpense.dueDate = new Date(year, month - 1, day);
    }
    expenseStore.expense = editedExpense;
    expenseDialog.value = true;
}

function openDestroyExpense(expenseData: Expense): void {
    expenseStore.expense = expenseData;
    deleteExpenseDialog.value = true;
}

function openDestroySelectedExpense(): void {
    deleteExpensesDialog.value = true;
}

function hideDialog(): void {
    expenseDialog.value = false;
    submitted.value = false;
}

function exportCSV(): void {
    const data = expenseStore.expenses.map((expense) => ({
        Nome: expense.name,
        Tipo: getTypeText(expense.type),
        Valor: formatCurrency(expense.amount),
        Categoria: expense.expenseType?.name || '',
        Vencimento: expense.dueDate,
        Status: getStatusText(expense.isPaid),
        'Criado em': expense.created
    }));

    const headers = Object.keys(data[0]);
    const csvContent = [headers.join(','), ...data.map((row) => headers.map((header) => `"${row[header as keyof typeof row]}"`).join(','))].join('\n');

    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', 'despesas.csv');
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function getStatusLabel(status: boolean): string {
    return status ? 'success' : 'warn';
}

function getStatusText(status: boolean): string {
    return status ? 'Pago' : 'Pendente';
}

function getTypeLabel(type: 'entrada' | 'saida'): string {
    return type === 'entrada' ? 'success' : 'danger';
}

function getTypeText(type: 'entrada' | 'saida'): string {
    return type === 'entrada' ? 'Entrada' : 'Saída';
}

function setToday(): void {
    filtersObj.startDueDate = new Date();
    filtersObj.endDueDate = new Date();
}

function resetFilters(): void {
    filtersObj.expenseType = null;
    filtersObj.type = null;
    filtersObj.status = null;
    filtersObj.startDueDate = null;
    filtersObj.endDueDate = null;
    searchTerm.value = '';
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
                            <div class="flex-wrap gap-2 items-center hidden md:flex">
                                <Button label="Adicionar Despesa/Receita (F1)" icon="pi pi-plus" severity="secondary" class="mr-2" @click="openCreateExpense" v-tooltip.top="'Cadastrar nova despesa/receita'" />
                                <DatePicker v-model="filtersObj.startDueDate" dateFormat="dd/mm/yy" placeholder="Venc. Início" class="mr-2" fluid style="width: 140px" v-tooltip.top="'Filtrar por data de vencimento inicial'" />
                                <DatePicker v-model="filtersObj.endDueDate" dateFormat="dd/mm/yy" placeholder="Venc. Fim" class="mr-2" fluid style="width: 140px" v-tooltip.top="'Filtrar por data de vencimento final'" />
                                <Button label="Hoje" @click="setToday" severity="secondary" class="mr-2" v-tooltip.top="'Definir filtros para data de hoje'" />
                                <Select
                                    v-model="filtersObj.expenseType"
                                    :options="expenseTypeOptions"
                                    optionLabel="label"
                                    optionValue="value"
                                    placeholder="Categoria"
                                    class="mr-2"
                                    style="width: 140px"
                                    v-tooltip.top="'Filtrar por categoria da despesa'"
                                />
                                <Select v-model="filtersObj.type" :options="typeFilterOptions" optionLabel="label" optionValue="value" placeholder="Tipo" class="mr-2" style="width: 140px" v-tooltip.top="'Filtrar por tipo (Entrada/Saída)'" />
                                <Select v-model="filtersObj.status" :options="statusFilterOptions" optionLabel="label" optionValue="value" placeholder="Status" class="mr-2" style="width: 140px" v-tooltip.top="'Filtrar por status (Pago/Pendente)'" />
                            </div>
                            <Button label="Resetar Filtros" icon="pi pi-refresh" severity="secondary" @click="resetFilters" class="mr-2" v-tooltip.top="'Limpar todos os filtros aplicados'" />
                            <Button
                                label="Deletar"
                                icon="pi pi-trash"
                                severity="secondary"
                                @click="openDestroySelectedExpense"
                                :disabled="!expenseStore.selectedExpenses || !expenseStore.selectedExpenses.length"
                                v-tooltip.top="'Excluir despesas/receitas selecionadas'"
                            />
                        </template>

                        <template #end>
                            <Button label="Exportar (F4)" icon="pi pi-upload" severity="secondary" @click="exportCSV" v-tooltip.top="'Exportar Dados da Tabela'" />
                        </template>
                    </Toolbar>

                    <Dialog v-model:visible="showFiltersModal" modal header="Filtros" :style="{ width: '30vw' }" :breakpoints="{ '960px': '75vw', '640px': '100vw' }" :maximizable="true">
                        <div class="flex flex-col gap-4">
                            <div class="flex flex-col gap-2 w-full">
                                <label for="startDueDate">Venc. Início</label>
                                <DatePicker v-model="filtersObj.startDueDate" dateFormat="dd/mm/yy" placeholder="Venc. Início" class="w-full" fluid />
                            </div>
                            <div class="flex flex-col gap-2 w-full">
                                <label for="endDueDate">Venc. Fim</label>
                                <DatePicker v-model="filtersObj.endDueDate" dateFormat="dd/mm/yy" placeholder="Venc. Fim" class="w-full" fluid />
                            </div>
                            <Button label="Hoje" @click="setToday" severity="secondary" class="w-full" />
                            <div class="flex flex-col gap-2">
                                <label for="expenseType">Categoria</label>
                                <Select v-model="filtersObj.expenseType" :options="expenseTypeOptions" optionLabel="label" optionValue="value" placeholder="Categoria" class="w-full" />
                            </div>
                            <div class="flex flex-col gap-2">
                                <label for="type">Tipo</label>
                                <Select v-model="filtersObj.type" :options="typeFilterOptions" optionLabel="label" optionValue="value" placeholder="Tipo" class="w-full" />
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
                            <Button label="Adicionar Despesa/Receita" icon="pi pi-plus" severity="secondary" @click="openCreateExpense" class="w-full" />
                            <Button label="Filtros" icon="pi pi-filter" @click="showFiltersModal = true" class="w-full" />

                            <div class="mt-4 flex gap-2">
                                <IconField class="flex-1">
                                    <InputIcon>
                                        <i class="pi pi-search" />
                                    </InputIcon>
                                    <InputText v-model="searchTerm" placeholder="Buscar despesa/receita..." @keyup.enter="onSearch" class="w-full" />
                                </IconField>
                                <Button icon="pi pi-search" severity="secondary" @click="onSearch" :loading="expenseStore.loading" />
                            </div>
                        </div>
                    </div>

                    <div class="hidden md:block">
                        <DataTable
                            ref="dt"
                            v-model:selection="expenseStore.selectedExpenses"
                            :value="expenseStore.expenses"
                            dataKey="id"
                            :paginator="true"
                            :rows="rowsPerPage"
                            :totalRecords="expenseStore.pagination.total"
                            :loading="expenseStore.loading"
                            :first="(currentPage - 1) * rowsPerPage"
                            paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                            :rowsPerPageOptions="[5, 10, 25, 50]"
                            currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} despesas/receitas"
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
                                            <i class="pi pi-fw pi-money-bill"></i>
                                        </span>
                                        Despesas/Receitas
                                    </h4>
                                    <div class="flex gap-2">
                                        <IconField>
                                            <InputIcon>
                                                <i class="pi pi-search" />
                                            </InputIcon>
                                            <InputText v-model="searchTerm" placeholder="Buscar despesa/receita..." @keyup.enter="onSearch" />
                                        </IconField>
                                        <Button icon="pi pi-search" severity="secondary" @click="onSearch" :loading="expenseStore.loading" v-tooltip.top="'Buscar'" />
                                    </div>
                                </div>
                            </template>
                            <Column selectionMode="multiple" style="width: 3rem" :exportable="false"></Column>

                            <Column field="name" header="Nome" sortable style="min-width: 16rem">
                                <template #body="slotProps">
                                    {{ slotProps.data.name }}
                                </template>
                            </Column>

                            <Column field="type" header="Tipo" sortable style="min-width: 8rem">
                                <template #body="slotProps">
                                    <Tag :value="getTypeText(slotProps.data.type)" :severity="getTypeLabel(slotProps.data.type)" />
                                </template>
                            </Column>

                            <Column field="amount" header="Valor" sortable style="min-width: 12rem">
                                <template #body="slotProps">
                                    {{ formatCurrency(slotProps.data.amount) }}
                                </template>
                            </Column>

                            <Column field="expense_type_id" header="Categoria" sortable style="min-width: 10rem">
                                <template #body="slotProps">
                                    {{ slotProps.data.expenseType?.name }}
                                </template>
                            </Column>

                            <Column field="due_date" header="Vencimento" sortable style="min-width: 8rem">
                                <template #body="slotProps">
                                    {{ slotProps.data.dueDate }}
                                </template>
                            </Column>

                            <Column field="is_paid" header="Status" sortable style="min-width: 8rem">
                                <template #body="slotProps">
                                    <Tag :value="getStatusText(slotProps.data.isPaid)" :severity="getStatusLabel(slotProps.data.isPaid)" />
                                </template>
                            </Column>

                            <Column field="created_at" header="Criado em" sortable style="min-width: 8rem">
                                <template #body="slotProps">
                                    {{ slotProps.data.created }}
                                </template>
                            </Column>

                            <Column :exportable="false" style="min-width: 12rem">
                                <template #body="slotProps">
                                    <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="openUpdateExpense(slotProps.data)" v-tooltip.top="'Editar Despesa/Receita'" />
                                    <Button icon="pi pi-trash" outlined rounded severity="danger" @click="openDestroyExpense(slotProps.data)" v-tooltip.top="'Excluir Despesa/Receita'" />
                                </template>
                            </Column>

                            <template #empty>
                                <div class="text-center py-8 text-gray-500">
                                    <i class="pi pi-fw pi-money-bill text-4xl mb-2"></i>
                                    <p>Nenhuma despesa/receita encontrada</p>
                                </div>
                            </template>
                        </DataTable>
                    </div>

                    <div class="block md:hidden">
                        <div v-if="expenseStore.loading" class="text-center py-8">
                            <ProgressSpinner />
                        </div>

                        <div v-else-if="expenseStore.expenses.length === 0" class="text-center py-8 text-gray-500">
                            <i class="pi pi-fw pi-money-bill text-4xl mb-2"></i>
                            <p>Nenhuma despesa/receita encontrada</p>
                        </div>

                        <div v-else class="space-y-4">
                            <Card v-for="expense in expenseStore.expenses" :key="expense.id" class="h-full flex flex-col rounded-none sm:rounded-xl border border-gray-200 dark:border-gray-700">
                                <template #content>
                                    <div class="p-4 space-y-4 flex-grow">
                                        <div class="flex justify-between items-start mb-3">
                                            <div class="flex-1">
                                                <h3 class="font-semibold text-lg text-gray-900 dark:text-gray-100">{{ expense.name }}</h3>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ expense.expenseType?.name }}</p>
                                            </div>
                                            <Tag :value="getTypeText(expense.type)" :severity="getTypeLabel(expense.type)" />
                                        </div>

                                        <div class="grid grid-cols-2 gap-4 mb-4">
                                            <div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Valor</p>
                                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ formatCurrency(expense.amount) }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Vencimento</p>
                                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ expense.dueDate }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Criado em</p>
                                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ expense.created }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Status</p>
                                                <Tag :value="getStatusText(expense.isPaid)" :severity="getStatusLabel(expense.isPaid)" />
                                            </div>
                                        </div>

                                        <div v-if="expense.description" class="mb-4 pb-4 border-b border-gray-100 dark:border-gray-700">
                                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Descrição</p>
                                            <p class="text-sm text-gray-700 dark:text-gray-300">{{ expense.description }}</p>
                                        </div>

                                        <div class="pt-3">
                                            <div class="grid grid-cols-1 gap-2 font-sans">
                                                <Button label="Editar" size="small" outlined class="w-full font-sans text-xs" @click="openUpdateExpense(expense)" />
                                                <Button label="Excluir" size="small" outlined severity="danger" class="w-full font-sans text-xs" @click="openDestroyExpense(expense)" />
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </Card>
                        </div>

                        <div v-if="expenseStore.expenses.length > 0" class="mt-6 flex justify-center">
                            <Paginator
                                :first="(currentPage - 1) * rowsPerPage"
                                :rows="rowsPerPage"
                                :totalRecords="expenseStore.pagination.total"
                                @page="onPaginatorPageChange"
                                template="FirstPageLink PrevPageLink CurrentPageReport NextPageLink LastPageLink"
                                currentPageReportTemplate="Página {currentPage} de {totalPages}"
                            />
                        </div>
                    </div>
                </div>

                <Dialog v-model:visible="expenseDialog" modal header="Detalhes da Despesa/Receita" :style="{ width: '50vw' }" :breakpoints="{ '960px': '75vw', '640px': '100vw' }" :maximizable="true">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="col-span-1 md:col-span-3">
                            <label for="name" class="flex items-center justify-between font-bold mb-3">
                                <span>Nome <span class="text-red-500">*</span></span>
                                <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:inline" v-tooltip.top="'Nome identificador da despesa ou receita'" />
                            </label>
                            <InputText id="name" v-model.trim="expenseStore.expense.name" required autofocus :invalid="submitted && !expenseStore.expense.name" placeholder="Digite o nome da despesa" fluid />
                            <small v-if="submitted && !expenseStore.expense.name" class="text-red-500">Nome é obrigatório.</small>
                        </div>

                        <div>
                            <label for="type" class="flex items-center justify-between font-bold mb-3">
                                <span>Tipo <span class="text-red-500">*</span></span>
                                <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:inline" v-tooltip.top="'Define se é uma entrada (receita) ou saída (despesa)'" />
                            </label>
                            <Select id="type" v-model="expenseStore.expense.type" :options="typeOptions" optionLabel="label" optionValue="value" placeholder="Selecione o tipo" fluid />
                        </div>

                        <div>
                            <label for="amount" class="flex items-center justify-between font-bold mb-3">
                                <span>Valor <span class="text-red-500">*</span></span>
                                <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:inline" v-tooltip.top="'Valor monetário da despesa ou receita'" />
                            </label>
                            <InputNumber
                                id="amount"
                                v-model="expenseStore.expense.amount"
                                mode="currency"
                                currency="BRL"
                                locale="pt-BR"
                                :invalid="submitted && !expenseStore.expense.amount"
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
                            <small v-if="submitted && !expenseStore.expense.amount" class="text-red-500">Valor é obrigatório.</small>
                            <small class="text-gray-500 block mt-1 md:hidden">Se tiver dificuldade em digitar o valor, apague tudo e digite novamente.</small>
                        </div>

                        <div>
                            <label for="expenseType" class="flex items-center justify-between font-bold mb-3">
                                <span>Categoria <span class="text-red-500">*</span></span>
                                <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:inline" v-tooltip.top="'Classificação da despesa (ex: Alimentação, Transporte, etc.)'" />
                            </label>
                            <Select
                                id="expenseType"
                                v-model="expenseStore.expense.expenseTypeId"
                                :options="expenseTypeStore.expenseTypeOptions"
                                optionLabel="label"
                                optionValue="value"
                                placeholder="Selecione a categoria"
                                :invalid="submitted && !expenseStore.expense.expenseTypeId"
                                filter
                                fluid
                            />
                            <small v-if="submitted && !expenseStore.expense.expenseTypeId" class="text-red-500">Categoria é obrigatória.</small>
                        </div>

                        <div>
                            <label for="dueDate" class="flex items-center justify-between font-bold mb-3">
                                <span>Data de Vencimento <span class="text-red-500">*</span></span>
                                <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:inline" v-tooltip.top="'Data em que a despesa vence ou foi recebida'" />
                            </label>
                            <DatePicker id="dueDate" v-model="expenseStore.expense.dueDate" dateFormat="dd/mm/yy" placeholder="Selecione a data" :invalid="submitted && !expenseStore.expense.dueDate" fluid />
                            <small v-if="submitted && !expenseStore.expense.dueDate" class="text-red-500">Data de vencimento é obrigatória.</small>
                        </div>

                        <div>
                            <label for="isPaid" class="flex items-center justify-between font-bold mb-3">
                                <span>Pago</span>
                                <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:inline" v-tooltip.top="'Indica se a despesa foi paga ou está pendente'" />
                            </label>
                            <Select id="isPaid" v-model="expenseStore.expense.isPaid" :options="paidOptions" optionLabel="label" optionValue="value" placeholder="Selecione o status" fluid />
                        </div>

                        <div class="col-span-1 md:col-span-3">
                            <label for="description" class="flex items-center justify-between font-bold mb-3">
                                <span>Descrição</span>
                                <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:inline" v-tooltip.top="'Descrição detalhada da despesa (opcional)'" />
                            </label>
                            <Textarea id="description" v-model="expenseStore.expense.description" rows="3" placeholder="Digite a descrição da despesa" fluid />
                        </div>
                    </div>

                    <template #footer>
                        <Button label="Cancelar" icon="pi pi-times" text @click="hideDialog" />
                        <Button label="Salvar" icon="pi pi-check" @click="saveExpense" />
                    </template>
                </Dialog>

                <Dialog v-model:visible="deleteExpenseDialog" :style="{ width: '450px' }" header="Confirmar" :modal="true">
                    <div class="flex items-center gap-4">
                        <i class="pi pi-exclamation-triangle !text-3xl" />
                        <span v-if="expenseStore.expense">
                            Tem certeza de que deseja deletar a despesa/receita <b>{{ expenseStore.expense.name }}</b
                            >?
                        </span>
                    </div>
                    <template #footer>
                        <Button label="Não" icon="pi pi-times" text @click="deleteExpenseDialog = false" />
                        <Button label="Sim" icon="pi pi-check" @click="deleteExpense" />
                    </template>
                </Dialog>

                <Dialog v-model:visible="deleteExpensesDialog" :style="{ width: '450px' }" header="Confirmar" :modal="true">
                    <div class="flex items-center gap-4">
                        <i class="pi pi-exclamation-triangle !text-3xl" />
                        <span v-if="expenseStore.selectedExpenses"> Tem certeza de que deseja deletar as despesas/receitas selecionadas? </span>
                    </div>
                    <template #footer>
                        <Button label="Não" icon="pi pi-times" text @click="deleteExpensesDialog = false" />
                        <Button label="Sim" icon="pi pi-check" @click="deleteSelectedExpenses" />
                    </template>
                </Dialog>
            </div>
        </div>
    </div>
</template>
