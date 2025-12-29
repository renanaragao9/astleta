<script setup lang="ts">
import { ref, reactive, watch, onMounted } from 'vue';
import { useAthleteRachaStore } from '@/stores/athlete/rachaStore';
import { useToast } from 'primevue/usetoast';
import type { AthleteRacha } from '@/types/athlete/racha';
import type { DataTableSortEvent, DataTablePageEvent } from 'primevue/datatable';
import { Select } from 'primevue';

const toast = useToast();
const rachaStore = useAthleteRachaStore();

const searchTerm = ref('');
const rowsPerPage = ref(10);
const sortField = ref('bookingDate');
const sortOrder = ref<'asc' | 'desc'>('desc');
const currentPage = ref(1);
const showFiltersModal = ref(false);
const showDetailsModal = ref(false);
const selectedRacha = ref<AthleteRacha | null>(null);
const showJoinRachaModal = ref(false);
const rachaNumber = ref<string>('');
const errorMessage = ref('');

const filtersObj = reactive({
    status: '',
    startDate: null as Date | null,
    endDate: null as Date | null
});

const statusOptions = [
    { label: 'Todos', value: '' },
    { label: 'Confirmado', value: 'confirmado' },
    { label: 'Pendente', value: 'pendente' },
    { label: 'Cancelado', value: 'cancelado' },
    { label: 'Concluído', value: 'concluido' }
];

watch(
    () => filtersObj,
    async () => {
        currentPage.value = 1;
        await loadRachas();
    },
    { deep: true }
);

watch(rachaNumber, () => {
    if (errorMessage.value) {
        errorMessage.value = '';
    }
});

onMounted(async () => {
    try {
        await loadRachas();
    } catch {
        if (rachaStore.error) {
            toast.add({
                severity: 'error',
                summary: 'Erro',
                detail: rachaStore.error,
                life: 5000
            });
        }
    }
});

const loadRachas = async (): Promise<void> => {
    const filters = {
        search: searchTerm.value || undefined,
        sort: sortField.value,
        direction: sortOrder.value,
        per_page: rowsPerPage.value,
        page: currentPage.value,
        booking_status: filtersObj.status || undefined,
        start_date: filtersObj.startDate ? filtersObj.startDate.toISOString().split('T')[0] : undefined,
        end_date: filtersObj.endDate ? filtersObj.endDate.toISOString().split('T')[0] : undefined
    };
    await rachaStore.getRachas(filters);
};

const onSearch = async (): Promise<void> => {
    currentPage.value = 1;
    await loadRachas();
};

const onPageChange = async (event: DataTablePageEvent): Promise<void> => {
    rowsPerPage.value = event.rows;
    currentPage.value = event.page + 1;
    await loadRachas();
};

const onPaginatorPageChange = async (event: { page: number; first: number; rows: number }): Promise<void> => {
    currentPage.value = event.page + 1;
    rowsPerPage.value = event.rows;
    await loadRachas();
};

const onSort = async (event: DataTableSortEvent): Promise<void> => {
    if (typeof event.sortField === 'string') {
        sortField.value = event.sortField || 'bookingDate';
        sortOrder.value = event.sortOrder === 1 ? 'asc' : 'desc';
        await loadRachas();
    }
};

const joinRacha = async (): Promise<void> => {
    if (!rachaNumber.value || rachaNumber.value.trim() === '') {
        errorMessage.value = 'Por favor, digite o número da reserva';
        toast.add({
            severity: 'warn',
            summary: 'Erro',
            detail: 'Por favor, digite o número da reserva',
            life: 5000
        });
        return;
    }

    try {
        await rachaStore.joinRacha(rachaNumber.value);
        toast.add({
            severity: 'success',
            summary: 'Sucesso',
            detail: 'Entrou no racha com sucesso',
            life: 5000
        });
        showJoinRachaModal.value = false;
        rachaNumber.value = '';
        errorMessage.value = '';
        await loadRachas();
    } catch {
        if (rachaStore.error) {
            toast.add({
                severity: 'error',
                summary: 'Erro',
                detail: rachaStore.error,
                life: 5000
            });
        }
    }
};

