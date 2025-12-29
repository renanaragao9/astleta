<script setup lang="ts">
import { ref, reactive, watch, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useBookingStore } from '@/stores/athlete/booking/bookingAthleteStore';
import type { Booking } from '@/types/athlete/booking/bookingAthlete';
import type { DataTableSortEvent, DataTablePageEvent } from 'primevue/datatable';
import { useToast } from 'primevue/usetoast';
import { useFormat } from '@/utils/useFormat';

const router = useRouter();
const toast = useToast();
const bookingStore = useBookingStore();
const { formatCurrency } = useFormat();

const searchTerm = ref('');
const rowsPerPage = ref(10);
const sortField = ref('id');
const sortOrder = ref<'asc' | 'desc'>('desc');
const currentPage = ref(1);
const showFiltersModal = ref(false);

const filtersObj = reactive({
    status: '',
    startDate: null as Date | null,
    endDate: null as Date | null,
    paymentType: ''
});

const paymentTypeOptions = [
    { label: 'Todos', value: '' },
    { label: 'Online', value: 'online' },
    { label: 'Presencial', value: 'presencial' }
];

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
        await loadBookings();
    },
    { deep: true }
);

watch(
    () => bookingStore.pagination?.currentPage,
    (newPage) => {
        if (newPage && newPage !== currentPage.value) {
            currentPage.value = newPage;
        }
    }
);

onMounted(async () => {
    try {
        await Promise.all([loadBookings()]);
    } catch {
        if (bookingStore.error) {
            toast.add({
                severity: 'error',
                summary: 'Erro',
                detail: bookingStore.error,
                life: 5000
            });
        }
    }
});

const loadBookings = async (): Promise<void> => {
    const filters = {
        search: searchTerm.value || undefined,
        sort: sortField.value,
        direction: sortOrder.value,
        per_page: rowsPerPage.value,
        page: currentPage.value,
        booking_status: filtersObj.status || undefined,
        start_date: filtersObj.startDate ? filtersObj.startDate.toISOString().split('T')[0] : undefined,
        end_date: filtersObj.endDate ? filtersObj.endDate.toISOString().split('T')[0] : undefined,
        payment_type: filtersObj.paymentType || undefined
    };

    await bookingStore.getBookings(filters);
};

const onSearch = async (): Promise<void> => {
    currentPage.value = 1;
    await loadBookings();
};

const onPageChange = async (event: DataTablePageEvent): Promise<void> => {
    rowsPerPage.value = event.rows;
    currentPage.value = event.page + 1;
    await loadBookings();
};

const onPaginatorPageChange = async (event: { page: number; first: number; rows: number }): Promise<void> => {
    currentPage.value = event.page + 1;
    rowsPerPage.value = event.rows;
    await loadBookings();
};

const onSort = async (event: DataTableSortEvent): Promise<void> => {
    if (typeof event.sortField === 'string') {
        sortField.value = event.sortField || 'bookingDate';
        sortOrder.value = event.sortOrder === 1 ? 'asc' : 'desc';
        await loadBookings();
    }
};

const downloadReceipt = async (booking: Booking): Promise<void> => {
    try {
        const blob = await bookingStore.downloadReceipt(booking.id);

        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `recibo_reserva_${booking.id}.pdf`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);

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
            detail: bookingStore.error,
            life: 5000
        });
    }
};

