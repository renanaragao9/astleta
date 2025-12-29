<script setup lang="ts">
import { onMounted, watch, reactive, ref, computed } from 'vue';
import { useFinancialStore } from '@/stores/company/financialStore';
import type { FinancialFilters } from '@/types/company/financial';
import { useFormat } from '@/utils/useFormat';
import { formatLocalDate } from '@/utils/dateUtils';

const financialStore = useFinancialStore();
const { formatCurrency } = useFormat();

const filtersObj = reactive({
    startDate: new Date(),
    endDate: new Date()
});

const selectedModule = ref<string>('realizados');

const moduleOptions = ref<{ label: string; value: string }[]>([
    { label: 'Realizados', value: 'realizados' },
    { label: 'Possíveis', value: 'possiveis' },
    { label: 'Cancelados', value: 'cancelados' }
]);

const searchFilters = reactive({
    actualExpenses: '',
    actualBookings: '',
    actualTabs: '',
    actualTransfers: '',
    actualPurchases: '',
    possibleExpenses: '',
    possibleBookings: '',
    possibleTabs: '',
    canceledBookings: '',
    canceledTabs: '',
    canceledPurchases: ''
});

const filteredActualExpenses = computed(() => {
    const data = financialStore.financials?.actual.expenseReferences || [];
    if (!searchFilters.actualExpenses) return data;
    return data.filter((item) => item.name.toLowerCase().includes(searchFilters.actualExpenses.toLowerCase()) || (item.type && item.type.toLowerCase().includes(searchFilters.actualExpenses.toLowerCase())));
});

const filteredActualBookings = computed(() => {
    const data = financialStore.financials?.actual.bookingReferences || [];
    if (!searchFilters.actualBookings) return data;
    return data.filter(
        (item) =>
            item.name.toLowerCase().includes(searchFilters.actualBookings.toLowerCase()) ||
            (item.status && item.status.toLowerCase().includes(searchFilters.actualBookings.toLowerCase())) ||
            (item.fieldName && item.fieldName.toLowerCase().includes(searchFilters.actualBookings.toLowerCase()))
    );
});

const filteredActualTabs = computed(() => {
    const data = financialStore.financials?.actual.tabReferences || [];
    if (!searchFilters.actualTabs) return data;
    return data.filter((item) => item.name.toLowerCase().includes(searchFilters.actualTabs.toLowerCase()) || (item.status && item.status.toLowerCase().includes(searchFilters.actualTabs.toLowerCase())));
});

const filteredActualTransfers = computed(() => {
    const data = financialStore.financials?.actual.transferReferences || [];
    if (!searchFilters.actualTransfers) return data;
    return data.filter((item) => item.name.toLowerCase().includes(searchFilters.actualTransfers.toLowerCase()));
});

const filteredActualPurchases = computed(() => {
    const data = financialStore.financials?.actual.purchaseReferences || [];
    if (!searchFilters.actualPurchases) return data;
    return data.filter((item) => item.number.toLowerCase().includes(searchFilters.actualPurchases.toLowerCase()));
});

const filteredPossibleExpenses = computed(() => {
    const data = financialStore.financials?.possible.expenseReferences || [];
    if (!searchFilters.possibleExpenses) return data;
    return data.filter((item) => item.name.toLowerCase().includes(searchFilters.possibleExpenses.toLowerCase()) || (item.type && item.type.toLowerCase().includes(searchFilters.possibleExpenses.toLowerCase())));
});

const filteredPossibleBookings = computed(() => {
    const data = financialStore.financials?.possible.bookingReferences || [];
    if (!searchFilters.possibleBookings) return data;
    return data.filter(
        (item) =>
            item.name.toLowerCase().includes(searchFilters.possibleBookings.toLowerCase()) ||
            (item.status && item.status.toLowerCase().includes(searchFilters.possibleBookings.toLowerCase())) ||
            (item.fieldName && item.fieldName.toLowerCase().includes(searchFilters.possibleBookings.toLowerCase()))
    );
});

const filteredPossibleTabs = computed(() => {
    const data = financialStore.financials?.possible.tabReferences || [];
    if (!searchFilters.possibleTabs) return data;
    return data.filter((item) => item.name.toLowerCase().includes(searchFilters.possibleTabs.toLowerCase()) || (item.status && item.status.toLowerCase().includes(searchFilters.possibleTabs.toLowerCase())));
});

const filteredCanceledBookings = computed(() => {
    const data = financialStore.financials?.canceled.bookingReferences || [];
    if (!searchFilters.canceledBookings) return data;
    return data.filter(
        (item) =>
            item.name.toLowerCase().includes(searchFilters.canceledBookings.toLowerCase()) ||
            (item.status && item.status.toLowerCase().includes(searchFilters.canceledBookings.toLowerCase())) ||
            (item.fieldName && item.fieldName.toLowerCase().includes(searchFilters.canceledBookings.toLowerCase()))
    );
});

const filteredCanceledTabs = computed(() => {
    const data = financialStore.financials?.canceled.tabReferences || [];
    if (!searchFilters.canceledTabs) return data;
    return data.filter((item) => item.name.toLowerCase().includes(searchFilters.canceledTabs.toLowerCase()) || (item.status && item.status.toLowerCase().includes(searchFilters.canceledTabs.toLowerCase())));
});

const filteredCanceledPurchases = computed(() => {
    const data = financialStore.financials?.canceled.purchaseReferences || [];
    if (!searchFilters.canceledPurchases) return data;
    return data.filter((item) => item.number.toLowerCase().includes(searchFilters.canceledPurchases.toLowerCase()) || (item.status && item.status.toLowerCase().includes(searchFilters.canceledPurchases.toLowerCase())));
});

const getColorClasses = (color: string) => {
    const colors = {
        blue: 'text-blue-600 bg-blue-50 dark:bg-blue-900/20 dark:text-blue-400',
        green: 'text-green-600 bg-green-50 dark:bg-green-900/20 dark:text-green-400',
        orange: 'text-orange-600 bg-orange-50 dark:bg-orange-900/20 dark:text-orange-400',
        purple: 'text-purple-600 bg-purple-50 dark:bg-purple-900/20 dark:text-purple-400',
        red: 'text-red-600 bg-red-50 dark:bg-red-900/20 dark:text-red-400'
    };
    return colors[color as keyof typeof colors] || colors.blue;
};

const getNumberColorClasses = (color: string) => {
    const colors = {
        blue: 'text-blue-600 dark:text-blue-400',
        green: 'text-green-600 dark:text-green-400',
        orange: 'text-orange-600 dark:text-orange-400',
        purple: 'text-purple-600 dark:text-purple-400',
        red: 'text-red-600 dark:text-red-400'
    };
    return colors[color as keyof typeof colors] || colors.blue;
};

const safeValue = (value: number) => {
    if (value === undefined || value === null || isNaN(value)) {
        return 0;
    }
    return value;
};

const getBalanceColor = (balance: number) => {
    return balance >= 0 ? 'green' : 'red';
};