const openDetailRacha = (racha: AthleteRacha): void => {
    selectedRacha.value = racha;
    showDetailsModal.value = true;
};

const setToday = (): void => {
    filtersObj.startDate = new Date();
    filtersObj.endDate = new Date();
};

const resetFilters = (): void => {
    filtersObj.status = '';
    filtersObj.startDate = null;
    filtersObj.endDate = null;
    searchTerm.value = '';
};

const applyFilters = (): void => {
    showFiltersModal.value = false;
};

const getStatusSeverity = (status: string): string => {
    switch (status.toLowerCase()) {
        case 'confirmado':
        case 'confirmed':
        case 'ativo':
            return 'success';
        case 'pendente':
        case 'pending':
            return 'warn';
        case 'cancelado':
        case 'cancelled':
            return 'danger';
        case 'concluido':
            return 'success';
        default:
            return 'info';
    }
};

const getStatusIcon = (status: string): string => {
    switch (status.toLowerCase()) {
        case 'confirmado':
        case 'confirmed':
        case 'ativo':
            return 'pi pi-check-circle';
        case 'pendente':
        case 'pending':
            return 'pi pi-clock';
        case 'cancelado':
        case 'cancelled':
            return 'pi pi-times-circle';
        case 'concluido':
            return 'pi pi-check';
        default:
            return 'pi pi-info-circle';
    }
};

const getStatusLabel = (status: string): string => {
    const statusMap: Record<string, string> = {
        pendente: 'Pendente',
        confirmado: 'Confirmado',
        cancelado: 'Cancelado',
        concluido: 'Concluído'
    };
    return statusMap[status] || status;
};