const openViewBooking = (booking: Booking): void => {
    router.push({ name: 'athleteBookingDetails', params: { id: booking.id.toString() } });
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

const getStatusSeverity = (status: string): string => {
    switch (status.toLowerCase()) {
        case 'confirmed':
        case 'ativo':
        case 'confirmado':
            return 'success';
        case 'pending':
        case 'pendente':
            return 'warn';
        case 'cancelled':
        case 'cancelado':
            return 'danger';
        default:
            return 'info';
    }
};

const setToday = (): void => {
    filtersObj.startDate = new Date();
    filtersObj.endDate = new Date();
};

const resetFilters = (): void => {
    filtersObj.status = '';
    filtersObj.startDate = null;
    filtersObj.endDate = null;
    filtersObj.paymentType = '';
    searchTerm.value = '';
};

const applyFilters = (): void => {
    showFiltersModal.value = false;
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
                                <DatePicker v-model="filtersObj.startDate" dateFormat="dd/mm/yy" placeholder="Data Início" class="mr-2" style="width: 140px" fluid />
                                <DatePicker v-model="filtersObj.endDate" dateFormat="dd/mm/yy" placeholder="Data Fim" class="mr-2" style="width: 140px" fluid />
                                <Button label="Hoje" @click="setToday" severity="secondary" class="mr-2" />
                                <Select v-model="filtersObj.status" :options="statusOptions" optionLabel="label" optionValue="value" placeholder="Status" class="mr-2" style="width: 140px" />
                                <Select v-model="filtersObj.paymentType" :options="paymentTypeOptions" optionLabel="label" optionValue="value" placeholder="Tipo Pagamento" class="mr-2" style="width: 140px" />
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
                            <div class="flex flex-col gap-2">
                                <label for="paymentType">Tipo de Pagamento</label>
                                <Select v-model="filtersObj.paymentType" :options="paymentTypeOptions" optionLabel="label" optionValue="value" placeholder="Tipo Pagamento" class="w-full" />
                            </div>
                        </div>
                        <template #footer>
                            <Button label="Cancelar" icon="pi pi-times" text @click="showFiltersModal = false" />
                            <Button label="Limpar Filtros" icon="pi pi-refresh" severity="secondary" @click="resetFilters" />
                            <Button label="Aplicar" icon="pi pi-check" @click="applyFilters" />
                        </template>
                    </Dialog>

                    <div class="hidden md:block">
                        <div v-if="bookingStore.loading" class="text-center py-8">
                            <ProgressSpinner />
                        </div>
                        <div v-else-if="bookingStore.bookings.length === 0" class="text-center py-8 text-gray-500">
                            <i class="pi pi-calendar-plus text-4xl mb-4 text-gray-400"></i>
                            <p class="mb-4">Nenhuma reserva encontrada</p>
                            <p class="text-sm text-600 mb-4">Para fazer uma reserva, navegue até a seção de arenas disponíveis e selecione o horário desejado.</p>
                            <router-link to="/" class="inline-flex items-center px-4 py-2 border border-primary-500 text-primary-500 bg-transparent rounded hover:bg-primary-500 hover:text-white transition">
                                <i class="pi pi-plus mr-2"></i>
                                Alugar Arena
                            </router-link>
                        </div>
                        <DataTable
                            v-else
                            ref="dt"
                            :value="bookingStore.bookings || []"
                            dataKey="id"
                            :paginator="bookingStore.bookings.length > 0 || true"
                            :rows="rowsPerPage"
                            :totalRecords="bookingStore.pagination?.total || 0"
                            :loading="bookingStore.loading"
                            paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                            :rowsPerPageOptions="[5, 10, 25, 50]"
                            currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} reservas"
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
                                    <h4 class="m-0">Minhas Reservas</h4>
                                    <div class="flex gap-2">
                                        <IconField>
                                            <InputIcon>
                                                <i class="pi pi-search" />
                                            </InputIcon>
                                            <InputText v-model="searchTerm" placeholder="Buscar reservas..." @keyup.enter="onSearch" />
                                        </IconField>
                                        <Button icon="pi pi-search" severity="secondary" @click="onSearch" :loading="bookingStore.loading" />
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
                                    {{ slotProps.data.startTime }}
                                </template>
                            </Column>

                            <Column field="end_time" header="Horário Fim" sortable>
                                <template #body="slotProps">
                                    {{ slotProps.data.endTime }}
                                </template>
                            </Column>

                            <Column field="duration_minutes" header="Duração" sortable>
                                <template #body="slotProps">
                                    {{ slotProps.data.durationMinutes }}
                                </template>
                            </Column>

                            <Column field="total_amount" header="Valor" sortable>
                                <template #body="slotProps">
                                    {{ slotProps.data.totalAmount }}
                                </template>
                            </Column>

                            <Column field="booking_status" header="Status" sortable>
                                <template #body="slotProps">
                                    <Tag :value="getStatusLabel(slotProps.data.bookingStatus)" :severity="getStatusSeverity(slotProps.data.bookingStatus)" />
                                </template>
                            </Column>

                            <Column field="field_id" header="Campo">
                                <template #body="slotProps">
                                    {{ slotProps.data.field?.name }}
                                </template>
                            </Column>

                            <Column field="user_id" header="Usuário" sortable style="min-width: 10rem">
                                <template #body="slotProps">
                                    {{ slotProps.data.user?.name || '-' }}
                                </template>
                            </Column>

                            <Column header="Ações">
                                <template #body="slotProps">
                                    <Button icon="pi pi-eye" class="p-button-rounded p-button-primary p-button-text" @click="openViewBooking(slotProps.data)" v-tooltip.top="'Visualizar'" />
                                    <Button icon="pi pi-download" class="p-button-rounded p-button-primary p-button-text" @click="downloadReceipt(slotProps.data)" v-tooltip.top="'Baixar Recibo'" />
                                </template>
                            </Column>
                        </DataTable>

                        <div v-if="(bookingStore.bookings.length === 0 && !bookingStore.loading) || bookingStore.pagination?.total === 0" class="text-center mt-4">Mostrando 0 a 0 de 0 reservas</div>
                    </div>

                    <div class="block md:hidden">
                        <div v-if="bookingStore.loading" class="text-center py-8">
                            <ProgressSpinner />
                        </div>

                        <div v-else-if="bookingStore.bookings.length === 0" class="text-center py-8 text-gray-500">
                            <i class="pi pi-calendar-plus text-4xl mb-4 text-gray-400"></i>
                            <p class="mb-4">Nenhuma reserva encontrada</p>
                            <p class="text-sm text-600 mb-4">Para fazer uma reserva, navegue até a seção de arenas disponíveis e selecione o horário desejado.</p>
                            <router-link to="/" class="inline-flex items-center px-4 py-2 border border-primary-500 text-primary-500 bg-transparent rounded hover:bg-primary-500 hover:text-white transition">
                                <i class="pi pi-plus mr-2"></i>
                                Alugar Arena
                            </router-link>
                        </div>

                        <div v-else class="space-y-4">
                            <Card v-for="booking in bookingStore.bookings" :key="booking.id" class="h-full flex flex-col rounded-none sm:rounded-xl border border-gray-200 dark:border-gray-700">
                                <template #content>
                                    <div class="p-4 space-y-4 flex-grow">
                                        <div class="flex justify-between items-start mb-3">
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <Tag :value="getStatusLabel(booking.bookingStatus)" :severity="getStatusSeverity(booking.bookingStatus)" />
                                                    <p class="text-lg text-gray-600 dark:text-gray-400">{{ booking.field.name }}</p>
                                                </div>
                                                <h3 class="font-semibold text-lg text-gray-900 dark:text-gray-100">{{ booking.bookingNumber }}</h3>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-2 gap-4 mb-4">
                                            <div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Data</p>
                                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ booking.bookingDate }}</p>
                                            </div>

                                            <div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Horário</p>
                                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ booking.startTime }} - {{ booking.endTime }}</p>
                                            </div>

                                            <div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Duração</p>
                                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ booking.durationMinutes }}</p>
                                            </div>

                                            <div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Valor</p>
                                                <p class="font-medium text-green-600 dark:text-green-400">{{ formatCurrency(booking.totalAmount) }}</p>
                                            </div>
                                        </div>

                                        <div class="flex flex-col sm:flex-row justify-between items-center pt-3 border-t border-gray-100 dark:border-gray-700">
                                            <div class="text-sm text-gray-500 dark:text-gray-400 mb-2 sm:mb-0">{{ booking.paymentForm?.name }} • {{ booking.paymentForm?.type }}</div>

                                            <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                                                <Button label="Ver Detalhes" icon="pi pi-eye" size="small" outlined @click="openViewBooking(booking)" class="w-full sm:w-auto" />
                                                <Button label="Recibo" icon="pi pi-download" size="small" outlined severity="success" @click="downloadReceipt(booking)" class="w-full sm:w-auto" />
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </Card>
                        </div>

                        <div v-if="bookingStore.bookings.length > 0" class="mt-6 flex justify-center">
                            <Paginator
                                :first="(currentPage - 1) * rowsPerPage"
                                :rows="rowsPerPage"
                                :totalRecords="bookingStore.pagination?.total || 0"
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