onMounted(async () => {
    setToday();
});

watch(
    () => filtersObj,
    async () => {
        await loadFinancials();
    },
    { deep: true }
);

async function loadFinancials(): Promise<void> {
    const filters: FinancialFilters = {
        start_date: filtersObj.startDate ? formatLocalDate(filtersObj.startDate) : undefined,
        end_date: filtersObj.endDate ? formatLocalDate(filtersObj.endDate) : undefined
    };

    await financialStore.fetchFinancials(filters);
}

function setToday(): void {
    filtersObj.startDate = new Date();
    filtersObj.endDate = new Date();
}

function setWeek(): void {
    const today = new Date();
    const startOfWeek = new Date(today);
    startOfWeek.setDate(today.getDate() - today.getDay());
    const endOfWeek = new Date(today);
    endOfWeek.setDate(today.getDate() + (6 - today.getDay()));

    filtersObj.startDate = startOfWeek;
    filtersObj.endDate = endOfWeek;
}

function setMonth(): void {
    const today = new Date();
    const startOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
    const endOfMonth = new Date(today.getFullYear(), today.getMonth() + 1, 0);

    filtersObj.startDate = startOfMonth;
    filtersObj.endDate = endOfMonth;
}

function resetFilters(): void {
    setToday();
}

function exportCSV(): void {
    const data = [];
    const financials = financialStore.financials;

    if (!financials) return;

    data.push({ Categoria: 'Período', Nome: 'Data Inicial', Valor: financials.period.startDate || '', Tipo: '' });
    data.push({ Categoria: 'Período', Nome: 'Data Final', Valor: financials.period.endDate || '', Tipo: '' });

    if (selectedModule.value === 'realizados') {
        data.push({ Categoria: 'Totais Realizados', Nome: 'Receitas', Valor: formatCurrency(financials.actual.totalRevenue || 0), Tipo: '' });
        data.push({ Categoria: 'Totais Realizados', Nome: 'Despesas', Valor: formatCurrency(financials.actual.totalExpenses || 0), Tipo: '' });
        data.push({ Categoria: 'Totais Realizados', Nome: 'Saldo', Valor: formatCurrency(financials.actual.totalBalance || 0), Tipo: '' });

        financials.actual.expenseReferences?.forEach((item) => {
            data.push({ Categoria: 'Despesas Realizadas', Nome: item.name, Valor: formatCurrency(item.value), Tipo: item.type === 'entrada' ? 'Entrada' : 'Saída' });
        });

        financials.actual.bookingReferences?.forEach((item) => {
            data.push({ Categoria: 'Reservas Realizadas', Nome: item.name, Valor: formatCurrency(item.value), Tipo: '' });
        });

        financials.actual.tabReferences?.forEach((item) => {
            data.push({ Categoria: 'Comandas Realizadas', Nome: item.name, Valor: formatCurrency(item.value), Tipo: '' });
        });

        financials.actual.transferReferences?.forEach((item) => {
            data.push({ Categoria: 'Despesas Realizadas', Nome: item.name, Valor: formatCurrency(item.value), Tipo: item.type === 'entrada' ? 'Entrada' : item.type === 'saida' ? 'Saída' : '' });
        });
    } else if (selectedModule.value === 'possiveis') {
        data.push({ Categoria: 'Totais Possíveis', Nome: 'Receitas', Valor: formatCurrency(financials.possible.revenue || 0), Tipo: '' });
        data.push({ Categoria: 'Totais Possíveis', Nome: 'Despesas', Valor: formatCurrency(financials.possible.expenses || 0), Tipo: '' });

        financials.possible.expenseReferences?.forEach((item) => {
            data.push({ Categoria: 'Despesas Possíveis', Nome: item.name, Valor: formatCurrency(item.value), Tipo: item.type === 'entrada' ? 'Entrada' : 'Saída' });
        });

        financials.possible.bookingReferences?.forEach((item) => {
            data.push({ Categoria: 'Reservas Possíveis', Nome: item.name, Valor: formatCurrency(item.value), Tipo: '' });
        });

        financials.possible.tabReferences?.forEach((item) => {
            data.push({ Categoria: 'Comandas Possíveis', Nome: item.name, Valor: formatCurrency(item.value), Tipo: '' });
        });
    } else if (selectedModule.value === 'cancelados') {
        data.push({ Categoria: 'Cancelados', Nome: 'Valor Total', Valor: formatCurrency(financials.canceled.amount || 0), Tipo: '' });

        financials.canceled.bookingReferences?.forEach((item) => {
            data.push({ Categoria: 'Reservas Canceladas', Nome: item.name, Valor: formatCurrency(item.value), Tipo: '' });
        });

        financials.canceled.tabReferences?.forEach((item) => {
            data.push({ Categoria: 'Comandas Canceladas', Nome: item.name, Valor: formatCurrency(item.value), Tipo: '' });
        });
    }

    const headers = ['Categoria', 'Nome', 'Valor', 'Tipo'];
    const csvContent = [headers.join(','), ...data.map((row) => headers.map((header) => `"${row[header as keyof typeof row]}"`).join(','))].join('\n');

    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', `relatorio_financeiro_${selectedModule.value}.csv`);
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
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 gap-2">
                        <div>
                            <h5 class="mb-1" v-tooltip.top="'Dashboard financeiro com visão completa de receitas, despesas e projeções'">Relatório Financeiro</h5>
                            <p class="text-base text-gray-600 dark:text-gray-400">
                                <i class="pi pi-calendar-clock mr-1"></i>
                                Última atualização: {{ new Date().toLocaleString('pt-BR') }}
                            </p>
                        </div>

                        <div class="flex flex-col md:flex-row gap-2 items-stretch md:items-center w-full md:w-auto">
                            <Button label="Atualizar" icon="pi pi-refresh" severity="info" @click="loadFinancials" :loading="financialStore.loading" class="w-full md:w-auto" fluid v-tooltip.top="'Recarregar dados financeiros'" />
                            <Select v-model="selectedModule" :options="moduleOptions" optionLabel="label" optionValue="value" placeholder="Módulo" class="w-full md:w-32" fluid v-tooltip.top="'Selecione o módulo para exportar'" />
                            <Button label="Exportar CSV" icon="pi pi-upload" severity="secondary" @click="exportCSV" class="w-full md:w-auto" fluid v-tooltip.top="'Exportar dados financeiros em formato CSV'" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div class="col">
                            <label for="startDate" class="block text-base font-medium text-gray-700">Data Inicial</label>
                            <DatePicker id="startDate" v-model="filtersObj.startDate" dateFormat="dd/mm/yy" :showIcon="true" class="w-full" fluid v-tooltip.top="'Selecione a data inicial para filtrar o relatório financeiro'" />
                        </div>

                        <div class="col">
                            <label for="endDate" class="block text-base font-medium text-gray-700">Data Final</label>
                            <DatePicker id="endDate" v-model="filtersObj.endDate" dateFormat="dd/mm/yy" :showIcon="true" class="w-full" fluid v-tooltip.top="'Selecione a data final para filtrar o relatório financeiro'" />
                        </div>

                        <div class="col flex flex-wrap md:flex-nowrap items-end gap-2">
                            <Button label="Hoje" icon="pi pi-calendar" @click="setToday" class="p-button-secondary w-full md:w-auto" fluid v-tooltip.top="'Definir período para o dia atual'" />
                            <Button label="Semana" icon="pi pi-calendar-plus" @click="setWeek" class="p-button-secondary w-full md:w-auto" fluid v-tooltip.top="'Definir período para a semana atual'" />
                            <Button label="Mês" icon="pi pi-calendar-times" @click="setMonth" class="p-button-secondary w-full md:w-auto" fluid v-tooltip.top="'Definir período para o mês atual'" />
                            <Button label="Limpar" icon="pi pi-times" @click="resetFilters" class="p-button-secondary w-full md:w-auto" fluid v-tooltip.top="'Limpar filtros e voltar ao período atual'" />
                        </div>
                    </div>

                    <div class="mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div class="col">
                                <h6 class="text-gray-700 dark:text-gray-300 mb-2">Período Analisado</h6>
                                <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded-lg">
                                    <p class="text-base mb-1"><strong>Início:</strong> {{ financialStore.financials?.period.startDate ? new Date(financialStore.financials.period.startDate).toLocaleString('pt-BR') : '-' }}</p>
                                    <p class="text-base"><strong>Fim:</strong> {{ financialStore.financials?.period.endDate ? new Date(financialStore.financials.period.endDate).toLocaleString('pt-BR') : '-' }}</p>
                                </div>
                            </div>

                            <div class="col">
                                <h6 class="text-gray-700 dark:text-gray-300 mb-2">Resumo de Transações</h6>
                                <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded-lg">
                                    <div class="grid grid-cols-2 gap-2 text-base">
                                        <div>
                                            <span class="text-blue-600">Reservas:</span>
                                            {{ (financialStore.financials?.totals.bookingsCount || 0) + (financialStore.financials?.totals.possibleBookingsCount || 0) + (financialStore.financials?.totals.canceledBookingsCount || 0) }}
                                        </div>
                                        <div>
                                            <span class="text-purple-600">Comandas:</span>
                                            {{ (financialStore.financials?.totals.tabsCount || 0) + (financialStore.financials?.totals.possibleTabsCount || 0) + (financialStore.financials?.totals.canceledTabsCount || 0) }}
                                        </div>
                                        <div><span class="text-red-600">Despesas / Receitas:</span> {{ (financialStore.financials?.totals.expensesCount || 0) + (financialStore.financials?.totals.possibleExpensesCount || 0) }}</div>
                                        <div><span class="text-orange-600">Taxas do Sistema:</span> {{ financialStore.financials?.totals.transfersCount || 0 }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4">
                        <h5 class="text-xl font-semibold" v-tooltip.top="'Visão geral dos principais indicadores financeiros do período selecionado'">Dashboard Financeiro</h5>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                        <div class="col">
                            <Card
                                class="border border-gray-200 dark:border-gray-700 group hover:shadow-lg transition-all duration-300 hover:-translate-y-1 cursor-pointer"
                                v-tooltip.top="'Total de receitas efetivamente realizadas no período selecionado'"
                            >
                                <template #content>
                                    <div class="p-4">
                                        <div class="flex items-center justify-between gap-3">
                                            <div class="flex-1 min-w-0">
                                                <div class="text-2xl font-bold truncate" :class="getNumberColorClasses('blue')">
                                                    {{ formatCurrency(financialStore.financials?.actual.totalRevenue || 0) }}
                                                </div>
                                                <div class="text-base text-gray-600 dark:text-gray-400 mt-1 truncate">Receitas do Período</div>
                                            </div>
                                            <div class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center transition-transform group-hover:scale-110" :class="getColorClasses('blue')">
                                                <i class="pi pi-dollar text-lg"></i>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </Card>
                        </div>
                        <div class="col">
                            <Card
                                class="border border-gray-200 dark:border-gray-700 group hover:shadow-lg transition-all duration-300 hover:-translate-y-1 cursor-pointer"
                                v-tooltip.top="'Total de despesas efetivamente realizadas no período selecionado'"
                            >
                                <template #content>
                                    <div class="p-4">
                                        <div class="flex items-center justify-between gap-3">
                                            <div class="flex-1 min-w-0">
                                                <div class="text-2xl font-bold truncate" :class="getNumberColorClasses('red')">
                                                    {{ formatCurrency(financialStore.financials?.actual.totalExpenses || 0) }}
                                                </div>
                                                <div class="text-base text-gray-600 dark:text-gray-400 mt-1 truncate">Despesas do Período</div>
                                            </div>
                                            <div class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center transition-transform group-hover:scale-110" :class="getColorClasses('red')">
                                                <i class="pi pi-minus-circle text-lg"></i>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </Card>
                        </div>
                        <div class="col">
                            <Card
                                class="border border-gray-200 dark:border-gray-700 group hover:shadow-lg transition-all duration-300 hover:-translate-y-1 cursor-pointer"
                                v-tooltip.top="'Saldo resultante (Receitas - Despesas) do período selecionado'"
                            >
                                <template #content>
                                    <div class="p-4">
                                        <div class="flex items-center justify-between gap-3">
                                            <div class="flex-1 min-w-0">
                                                <div class="text-2xl font-bold truncate" :class="getNumberColorClasses(getBalanceColor(safeValue(financialStore.financials?.actual.totalBalance || 0)))">
                                                    {{ formatCurrency(safeValue(financialStore.financials?.actual.totalBalance || 0)) }}
                                                </div>
                                                <div class="text-base text-gray-600 dark:text-gray-400 mt-1 truncate">Saldo do Período</div>
                                            </div>
                                            <div
                                                class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center transition-transform group-hover:scale-110"
                                                :class="getColorClasses(getBalanceColor(safeValue(financialStore.financials?.actual.totalBalance || 0)))"
                                            >
                                                <i class="pi pi-check-circle text-lg"></i>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </Card>
                        </div>
                        <div class="col">
                            <Card class="border border-gray-200 dark:border-gray-700 group hover:shadow-lg transition-all duration-300 hover:-translate-y-1 cursor-pointer" v-tooltip.top="'Valor total de cancelamentos no período selecionado'">
                                <template #content>
                                    <div class="p-4">
                                        <div class="flex items-center justify-between gap-3">
                                            <div class="flex-1 min-w-0">
                                                <div class="text-2xl font-bold truncate" :class="getNumberColorClasses('purple')">
                                                    {{ formatCurrency(safeValue(financialStore.financials?.canceled.amount || 0)) }}
                                                </div>
                                                <div class="text-base text-gray-600 dark:text-gray-400 mt-1 truncate">Cancelamentos</div>
                                            </div>
                                            <div class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center transition-transform group-hover:scale-110" :class="getColorClasses('purple')">
                                                <i class="pi pi-times-circle text-lg"></i>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </Card>
                        </div>
                    </div>

                    <h6 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-200">Análise Rápida do Período</h6>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                        <Card
                            class="border border-gray-200 dark:border-gray-700 group hover:shadow-lg transition-all duration-300 hover:-translate-y-1 cursor-pointer"
                            v-tooltip.top="'Taxa de Conversão = (Reservas Concluídas / Total de Reservas) × 100%'"
                        >
                            <template #content>
                                <div class="p-4">
                                    <div class="flex items-center justify-between gap-3">
                                        <div class="flex-1 min-w-0">
                                            <div class="text-2xl font-bold truncate" :class="getNumberColorClasses('blue')">
                                                {{
                                                    (
                                                        ((financialStore.financials?.totals.bookingsCount || 0) /
                                                            Math.max(
                                                                (financialStore.financials?.totals.bookingsCount || 0) + (financialStore.financials?.totals.possibleBookingsCount || 0) + (financialStore.financials?.totals.canceledBookingsCount || 0),
                                                                1
                                                            )) *
                                                        100
                                                    ).toFixed(1)
                                                }}%
                                            </div>
                                            <div class="text-base text-gray-600 dark:text-gray-400 mt-1 truncate">Taxa de Conversão</div>
                                        </div>
                                        <div class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center transition-transform group-hover:scale-110" :class="getColorClasses('blue')">
                                            <i class="pi pi-percentage text-lg"></i>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </Card>

                        <Card
                            class="border border-gray-200 dark:border-gray-700 group hover:shadow-lg transition-all duration-300 hover:-translate-y-1 cursor-pointer"
                            v-tooltip.top="'Valor Médio = (Receita Reservas + Receita Comandas) / (Nº Reservas + Nº Comandas)'"
                        >
                            <template #content>
                                <div class="p-4">
                                    <div class="flex items-center justify-between gap-3">
                                        <div class="flex-1 min-w-0">
                                            <div class="text-2xl font-bold truncate" :class="getNumberColorClasses('green')">
                                                {{
                                                    formatCurrency(
                                                        ((financialStore.financials?.totals.bookingRevenue || 0) + (financialStore.financials?.totals.tabRevenue || 0)) /
                                                            Math.max((financialStore.financials?.totals.bookingsCount || 0) + (financialStore.financials?.totals.tabsCount || 0), 1)
                                                    )
                                                }}
                                            </div>
                                            <div class="text-base text-gray-600 dark:text-gray-400 mt-1 truncate">Valor Médio por Transação</div>
                                        </div>
                                        <div class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center transition-transform group-hover:scale-110" :class="getColorClasses('green')">
                                            <i class="pi pi-dollar text-lg"></i>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </Card>

                        <Card class="border border-gray-200 dark:border-gray-700 group hover:shadow-lg transition-all duration-300 hover:-translate-y-1 cursor-pointer" v-tooltip.top="'Margem de Lucro = (Saldo Total / Receitas Totais) × 100%'">
                            <template #content>
                                <div class="p-4">
                                    <div class="flex items-center justify-between gap-3">
                                        <div class="flex-1 min-w-0">
                                            <div class="text-2xl font-bold truncate" :class="getNumberColorClasses((financialStore.financials?.actual.totalRevenue || 0) > 0 ? 'green' : 'red')">
                                                {{ (((financialStore.financials?.actual.totalBalance || 0) / Math.max(financialStore.financials?.actual.totalRevenue || 0, 1)) * 100).toFixed(1) }}%
                                            </div>
                                            <div class="text-base text-gray-600 dark:text-gray-400 mt-1 truncate">Margem de Lucro</div>
                                        </div>
                                        <div
                                            class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center transition-transform group-hover:scale-110"
                                            :class="getColorClasses((financialStore.financials?.actual.totalRevenue || 0) > 0 ? 'green' : 'red')"
                                        >
                                            <i class="pi pi-chart-bar text-lg"></i>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </Card>

                        <Card class="border border-gray-200 dark:border-gray-700 group hover:shadow-lg transition-all duration-300 hover:-translate-y-1 cursor-pointer" v-tooltip.top="'Receita Potencial = Receitas Realizadas + Receitas Possíveis'">
                            <template #content>
                                <div class="p-4">
                                    <div class="flex items-center justify-between gap-3">
                                        <div class="flex-1 min-w-0">
                                            <div class="text-2xl font-bold truncate" :class="getNumberColorClasses('orange')">
                                                {{ formatCurrency((financialStore.financials?.actual.totalRevenue || 0) + (financialStore.financials?.possible.revenue || 0)) }}
                                            </div>
                                            <div class="text-base text-gray-600 dark:text-gray-400 mt-1 truncate">Receita Potencial</div>
                                        </div>
                                        <div class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center transition-transform group-hover:scale-110" :class="getColorClasses('orange')">
                                            <i class="pi pi-chart-bar text-lg"></i>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </Card>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-12 gap-6 mb-4">
                        <div class="col md:col-span-12">
                            <div class="flex justify-between items-center mb-4">
                                <h5 class="text-xl font-semibold" v-tooltip.top="'Composição da receita efetivamente realizada'">Composição da Receita</h5>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                                <div class="col">
                                    <Card
                                        class="border border-gray-200 dark:border-gray-700 group hover:shadow-lg transition-all duration-300 hover:-translate-y-1 cursor-pointer"
                                        v-tooltip.top="'Receita total proveniente de reservas efetivamente realizadas'"
                                    >
                                        <template #content>
                                            <div class="p-4">
                                                <div class="flex items-center justify-between gap-3">
                                                    <div class="flex-1 min-w-0">
                                                        <div class="text-2xl font-bold truncate" :class="getNumberColorClasses('green')">
                                                            {{ formatCurrency(financialStore.financials?.totals.bookingRevenue || 0) }}
                                                        </div>
                                                        <div class="text-base text-gray-600 dark:text-gray-400 mt-1 truncate">Reservas</div>
                                                    </div>
                                                    <div class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center transition-transform group-hover:scale-110" :class="getColorClasses('green')">
                                                        <i class="pi pi-calendar text-lg"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </Card>
                                </div>

                                <div class="col">
                                    <Card
                                        class="border border-gray-200 dark:border-gray-700 group hover:shadow-lg transition-all duration-300 hover:-translate-y-1 cursor-pointer"
                                        v-tooltip.top="'Receita total proveniente de comandas efetivamente realizadas'"
                                    >
                                        <template #content>
                                            <div class="p-4">
                                                <div class="flex items-center justify-between gap-3">
                                                    <div class="flex-1 min-w-0">
                                                        <div class="text-2xl font-bold truncate" :class="getNumberColorClasses('green')">
                                                            {{ formatCurrency(financialStore.financials?.totals.tabRevenue || 0) }}
                                                        </div>
                                                        <div class="text-base text-gray-600 dark:text-gray-400 mt-1 truncate">Comandas</div>
                                                    </div>
                                                    <div class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center transition-transform group-hover:scale-110" :class="getColorClasses('green')">
                                                        <i class="pi pi-shopping-cart text-lg"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </Card>
                                </div>

                                <div class="col">
                                    <Card
                                        class="border border-gray-200 dark:border-gray-700 group hover:shadow-lg transition-all duration-300 hover:-translate-y-1 cursor-pointer"
                                        v-tooltip.top="'Receita total proveniente de entradas efetivamente realizadas dentro da Despesas / Receitas'"
                                    >
                                        <template #content>
                                            <div class="p-4">
                                                <div class="flex items-center justify-between gap-3">
                                                    <div class="flex-1 min-w-0">
                                                        <div class="text-2xl font-bold truncate" :class="getNumberColorClasses('green')">
                                                            {{ formatCurrency(financialStore.financials?.totals.expenseInputs || 0) }}
                                                        </div>
                                                        <div class="text-base text-gray-600 dark:text-gray-400 mt-1 truncate">Entradas</div>
                                                    </div>
                                                    <div class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center transition-transform group-hover:scale-110" :class="getColorClasses('green')">
                                                        <i class="pi pi-plus-circle text-lg"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </Card>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                                <div class="col">
                                    <Card class="border border-gray-200 dark:border-gray-700 group hover:shadow-lg transition-all duration-300 hover:-translate-y-1 cursor-pointer" v-tooltip.top="'Despesa total sobre compras efetivamente realizadas'">
                                        <template #content>
                                            <div class="p-4">
                                                <div class="flex items-center justify-between gap-3">
                                                    <div class="flex-1 min-w-0">
                                                        <div class="text-2xl font-bold truncate" :class="getNumberColorClasses('red')">
                                                            {{ formatCurrency(financialStore.financials?.totals.purchases || 0) }}
                                                        </div>
                                                        <div class="text-base text-gray-600 dark:text-gray-400 mt-1 truncate">Compras</div>
                                                    </div>
                                                    <div class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center transition-transform group-hover:scale-110" :class="getColorClasses('red')">
                                                        <i class="pi pi-shopping-cart text-lg"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </Card>
                                </div>

                                <div class="col">
                                    <Card
                                        class="border border-gray-200 dark:border-gray-700 group hover:shadow-lg transition-all duration-300 hover:-translate-y-1 cursor-pointer"
                                        v-tooltip.top="'Despesa total proveniente de taxas do sistema efetivamente aplicadas'"
                                    >
                                        <template #content>
                                            <div class="p-4">
                                                <div class="flex items-center justify-between gap-3">
                                                    <div class="flex-1 min-w-0">
                                                        <div class="text-2xl font-bold truncate" :class="getNumberColorClasses('red')">
                                                            {{ formatCurrency(financialStore.financials?.totals.transfersCount || 0) }}
                                                        </div>
                                                        <div class="text-base text-gray-600 dark:text-gray-400 mt-1 truncate">Taxas</div>
                                                    </div>
                                                    <div class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center transition-transform group-hover:scale-110" :class="getColorClasses('red')">
                                                        <i class="pi pi-percentage text-lg"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </Card>
                                </div>

                                <div class="col">
                                    <Card
                                        class="border border-gray-200 dark:border-gray-700 group hover:shadow-lg transition-all duration-300 hover:-translate-y-1 cursor-pointer"
                                        v-tooltip.top="'Despesa total proveniente de saídas efetivamente realizadas dentro da Despesas / Receitas'"
                                    >
                                        <template #content>
                                            <div class="p-4">
                                                <div class="flex items-center justify-between gap-3">
                                                    <div class="flex-1 min-w-0">
                                                        <div class="text-2xl font-bold truncate" :class="getNumberColorClasses('red')">
                                                            {{ formatCurrency(financialStore.financials?.totals.expenseOutputs || 0) }}
                                                        </div>
                                                        <div class="text-base text-gray-600 dark:text-gray-400 mt-1 truncate">Saídas</div>
                                                    </div>
                                                    <div class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center transition-transform group-hover:scale-110" :class="getColorClasses('red')">
                                                        <i class="pi pi-minus-circle text-lg"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </Card>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                                <div class="col">
                                    <Card class="border border-gray-200 dark:border-gray-700 group hover:shadow-lg transition-all duration-300 hover:-translate-y-1 cursor-pointer" v-tooltip.top="'Total de receitas efetivamente realizadas'">
                                        <template #content>
                                            <div class="p-4">
                                                <div class="flex items-center justify-between gap-3">
                                                    <div class="flex-1 min-w-0">
                                                        <div class="text-2xl font-bold truncate" :class="getNumberColorClasses('blue')">
                                                            {{ formatCurrency(financialStore.financials?.actual.totalRevenue || 0) }}
                                                        </div>
                                                        <div class="text-base text-gray-600 dark:text-gray-400 mt-1 truncate">Receitas</div>
                                                    </div>
                                                    <div class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center transition-transform group-hover:scale-110" :class="getColorClasses('blue')">
                                                        <i class="pi pi-dollar text-lg"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </Card>
                                </div>
                                <div class="col">
                                    <Card class="border border-gray-200 dark:border-gray-700 group hover:shadow-lg transition-all duration-300 hover:-translate-y-1 cursor-pointer" v-tooltip.top="'Total de despesas efetivamente realizadas'">
                                        <template #content>
                                            <div class="p-4">
                                                <div class="flex items-center justify-between gap-3">
                                                    <div class="flex-1 min-w-0">
                                                        <div class="text-2xl font-bold truncate" :class="getNumberColorClasses('red')">
                                                            {{ formatCurrency(financialStore.financials?.actual.totalExpenses || 0) }}
                                                        </div>
                                                        <div class="text-base text-gray-600 dark:text-gray-400 mt-1 truncate">Despesas</div>
                                                    </div>
                                                    <div class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center transition-transform group-hover:scale-110" :class="getColorClasses('red')">
                                                        <i class="pi pi-minus-circle text-lg"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </Card>
                                </div>
                                <div class="col">
                                    <Card
                                        class="border border-gray-200 dark:border-gray-700 group hover:shadow-lg transition-all duration-300 hover:-translate-y-1 cursor-pointer"
                                        v-tooltip.top="'Saldo resultante (Receitas - Despesas) efetivamente realizadas'"
                                    >
                                        <template #content>
                                            <div class="p-4">
                                                <div class="flex items-center justify-between gap-3">
                                                    <div class="flex-1 min-w-0">
                                                        <div class="text-2xl font-bold truncate" :class="getNumberColorClasses(getBalanceColor(safeValue(financialStore.financials?.actual.totalBalance || 0)))">
                                                            {{ formatCurrency(financialStore.financials?.actual.totalBalance || 0) }}
                                                        </div>
                                                        <div class="text-base text-gray-600 dark:text-gray-400 mt-1 truncate">Saldo</div>
                                                    </div>
                                                    <div
                                                        class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center transition-transform group-hover:scale-110"
                                                        :class="getColorClasses(getBalanceColor(safeValue(financialStore.financials?.actual.totalBalance || 0)))"
                                                    >
                                                        <i class="pi pi-check-circle text-lg"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </Card>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-2">
                                <div class="col md:col-span-4">
                                    <Card class="border border-gray-200 dark:border-gray-700" style="height: 320px" v-tooltip.top="'Lista detalhada das despesas efetivamente realizadas'">
                                        <template #content>
                                            <div class="flex justify-between items-center mb-3">
                                                <h6 class="m-0">Despesas / Receitas ({{ financialStore.financials?.totals.expensesCount || 0 }})</h6>
                                                <small class="text-sm text-gray-500"
                                                    >Despesas {{ formatCurrency(financialStore.financials?.totals.expenseOutputs || 0) }} / Receitas {{ formatCurrency(financialStore.financials?.totals.expenseInputs || 0) }}</small
                                                >
                                            </div>
                                            <InputText v-model="searchFilters.actualExpenses" placeholder="Buscar..." class="w-full mb-3 p-inputtext-sm" />

                                            <DataTable :value="filteredActualExpenses" class="p-datatable-sm" :scrollable="true" scrollHeight="200px">
                                                <Column field="name" header="Nome"></Column>

                                                <Column header="Tipo">
                                                    <template #body="slotProps">
                                                        <Badge :value="slotProps.data.type === 'entrada' ? 'Entrada' : 'Saída'" :severity="slotProps.data.type === 'entrada' ? 'success' : 'danger'" />
                                                    </template>
                                                </Column>

                                                <Column header="Valor">
                                                    <template #body="slotProps">
                                                        {{ formatCurrency(slotProps.data.value) }}
                                                    </template>
                                                </Column>

                                                <Column header="Vencimento">
                                                    <template #body="slotProps">
                                                        <span v-if="slotProps.data.dueDate">{{ new Date(slotProps.data.dueDate).toLocaleDateString('pt-BR') }}</span>
                                                        <span v-else>-</span>
                                                    </template>
                                                </Column>
                                                <template #empty>Nenhum registro!</template>
                                            </DataTable>
                                        </template>
                                    </Card>
                                </div>
                                <div class="col md:col-span-4">
                                    <Card class="border border-gray-200 dark:border-gray-700" style="height: 320px" v-tooltip.top="'Lista detalhada das reservas efetivamente realizadas'">
                                        <template #content>
                                            <div class="flex justify-between items-center mb-3">
                                                <h6 class="m-0">Reservas ({{ financialStore.financials?.totals.bookingsCount || 0 }})</h6>
                                                <small class="text-sm text-gray-500">{{ formatCurrency(financialStore.financials?.totals.bookingRevenue || 0) }}</small>
                                            </div>

                                            <InputText v-model="searchFilters.actualBookings" placeholder="Buscar..." class="w-full mb-3 p-inputtext-sm" />

                                            <DataTable :value="filteredActualBookings" class="p-datatable-sm" :scrollable="true" scrollHeight="200px">
                                                <Column field="name" header="Número"></Column>

                                                <Column header="Campo">
                                                    <template #body="slotProps">
                                                        {{ slotProps.data.fieldName || '-' }}
                                                    </template>
                                                </Column>

                                                <Column header="Status">
                                                    <template #body="slotProps">
                                                        <Badge :value="slotProps.data.status" :severity="slotProps.data.status === 'concluido' ? 'success' : 'info'" />
                                                    </template>
                                                </Column>

                                                <Column header="Valor">
                                                    <template #body="slotProps">
                                                        {{ formatCurrency(slotProps.data.value) }}
                                                    </template>
                                                </Column>
                                                <template #empty>Nenhum registro!</template>
                                            </DataTable>
                                        </template>
                                    </Card>
                                </div>

                                <div class="col md:col-span-4">
                                    <Card class="border border-gray-200 dark:border-gray-700" style="height: 320px" v-tooltip.top="'Lista detalhada das compras efetivamente realizadas'">
                                        <template #content>
                                            <div class="flex justify-between items-center mb-3">
                                                <h6 class="m-0">Compras ({{ financialStore.financials?.totals.purchasesCount || 0 }})</h6>
                                                <small class="text-sm text-gray-500">{{ formatCurrency(financialStore.financials?.totals.purchases || 0) }}</small>
                                            </div>
                                            <InputText v-model="searchFilters.actualPurchases" placeholder="Buscar..." class="w-full mb-3 p-inputtext-sm" />

                                            <DataTable :value="filteredActualPurchases" class="p-datatable-sm" :scrollable="true" scrollHeight="200px">
                                                <Column field="number" header="Número"></Column>

                                                <Column header="Valor">
                                                    <template #body="slotProps">
                                                        {{ formatCurrency(slotProps.data.value) }}
                                                    </template>
                                                </Column>

                                                <Column header="Criado">
                                                    <template #body="slotProps">
                                                        <span v-if="slotProps.data.created">{{ new Date(slotProps.data.created).toLocaleDateString('pt-BR') }}</span>
                                                        <span v-else>-</span>
                                                    </template>
                                                </Column>

                                                <Column header="Data">
                                                    <template #body="slotProps">
                                                        <span v-if="slotProps.data.date">{{ new Date(slotProps.data.date).toLocaleDateString('pt-BR') }}</span>
                                                        <span v-else>-</span>
                                                    </template>
                                                </Column>

                                                <template #empty>Nenhum registro!</template>
                                            </DataTable>
                                        </template>
                                    </Card>
                                </div>

                                <div class="col md:col-span-4">
                                    <Card class="border border-gray-200 dark:border-gray-700" style="height: 320px" v-tooltip.top="'Lista detalhada das taxas do sistema efetivamente realizadas'">
                                        <template #content>
                                            <div class="flex justify-between items-center mb-3">
                                                <h6 class="m-0">Taxas do Sistema ({{ financialStore.financials?.totals.transfersCount || 0 }})</h6>
                                                <small class="text-sm text-gray-500">{{ formatCurrency(financialStore.financials?.totals.transferFees || 0) }}</small>
                                            </div>
                                            <InputText v-model="searchFilters.actualTransfers" placeholder="Buscar..." class="w-full mb-3 p-inputtext-sm" />

                                            <DataTable :value="filteredActualTransfers" class="p-datatable-sm" :scrollable="true" scrollHeight="200px">
                                                <Column field="name" header="Descrição"></Column>

                                                <Column header="Valor">
                                                    <template #body="slotProps">
                                                        {{ formatCurrency(slotProps.data.value) }}
                                                    </template>
                                                </Column>

                                                <Column header="Criado">
                                                    <template #body="slotProps">
                                                        <span v-if="slotProps.data.createdAt">{{ new Date(slotProps.data.createdAt).toLocaleDateString('pt-BR') }}</span>
                                                        <span v-else>-</span>
                                                    </template>
                                                </Column>

                                                <template #empty>Nenhum registro!</template>
                                            </DataTable>
                                        </template>
                                    </Card>
                                </div>

                                <div class="col md:col-span-4">
                                    <Card class="border border-gray-200 dark:border-gray-700" style="height: 320px" v-tooltip.top="'Lista detalhada das comandas efetivamente realizadas'">
                                        <template #content>
                                            <div class="flex justify-between items-center mb-3">
                                                <h6 class="m-0">Comandas ({{ financialStore.financials?.totals.tabsCount || 0 }})</h6>
                                                <small class="text-sm text-gray-500">{{ formatCurrency(financialStore.financials?.totals.tabRevenue || 0) }}</small>
                                            </div>

                                            <InputText v-model="searchFilters.actualTabs" placeholder="Buscar..." class="w-full mb-3 p-inputtext-sm" />

                                            <DataTable :value="filteredActualTabs" class="p-datatable-sm" :scrollable="true" scrollHeight="200px">
                                                <Column field="name" header="Código"></Column>

                                                <Column header="Status">
                                                    <template #body="slotProps">
                                                        <Badge :value="slotProps.data.status" severity="success" />
                                                    </template>
                                                </Column>

                                                <Column header="Valor">
                                                    <template #body="slotProps">
                                                        {{ formatCurrency(slotProps.data.value) }}
                                                    </template>
                                                </Column>

                                                <Column header="Fechado">
                                                    <template #body="slotProps">
                                                        <span v-if="slotProps.data.closedAt">{{ new Date(slotProps.data.closedAt).toLocaleDateString('pt-BR') }}</span>
                                                        <span v-else>-</span>
                                                    </template>
                                                </Column>

                                                <template #empty>Nenhum registro!</template>
                                            </DataTable>
                                        </template>
                                    </Card>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-12 gap-6 mb-4">
                        <div class="col md:col-span-12">
                            <div class="flex justify-between items-center mb-4">
                                <h5 class="text-xl font-semibold" v-tooltip.top="'Transações projetadas (reservas/comandas em aberto e despesas pendentes)'">Projeção de Receitas e Despesas Futuras</h5>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                <div class="col">
                                    <Card
                                        class="border border-gray-200 dark:border-gray-700 group hover:shadow-lg transition-all duration-300 hover:-translate-y-1 cursor-pointer"
                                        v-tooltip.top="'Total de receitas projetadas (reservas e comandas em aberto)'"
                                    >
                                        <template #content>
                                            <div class="p-4">
                                                <div class="flex items-center justify-between gap-3">
                                                    <div class="flex-1 min-w-0">
                                                        <div class="text-2xl font-bold truncate" :class="getNumberColorClasses('blue')">
                                                            {{ formatCurrency(financialStore.financials?.possible.revenue || 0) }}
                                                        </div>
                                                        <div class="text-sm text-gray-600 dark:text-gray-400 mt-1 truncate">Receitas</div>
                                                    </div>
                                                    <div class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center transition-transform group-hover:scale-110" :class="getColorClasses('blue')">
                                                        <i class="pi pi-dollar text-lg"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </Card>
                                </div>
                                <div class="col">
                                    <Card class="border border-gray-200 dark:border-gray-700 group hover:shadow-lg transition-all duration-300 hover:-translate-y-1 cursor-pointer" v-tooltip.top="'Total de despesas projetadas (despesas pendentes)'">
                                        <template #content>
                                            <div class="p-4">
                                                <div class="flex items-center justify-between gap-3">
                                                    <div class="flex-1 min-w-0">
                                                        <div class="text-2xl font-bold truncate" :class="getNumberColorClasses('red')">
                                                            {{ formatCurrency(financialStore.financials?.possible.expenses || 0) }}
                                                        </div>
                                                        <div class="text-sm text-gray-600 dark:text-gray-400 mt-1 truncate">Despesas</div>
                                                    </div>
                                                    <div class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center transition-transform group-hover:scale-110" :class="getColorClasses('red')">
                                                        <i class="pi pi-minus-circle text-lg"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </Card>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                                <div class="col">
                                    <Card class="border border-gray-200 dark:border-gray-700" style="height: 320px" v-tooltip.top="'Lista detalhada das despesas projetadas (pendentes)'">
                                        <template #content>
                                            <div class="flex justify-between items-center mb-3">
                                                <h6 class="m-0">Despesas Pendentes ({{ financialStore.financials?.totals.possibleExpensesCount || 0 }})</h6>
                                            </div>

                                            <InputText v-model="searchFilters.possibleExpenses" placeholder="Buscar..." class="w-full mb-3 p-inputtext-sm" />

                                            <DataTable :value="filteredPossibleExpenses" class="p-datatable-sm" :scrollable="true" scrollHeight="200px">
                                                <Column field="name" header="Nome"></Column>

                                                <Column header="Tipo">
                                                    <template #body="slotProps">
                                                        <Badge :value="slotProps.data.type === 'entrada' ? 'Entrada' : 'Saída'" :severity="slotProps.data.type === 'entrada' ? 'success' : 'warning'" />
                                                    </template>
                                                </Column>

                                                <Column header="Valor">
                                                    <template #body="slotProps">
                                                        {{ formatCurrency(slotProps.data.value) }}
                                                    </template>
                                                </Column>

                                                <Column header="Vencimento">
                                                    <template #body="slotProps">
                                                        <small v-if="slotProps.data.dueDate">{{ new Date(slotProps.data.dueDate).toLocaleDateString('pt-BR') }}</small>
                                                        <span v-else>-</span>
                                                    </template>
                                                </Column>

                                                <template #empty>Nenhum registro!</template>
                                            </DataTable>
                                        </template>
                                    </Card>
                                </div>
                                <div class="col">
                                    <Card class="border border-gray-200 dark:border-gray-700" style="height: 320px" v-tooltip.top="'Lista detalhada das reservas projetadas (em aberto)'">
                                        <template #content>
                                            <div class="flex justify-between items-center mb-3">
                                                <h6 class="m-0">Reservas Pendentes ({{ financialStore.financials?.totals.possibleBookingsCount || 0 }})</h6>
                                            </div>

                                            <InputText v-model="searchFilters.possibleBookings" placeholder="Buscar..." class="w-full mb-3 p-inputtext-sm" />

                                            <DataTable :value="filteredPossibleBookings" class="p-datatable-sm" :scrollable="true" scrollHeight="200px">
                                                <Column field="name" header="Número"></Column>

                                                <Column header="Campo">
                                                    <template #body="slotProps">
                                                        {{ slotProps.data.fieldName || '-' }}
                                                    </template>
                                                </Column>

                                                <Column header="Status">
                                                    <template #body="slotProps">
                                                        <Badge :value="slotProps.data.status" severity="warn" />
                                                    </template>
                                                </Column>

                                                <Column header="Valor">
                                                    <template #body="slotProps">
                                                        {{ formatCurrency(slotProps.data.value) }}
                                                    </template>
                                                </Column>
                                                <template #empty>Nenhum registro!</template>
                                            </DataTable>
                                        </template>
                                    </Card>
                                </div>
                                <div class="col">
                                    <Card class="border border-gray-200 dark:border-gray-700" style="height: 320px" v-tooltip.top="'Lista detalhada das comandas projetadas (em aberto)'">
                                        <template #content>
                                            <div class="flex justify-between items-center mb-3">
                                                <h6 class="m-0">Comandas Abertas ({{ financialStore.financials?.totals.possibleTabsCount || 0 }})</h6>
                                            </div>

                                            <InputText v-model="searchFilters.possibleTabs" placeholder="Buscar..." class="w-full mb-3 p-inputtext-sm" />

                                            <DataTable :value="filteredPossibleTabs" class="p-datatable-sm" :scrollable="true" scrollHeight="200px">
                                                <Column field="name" header="Código"></Column>

                                                <Column header="Status">
                                                    <template #body="slotProps">
                                                        <Badge :value="slotProps.data.status" severity="warning" />
                                                    </template>
                                                </Column>

                                                <Column header="Valor">
                                                    <template #body="slotProps">
                                                        {{ formatCurrency(slotProps.data.value) }}
                                                    </template>
                                                </Column>

                                                <Column header="Aberto">
                                                    <template #body="slotProps">
                                                        <small v-if="slotProps.data.openedAt">{{ new Date(slotProps.data.openedAt).toLocaleDateString('pt-BR') }}</small>
                                                        <span v-else>-</span>
                                                    </template>
                                                </Column>

                                                <template #empty>Nenhum registro!</template>
                                            </DataTable>
                                        </template>
                                    </Card>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-12 gap-6 mb-4">
                        <div class="col md:col-span-12">
                            <div class="flex justify-between items-center mb-4">
                                <h5 class="text-xl font-semibold" v-tooltip.top="'Transações que foram canceladas no período'">Cancelados</h5>
                                <div class="bg-red-100 dark:bg-red-900/30 px-3 py-1 rounded-full hidden md:block">
                                    <span class="text-sm text-red-800 dark:text-red-200"> Perda Total: {{ formatCurrency(financialStore.financials?.canceled.amount || 0) }} </span>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                                <div class="col">
                                    <Card class="border border-gray-200 dark:border-gray-700" style="height: 320px" v-tooltip.top="'Lista detalhada das reservas canceladas'">
                                        <template #content>
                                            <div class="flex justify-between items-center mb-3">
                                                <h6 class="m-0">Reservas Canceladas ({{ financialStore.financials?.totals.canceledBookingsCount || 0 }})</h6>
                                            </div>

                                            <InputText v-model="searchFilters.canceledBookings" placeholder="Buscar..." class="w-full mb-3 p-inputtext-sm" />

                                            <DataTable :value="filteredCanceledBookings" class="p-datatable-sm" :scrollable="true" scrollHeight="200px">
                                                <Column field="name" header="Número"></Column>

                                                <Column header="Campo">
                                                    <template #body="slotProps">
                                                        {{ slotProps.data.fieldName || '-' }}
                                                    </template>
                                                </Column>

                                                <Column header="Status">
                                                    <template #body="slotProps">
                                                        <Badge :value="slotProps.data.status" severity="danger" />
                                                    </template>
                                                </Column>

                                                <Column header="Valor">
                                                    <template #body="slotProps">
                                                        {{ formatCurrency(slotProps.data.value) }}
                                                    </template>
                                                </Column>

                                                <Column header="Data">
                                                    <template #body="slotProps">
                                                        <small v-if="slotProps.data.bookingDate">{{ new Date(slotProps.data.bookingDate).toLocaleDateString('pt-BR') }}</small>
                                                        <span v-else>-</span>
                                                    </template>
                                                </Column>

                                                <template #empty>Nenhum registro!</template>
                                            </DataTable>
                                        </template>
                                    </Card>
                                </div>

                                <div class="col">
                                    <Card class="border border-gray-200 dark:border-gray-700" style="height: 320px" v-tooltip.top="'Lista detalhada das comandas canceladas'">
                                        <template #content>
                                            <div class="flex justify-between items-center mb-3">
                                                <h6 class="m-0">Comandas Canceladas ({{ financialStore.financials?.totals.canceledTabsCount || 0 }})</h6>
                                            </div>
                                            <InputText v-model="searchFilters.canceledTabs" placeholder="Buscar..." class="w-full mb-3 p-inputtext-sm" />
                                            <DataTable :value="filteredCanceledTabs" class="p-datatable-sm" :scrollable="true" scrollHeight="200px">
                                                <Column field="name" header="Código"></Column>

                                                <Column header="Status">
                                                    <template #body="slotProps">
                                                        <Badge :value="slotProps.data.status" severity="danger" />
                                                    </template>
                                                </Column>

                                                <Column header="Valor">
                                                    <template #body="slotProps">
                                                        {{ formatCurrency(slotProps.data.value) }}
                                                    </template>
                                                </Column>

                                                <Column header="Fechado">
                                                    <template #body="slotProps">
                                                        <span v-if="slotProps.data.closedAt">{{ new Date(slotProps.data.closedAt).toLocaleDateString('pt-BR') }}</span>
                                                        <span v-else>-</span>
                                                    </template>
                                                </Column>
                                                <template #empty>Nenhum registro!</template>
                                            </DataTable>
                                        </template>
                                    </Card>
                                </div>

                                <div class="col">
                                    <Card class="border border-gray-200 dark:border-gray-700" style="height: 320px" v-tooltip.top="'Lista detalhada das compras canceladas'">
                                        <template #content>
                                            <div class="flex justify-between items-center mb-3">
                                                <h6 class="m-0">Compras Canceladas ({{ financialStore.financials?.totals.canceledPurchasesCount || 0 }})</h6>
                                            </div>
                                            <InputText v-model="searchFilters.canceledPurchases" placeholder="Buscar..." class="w-full mb-3 p-inputtext-sm" />
                                            <DataTable :value="filteredCanceledPurchases" class="p-datatable-sm" :scrollable="true" scrollHeight="200px">
                                                <Column field="number" header="Número"></Column>

                                                <Column header="Valor">
                                                    <template #body="slotProps">
                                                        {{ formatCurrency(slotProps.data.value) }}
                                                    </template>
                                                </Column>

                                                <Column header="Status">
                                                    <template #body="slotProps">
                                                        <Badge :value="slotProps.data.status" severity="danger" />
                                                    </template>
                                                </Column>

                                                <Column header="Criado">
                                                    <template #body="slotProps">
                                                        <span v-if="slotProps.data.created">{{ new Date(slotProps.data.created).toLocaleDateString('pt-BR') }}</span>
                                                        <span v-else>-</span>
                                                    </template>
                                                </Column>

                                                <Column header="Data">
                                                    <template #body="slotProps">
                                                        <span v-if="slotProps.data.date">{{ new Date(slotProps.data.date).toLocaleDateString('pt-BR') }}</span>
                                                        <span v-else>-</span>
                                                    </template>
                                                </Column>

                                                <template #empty>Nenhum registro!</template>
                                            </DataTable>
                                        </template>
                                    </Card>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