const statisticsArray = (racha: AthleteRacha) => {
    if (!racha.statistics) return [];
    return Object.entries(racha.statistics).map(([name, data]) => ({
        name,
        value: data.value,
        icon: data.icon || 'pi pi-chart-bar',
        color: data.color || 'primary'
    }));
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
                                <Button label="Entrar no Racha" icon="pi pi-plus" @click="showJoinRachaModal = true" class="mr-2" />
                                <DatePicker v-model="filtersObj.startDate" dateFormat="dd/mm/yy" placeholder="Data Início" class="mr-2" style="width: 140px" fluid />
                                <DatePicker v-model="filtersObj.endDate" dateFormat="dd/mm/yy" placeholder="Data Fim" class="mr-2" style="width: 140px" fluid />
                                <Button label="Hoje" @click="setToday" severity="secondary" class="mr-2" />
                                <Select v-model="filtersObj.status" :options="statusOptions" optionLabel="label" optionValue="value" placeholder="Status" class="mr-2" style="width: 140px" />
                                <Button label="Limpar Filtros" icon="pi pi-refresh" severity="secondary" @click="resetFilters" class="mr-2" v-tooltip.top="'Limpar todos os filtros aplicados'" />
                            </div>

                            <div class="block md:hidden">
                                <Button label="Entrar no Racha" icon="pi pi-plus" @click="showJoinRachaModal = true" class="mr-2" />
                                <Button label="Filtros" icon="pi pi-filter" @click="showFiltersModal = true" />
                            </div>
                        </template>
                    </Toolbar>

                    <Dialog v-model:visible="showFiltersModal" modal header="Filtros" :style="{ width: '50vw' }" :breakpoints="{ '960px': '75vw', '640px': '100vw' }" :maximizable="true">
                        <div class="flex flex-col gap-4">
                            <div class="flex flex-col gap-2">
                                <label for="startDate">Data Início</label>
                                <DatePicker v-model="filtersObj.startDate" dateFormat="dd/mm/yy" placeholder="Data Início" class="w-full" fluid />
                            </div>

                            <div class="flex flex-col gap-2">
                                <label for="endDate">Data Fim</label>
                                <DatePicker v-model="filtersObj.endDate" dateFormat="dd/mm/yy" placeholder="Data Fim" class="w-full" fluid />
                            </div>

                            <Button label="Hoje" @click="setToday" severity="secondary" class="w-full" />

                            <div class="flex flex-col gap-2">
                                <label for="status">Status</label>
                                <Select v-model="filtersObj.status" :options="statusOptions" optionLabel="label" optionValue="value" placeholder="Status" class="w-full" />
                            </div>
                        </div>
                        <template #footer>
                            <Button label="Cancelar" icon="pi pi-times" text @click="showFiltersModal = false" />
                            <Button label="Limpar Filtros" icon="pi pi-refresh" severity="secondary" @click="resetFilters" />
                            <Button label="Aplicar" icon="pi pi-check" @click="applyFilters" />
                        </template>
                    </Dialog>

                    <div class="hidden md:block">
                        <div v-if="rachaStore.loading" class="text-center py-8">
                            <ProgressSpinner />
                        </div>
                        <div v-else-if="rachaStore.rachas.length === 0" class="text-center py-8 text-gray-500">
                            <i class="pi pi-bookmark text-4xl mb-4 text-gray-400"></i>
                            <p class="mb-4">Nenhum racha encontrado</p>
                            <p class="text-sm text-600 mb-4">Você ainda não participou de nenhum racha.</p>
                        </div>
                        <div v-else>
                            <DataTable
                                ref="dt"
                                :value="rachaStore.rachas || []"
                                dataKey="id"
                                :paginator="rachaStore.rachas.length > 0 || true"
                                :rows="rowsPerPage"
                                :totalRecords="rachaStore.pagination?.total || 0"
                                paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                                :rowsPerPageOptions="[5, 10, 25, 50]"
                                currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} rachas"
                                :lazy="true"
                                :first="(currentPage - 1) * rowsPerPage"
                                @page="onPageChange"
                                @sort="onSort"
                                sortMode="single"
                                :sortField="sortField"
                                :sortOrder="sortOrder === 'asc' ? 1 : -1"
                            >
                                <template #header>
                                    <div class="flex flex-wrap gap-2 items-center justify-between">
                                        <h4 class="m-0"><i class="pi pi-bookmark mr-2"></i>Meus Rachas</h4>
                                        <div class="flex gap-2">
                                            <IconField>
                                                <InputIcon>
                                                    <i class="pi pi-search" />
                                                </InputIcon>
                                                <InputText v-model="searchTerm" placeholder="Buscar rachas..." @keyup.enter="onSearch" />
                                            </IconField>
                                            <Button icon="pi pi-search" severity="secondary" @click="onSearch" />
                                        </div>
                                    </div>
                                </template>

                                <Column field="booking_number" header="Número" sortable>
                                    <template #body="slotProps">
                                        {{ slotProps.data.bookingNumber }}
                                    </template>
                                </Column>

                                <Column field="booking_date" header="Data" sortable>
                                    <template #body="slotProps">
                                        {{ slotProps.data.bookingDate }}
                                    </template>
                                </Column>

                                <Column field="start_time" header="Horário Início" sortable>
                                    <template #body="slotProps">
                                        {{ slotProps.data.startTime.slice(0, 5) }}
                                    </template>
                                </Column>

                                <Column field="end_time" header="Horário Fim" sortable>
                                    <template #body="slotProps">
                                        {{ slotProps.data.endTime.slice(0, 5) }}
                                    </template>
                                </Column>

                                <Column field="field_name" header="Campo">
                                    <template #body="slotProps">
                                        {{ slotProps.data.fieldName }}
                                    </template>
                                </Column>

                                <Column field="booking_status" header="Status" sortable>
                                    <template #body="slotProps">
                                        <Tag :value="getStatusLabel(slotProps.data.bookingStatus)" :severity="getStatusSeverity(slotProps.data.bookingStatus)" :icon="getStatusIcon(slotProps.data.bookingStatus)" />
                                    </template>
                                </Column>

                                <Column header="Ações">
                                    <template #body="slotProps">
                                        <Button icon="pi pi-eye" class="p-button-rounded p-button-info p-button-text" @click="openDetailRacha(slotProps.data)" v-tooltip.top="'Visualizar'" />
                                    </template>
                                </Column>
                            </DataTable>
                        </div>
                    </div>

                    <div class="block md:hidden">
                        <div v-if="rachaStore.loading" class="text-center py-8">
                            <ProgressSpinner />
                        </div>
                        <div v-else-if="rachaStore.rachas.length === 0" class="text-center py-8 text-gray-500">
                            <i class="pi pi-bookmark text-4xl mb-4 text-gray-400"></i>
                            <p class="mb-4">Nenhum racha encontrado</p>
                            <p class="text-sm text-600 mb-4">Você ainda não participou de nenhum racha.</p>
                        </div>
                        <div v-else class="space-y-4">
                            <Card v-for="racha in rachaStore.rachas" :key="racha.id" class="h-full flex flex-col rounded-none sm:rounded-xl border border-gray-200 dark:border-gray-700">
                                <template #content>
                                    <div class="p-4 space-y-4 flex-grow">
                                        <div class="flex justify-between items-start mb-3">
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <Tag :value="getStatusLabel(racha.bookingStatus)" :severity="getStatusSeverity(racha.bookingStatus)" />
                                                    <p class="text-lg text-gray-600 dark:text-gray-400">{{ racha.fieldName }}</p>
                                                </div>
                                                <h3 class="font-semibold text-lg text-gray-900 dark:text-gray-100">{{ racha.bookingNumber }}</h3>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-2 gap-4 mb-4">
                                            <div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Data</p>
                                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ racha.bookingDate }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Horário</p>
                                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ racha.startTime }} - {{ racha.endTime }}</p>
                                            </div>
                                        </div>

                                        <div class="flex justify-end pt-3 border-t border-gray-100 dark:border-gray-700">
                                            <Button label="Ver Detalhes" icon="pi pi-eye" size="small" outlined @click="openDetailRacha(racha)" />
                                        </div>
                                    </div>
                                </template>
                            </Card>
                        </div>

                        <div v-if="rachaStore.rachas.length > 0" class="mt-6 flex justify-center">
                            <Paginator
                                :first="(currentPage - 1) * rowsPerPage"
                                :rows="rowsPerPage"
                                :totalRecords="rachaStore.pagination?.total || 0"
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

    <Dialog v-model:visible="showDetailsModal" modal header="Detalhes do Racha" :style="{ width: '50vw' }" :breakpoints="{ '960px': '75vw', '640px': '100vw' }" :maximizable="true">
        <div v-if="selectedRacha" class="space-y-4">
            <div class="flex justify-between items-start mb-3">
                <div>
                    <div class="flex items-center gap-2">
                        <Tag :value="getStatusLabel(selectedRacha.bookingStatus)" :severity="getStatusSeverity(selectedRacha.bookingStatus)" />
                        <p class="text-lg text-gray-600 dark:text-gray-400">{{ selectedRacha.fieldName }}</p>
                    </div>
                    <h3 class="font-semibold text-lg text-gray-900 dark:text-gray-100">{{ selectedRacha.bookingNumber }}</h3>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Data</p>
                    <p class="font-medium text-gray-900 dark:text-gray-100">{{ selectedRacha.bookingDate }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Horário</p>
                    <p class="font-medium text-gray-900 dark:text-gray-100">{{ selectedRacha.startTime }} - {{ selectedRacha.endTime }}</p>
                </div>
            </div>

            <Tabs value="stats" class="w-full">
                <TabList>
                    <Tab value="stats">
                        <i class="pi pi-chart-bar mr-2"></i>
                        Estatísticas
                    </Tab>
                    <Tab value="rating">
                        <i class="pi pi-star mr-2"></i>
                        Avaliação
                    </Tab>
                </TabList>

                <TabPanels class="pt-4">
                    <TabPanel value="stats">
                        <div v-if="statisticsArray(selectedRacha).length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div v-for="stat in statisticsArray(selectedRacha)" :key="stat.name" class="text-center p-5 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                <i :class="`${stat.icon} text-2xl text-${stat.color} mb-3`"></i>
                                <div :class="`text-xl font-bold text-${stat.color} mb-2`">{{ stat.value }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">{{ stat.name }}</div>
                            </div>
                        </div>
                        <div v-else class="text-center py-8">
                            <i class="pi pi-chart-bar text-3xl text-gray-400 mb-3"></i>
                            <p class="text-gray-500 dark:text-gray-400">Nenhuma estatística registrada.</p>
                        </div>
                    </TabPanel>

                    <TabPanel value="rating">
                        <div v-if="selectedRacha.rating" class="space-y-4">
                            <div class="text-center p-4 bg-primary-50 dark:bg-primary-900/20 rounded-lg">
                                <div class="bg-primary-500 text-white rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-2">
                                    <span class="text-xl font-bold">{{ selectedRacha.rating.overall }}</span>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Avaliação Geral</p>
                            </div>

                            <div class="grid grid-cols-2 lg:grid-cols-5 gap-3">
                                <div class="text-center p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                    <div class="text-lg font-bold text-green-600 mb-1">{{ selectedRacha.rating.technical }}</div>
                                    <div class="text-xs text-gray-600 dark:text-gray-400">Técnico</div>
                                </div>
                                <div class="text-center p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                    <div class="text-lg font-bold text-purple-600 mb-1">{{ selectedRacha.rating.tactical }}</div>
                                    <div class="text-xs text-gray-600 dark:text-gray-400">Tático</div>
                                </div>
                                <div class="text-center p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                    <div class="text-lg font-bold text-orange-600 mb-1">{{ selectedRacha.rating.physical }}</div>
                                    <div class="text-xs text-gray-600 dark:text-gray-400">Físico</div>
                                </div>
                                <div class="text-center p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                    <div class="text-lg font-bold text-indigo-600 mb-1">{{ selectedRacha.rating.mental }}</div>
                                    <div class="text-xs text-gray-600 dark:text-gray-400">Mental</div>
                                </div>
                                <div class="text-center p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                    <div class="text-lg font-bold text-blue-600 mb-1">{{ selectedRacha.rating.teamwork }}</div>
                                    <div class="text-xs text-gray-600 dark:text-gray-400">Trabalho em Equipe</div>
                                </div>
                            </div>

                            <div v-if="selectedRacha.rating.comment" class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                <h5 class="font-semibold text-gray-800 dark:text-gray-200 mb-2">Comentário</h5>
                                <p class="text-gray-600 dark:text-gray-400">{{ selectedRacha.rating.comment }}</p>
                            </div>
                        </div>
                        <div v-else class="text-center py-8">
                            <i class="pi pi-star text-3xl text-gray-400 mb-3"></i>
                            <p class="text-gray-500 dark:text-gray-400">Nenhuma avaliação disponível.</p>
                        </div>
                    </TabPanel>
                </TabPanels>
            </Tabs>
        </div>

        <template #footer>
            <Button label="Fechar" icon="pi pi-times" @click="showDetailsModal = false" />
        </template>
    </Dialog>

    <Dialog v-model:visible="showJoinRachaModal" modal header="Entrar no Racha" :style="{ width: '30vw' }" :breakpoints="{ '640px': '100vw' }" :maximizable="true">
        <div class="flex flex-col gap-4">
            <div class="flex flex-col gap-2">
                <label for="rachaNumber">Número da Reserva</label>
                <InputText v-model="rachaNumber" inputId="rachaNumber" placeholder="Digite o número da reserva" class="w-full" />
                <small v-if="errorMessage" class="text-red-500">{{ errorMessage }}</small>
            </div>
        </div>
        <template #footer>
            <Button label="Cancelar" icon="pi pi-times" text @click="showJoinRachaModal = false" />
            <Button label="Entrar" icon="pi pi-check" @click="joinRacha" />
        </template>
    </Dialog>
</template>
