<script setup lang="ts">
import { onMounted, ref, computed, watch, reactive } from 'vue';
import { useBookingStore } from '@/stores/company/bookingStore';
import { useFieldStore } from '@/stores/company/fieldStore';
import { usePaymentFormStore } from '@/stores/company/select/paymentFormStore';
import type { BookingPayload } from '@/types/company/booking';
import type { PriceDetails, CalculatePricePayload } from '@/types/company/booking/priceDetails';
import type { AvailabilityResponse, AvailableSlot } from '@/types/company/booking/availability';
import type { Filters } from '@/types/company/booking/filters';
import type { SendBookingData } from '@/types/company/booking/sendBooking';
import type { Booking } from '@/types/company/booking';
import type { DataTableSortEvent, DataTablePageEvent } from 'primevue/datatable';
import { useToast } from 'primevue/usetoast';
import { formatLocalDate } from '@/utils/dateUtils';
import { useFormat } from '@/utils/useFormat';
import { formatPhone } from '@/utils/phoneFormatter';
import { useKeyboardShortcuts } from '@/utils/useKeyboardShortcuts';

const bookingStore = useBookingStore();
const fieldStore = useFieldStore();
const paymentFormStore = usePaymentFormStore();
const { formatCurrency } = useFormat();
const toast = useToast();

const searchTerm = ref('');
const rowsPerPage = ref(10);
const sortField = ref('id');
const sortOrder = ref<'asc' | 'desc'>('desc');
const currentPage = ref(1);

const bookingDialog = ref(false);
const submitted = ref(false);
const dt = ref<unknown>(null);
const showFiltersModal = ref(false);

const availableSlots = ref<AvailableSlot[]>([]);
const calculatedPrice = ref<number | null>(null);
const calculatedDetails = ref<PriceDetails | null>(null);
const timeSlots = ref<string[]>([]);
const includeExtraHour = ref(false);
const assignToUser = ref(false);
const userPhone = ref('');

const dialogState = reactive({
    sendBookingDialog: false,
    viewBookingDialog: false,
    statusEditDialog: false
});

const sendBookingState = reactive({
    selectedBooking: null as Booking | null,
    sendMethod: '' as 'email' | 'system' | 'whatsapp' | '',
    email: '',
    phone: ''
});

const viewBookingState = reactive({
    selectedBooking: null as Booking | null
});

const statusChangeState = reactive({
    selectedBooking: null as Booking | null,
    newStatus: '',
    oldStatus: '',
    cancellationReason: ''
});

const filtersObj = reactive({
    status: '',
    field: null as number | null,
    startDate: null as Date | null,
    endDate: null as Date | null,
    paymentType: ''
});

const fieldOptions = computed(() => {
    return [{ label: 'Todos', value: null }, ...(fieldStore.fields?.map((f) => ({ label: f.name, value: f.id })) || [])];
});

const filteredPaymentFormOptions = computed(() => {
    return paymentFormStore.paymentFormOptions;
});

const paymentTypeOptions = [
    { label: 'Todos', value: '' },
    { label: 'Presencial', value: 'presencial' },
    { label: 'Online', value: 'online' }
];

const statusOptions = [
    { label: 'Todos', value: '' },
    { label: 'Confirmado', value: 'confirmado' },
    { label: 'Pendente', value: 'pendente' },
    { label: 'Cancelado', value: 'cancelado' },
    { label: 'Concluído', value: 'concluido' }
];

useKeyboardShortcuts(openCreateBooking, saveBooking, bookingDialog, exportCSV);

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
        await Promise.all([loadBookings(), fieldStore.fetchFields(), paymentFormStore.fetchPaymentForms('presencial')]);
    } catch {
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: 'Erro ao carregar dados da página',
            life: 5000
        });
    }
});

async function loadBookings(): Promise<void> {
    const filters: Partial<Filters> = {
        search: searchTerm.value || undefined,
        sort: sortField.value,
        direction: sortOrder.value,
        perPage: rowsPerPage.value,
        page: currentPage.value,
        bookingDate: '',
        bookingStatus: filtersObj.status || undefined,
        fieldId: filtersObj.field || undefined,
        startDate: filtersObj.startDate ? formatLocalDate(filtersObj.startDate) : undefined,
        endDate: filtersObj.endDate ? formatLocalDate(filtersObj.endDate) : undefined,
        paymentType: filtersObj.paymentType || undefined
    };

    await bookingStore.fetchBookings(filters);
}

async function onSearch(): Promise<void> {
    currentPage.value = 1;
    await loadBookings();
}

async function onPageChange(event: DataTablePageEvent): Promise<void> {
    rowsPerPage.value = event.rows;
    currentPage.value = event.page + 1;
    await loadBookings();
}

async function onSort(event: DataTableSortEvent): Promise<void> {
    if (typeof event.sortField === 'string') {
        sortField.value = event.sortField || 'bookingDate';
        sortOrder.value = event.sortOrder === 1 ? 'asc' : 'desc';
        await loadBookings();
    }
}

async function onPaginatorPageChange(event: { page: number; first: number; rows: number }): Promise<void> {
    currentPage.value = event.page + 1;
    rowsPerPage.value = event.rows;
    await loadBookings();
}

async function checkAvailability(): Promise<void> {
    if (bookingStore.booking?.field?.id && bookingStore.booking?.bookingDate) {
        try {
            const dateParam = new Date(bookingStore.booking.bookingDate);
            const response = await bookingStore.getAvailability(bookingStore.booking.field.id, dateParam);
            availableSlots.value = response.availableSlots || [];
            generateTimeSlots();
            if (availableSlots.value.length === 0) {
                toast.add({ severity: 'info', summary: 'Informação', detail: 'Não há horários disponíveis para este dia.', life: 5000 });
                bookingStore.booking.startTime = '';
                bookingStore.booking.endTime = '';
            }
        } catch {
            toast.add({ severity: 'error', summary: 'Erro', detail: bookingStore.error, life: 3000 });
        }
    }
}

async function calculatePrice(): Promise<void> {
    if (bookingStore.booking?.field?.id && bookingStore.booking?.startTime && bookingStore.booking?.endTime) {
        try {
            const payload: CalculatePricePayload = {
                field_id: bookingStore.booking.field.id,
                start_time: bookingStore.booking.startTime,
                end_time: bookingStore.booking.endTime,
                include_extra_hour: includeExtraHour.value
            };
            const calculation = await bookingStore.calculatePrice(payload);
            calculatedDetails.value = {
                durationMinutes: calculation.durationMinutes,
                durationHours: calculation.durationHours,
                basePrice: calculation.basePrice,
                extraHourPrice: calculation.extraHourPrice,
                totalPrice: calculation.totalPrice,
                formattedBasePrice: formatCurrency(calculation.basePrice),
                formattedExtraHourPrice: formatCurrency(calculation.extraHourPrice),
                formattedTotalPrice: formatCurrency(calculation.totalPrice)
            };
            calculatedPrice.value = calculation.totalPrice;
        } catch {
            toast.add({ severity: 'error', summary: 'Erro', detail: bookingStore.error, life: 3000 });
        }
    }
}

async function saveBooking(): Promise<void> {
    submitted.value = true;

    if (bookingStore.booking?.field?.id && bookingStore.booking?.bookingDate && bookingStore.booking?.startTime && bookingStore.booking?.endTime && bookingStore.booking?.paymentType && bookingStore.booking?.paymentFormId) {
        if (assignToUser.value && !userPhone.value) {
            toast.add({ severity: 'warn', summary: 'Atenção', detail: 'Telefone é obrigatório quando atribuir a usuário.', life: 3000 });
            return;
        }

        try {
            const date = new Date(bookingStore.booking.bookingDate);
            const availability: AvailabilityResponse = await bookingStore.getAvailability(bookingStore.booking.field.id, date);
            const startTime = bookingStore.booking.startTime;
            const endTime = bookingStore.booking.endTime;

            const isAvailable = (() => {
                if (!availability.availableSlots || availability.availableSlots.length === 0) return false;

                const sortedSlots = availability.availableSlots.sort((a: AvailableSlot, b: AvailableSlot) => a.startTime.localeCompare(b.startTime));

                let currentTime = startTime;
                for (const slot of sortedSlots) {
                    if (slot.startTime === currentTime && slot.endTime <= endTime) {
                        currentTime = slot.endTime;
                        if (currentTime === endTime) return true;
                    }
                }
                return false;
            })();

            if (!isAvailable) {
                toast.add({ severity: 'error', summary: 'Erro', detail: 'Horário não disponível', life: 3000 });
                return;
            }

            const payload: BookingPayload = {
                field_id: bookingStore.booking.field.id,
                booking_date: date,
                start_time: bookingStore.booking.startTime,
                end_time: bookingStore.booking.endTime,
                payment_type: bookingStore.booking.paymentType as 'online' | 'presencial',
                payment_form_id: bookingStore.booking.paymentFormId,
                is_extra_hour: includeExtraHour.value,
                user_phone: assignToUser.value ? userPhone.value.replace(/\D/g, '') : undefined
            };

            const createdBooking = await bookingStore.createBooking(payload);

            if (assignToUser.value && userPhone.value) {
                try {
                    const sendData: SendBookingData = {
                        booking_id: createdBooking.data.id,
                        send_method: 'system',
                        email: '',
                        phone: userPhone.value.replace(/\D/g, '')
                    };
                    await bookingStore.sendBooking(sendData);
                    toast.add({ severity: 'success', summary: 'Sucesso', detail: 'Reserva criada e atribuída ao usuário com sucesso', life: 3000 });
                } catch {
                    toast.add({ severity: 'warn', summary: 'Atenção', detail: 'Reserva criada, mas erro ao atribuir ao usuário. Você pode tentar enviar novamente.', life: 5000 });
                }
            } else {
                toast.add({ severity: 'success', summary: 'Sucesso', detail: 'Reserva criada com sucesso', life: 3000 });
            }

            bookingDialog.value = false;
            bookingStore.clearBooking();
            availableSlots.value = [];
            calculatedPrice.value = null;
            assignToUser.value = false;
            userPhone.value = '';
            await loadBookings();
        } catch {
            toast.add({ severity: 'error', summary: 'Erro', detail: bookingStore.error, life: 5000 });
        }
    }
}

async function updateBookingStatus(bookingId: number, newStatus: string, cancellationReason?: string): Promise<void> {
    try {
        await bookingStore.updateBookingStatus(bookingId, newStatus as 'pendente' | 'confirmado' | 'cancelado' | 'concluido', cancellationReason);
        toast.add({
            severity: 'success',
            summary: 'Sucesso',
            detail: 'Status da reserva atualizado com sucesso',
            life: 3000
        });
    } catch {
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: bookingStore.error,
            life: 3000
        });
    }
}

async function sendBooking(): Promise<void> {
    submitted.value = true;

    if (!sendBookingState.sendMethod || !sendBookingState.selectedBooking) {
        return;
    }

    if (sendBookingState.sendMethod === 'email') {
        if (!sendBookingState.email) {
            toast.add({ severity: 'warn', summary: 'Atenção', detail: 'Endereço de email é obrigatório.', life: 3000 });
            return;
        }

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(sendBookingState.email)) {
            toast.add({ severity: 'warn', summary: 'Atenção', detail: 'Endereço de email inválido.', life: 3000 });
            return;
        }
    }

    if (sendBookingState.sendMethod === 'system' && !sendBookingState.phone) {
        toast.add({ severity: 'warn', summary: 'Atenção', detail: 'Telefone é obrigatório.', life: 3000 });
        return;
    }

    if (sendBookingState.sendMethod === 'whatsapp' && !sendBookingState.phone) {
        toast.add({ severity: 'warn', summary: 'Atenção', detail: 'Número do WhatsApp é obrigatório.', life: 3000 });
        return;
    }

    if (sendBookingState.sendMethod === 'whatsapp') {
        const booking = sendBookingState.selectedBooking!;
        const baseUrl = window.location.origin;
        const rachasUrl = `${baseUrl}/atleta/reservas`;

        let message = `Envio de Reserva\n\n`;
        message += `Número da Reserva: ${booking.bookingNumber}\n\n`;
        message += `Detalhes da Reserva\n`;
        message += `Status: ${getStatusLabel(booking.bookingStatus)}\n`;
        message += `Data: ${booking.bookingDate}\n`;
        message += `Horário Início: ${booking.startTime}\n`;
        message += `Horário Fim: ${booking.endTime}\n`;
        message += `Duração: ${booking.durationMinutes}\n`;
        message += `Arena: ${booking.field?.name}\n`;

        message += `Mais detalhes você pode ver pelo SeuRacha\n`;
        message += `Vá em "Minhas Reservas" através do link: ${rachasUrl}\n`;

        const phone = sendBookingState.phone.replace(/\D/g, '');
        const url = `https://wa.me/+55${phone}?text=${encodeURIComponent(message)}`;
        window.open(url, '_blank');

        toast.add({
            severity: 'success',
            summary: 'Sucesso',
            detail: 'WhatsApp aberto com a mensagem da reserva',
            life: 3000
        });

        hideSendBookingDialog();
        submitted.value = false;
        return;
    }

    try {
        const sendData: SendBookingData = {
            booking_id: sendBookingState.selectedBooking!.id,
            send_method: sendBookingState.sendMethod as 'email' | 'system',
            email: sendBookingState.email,
            phone: sendBookingState.phone.replace(/\D/g, '')
        };

        await bookingStore.sendBooking(sendData);

        toast.add({
            severity: 'success',
            summary: 'Sucesso',
            detail: `Reserva enviada com sucesso`,
            life: 3000
        });

        hideSendBookingDialog();
    } catch {
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: bookingStore.error,
            life: 3000
        });
    }

    submitted.value = false;
}

function openCreateBooking(): void {
    bookingStore.clearBooking();
    availableSlots.value = [];
    calculatedPrice.value = null;
    calculatedDetails.value = null;
    includeExtraHour.value = false;
    assignToUser.value = false;
    userPhone.value = '';
    submitted.value = false;
    bookingDialog.value = true;
    bookingStore.booking.paymentType = 'presencial';
}

function openStatusEditDialog(booking: Booking): void {
    if (booking.bookingStatus === 'cancelado') {
        toast.add({
            severity: 'warn',
            summary: 'Atenção',
            detail: 'Não é possível alterar o status de reservas canceladas.',
            life: 3000
        });
        return;
    }
    statusChangeState.selectedBooking = booking;
    statusChangeState.newStatus = booking.bookingStatus;
    statusChangeState.oldStatus = booking.bookingStatus;
    statusChangeState.cancellationReason = '';
    dialogState.statusEditDialog = true;
}

function openSendBookingDialog(bookingData: Booking): void {
    if (bookingData.bookingStatus === 'cancelado') {
        toast.add({
            severity: 'warn',
            summary: 'Atenção',
            detail: 'Não é possível enviar reservas canceladas.',
            life: 3000
        });
        return;
    }
    sendBookingState.selectedBooking = bookingData;
    sendBookingState.sendMethod = '';
    sendBookingState.email = '';
    sendBookingState.phone = '';
    dialogState.sendBookingDialog = true;
}

function openBookingDetails(bookingData: Booking): void {
    viewBookingState.selectedBooking = bookingData;
    dialogState.viewBookingDialog = true;
}

function confirmStatusUpdate(): void {
    if (statusChangeState.selectedBooking && statusChangeState.newStatus && statusChangeState.newStatus !== statusChangeState.oldStatus) {
        statusChangeState.selectedBooking.bookingStatus = statusChangeState.newStatus as 'pendente' | 'confirmado' | 'cancelado' | 'concluido';
        updateBookingStatus(statusChangeState.selectedBooking.id, statusChangeState.newStatus, statusChangeState.cancellationReason || undefined);
    }
    hideStatusEditDialog();
}

function hideDialog(): void {
    bookingDialog.value = false;
    submitted.value = false;
    availableSlots.value = [];
    calculatedPrice.value = null;
    calculatedDetails.value = null;
    includeExtraHour.value = false;
    assignToUser.value = false;
    userPhone.value = '';
}

function hideSendBookingDialog(): void {
    dialogState.sendBookingDialog = false;
    sendBookingState.selectedBooking = null;
    sendBookingState.sendMethod = '';
    sendBookingState.email = '';
    sendBookingState.phone = '';
}

function hideViewBookingDialog(): void {
    dialogState.viewBookingDialog = false;
    viewBookingState.selectedBooking = null;
}

function hideStatusEditDialog(): void {
    dialogState.statusEditDialog = false;
    statusChangeState.selectedBooking = null;
    statusChangeState.newStatus = '';
    statusChangeState.oldStatus = '';
    statusChangeState.cancellationReason = '';
}

function generateTimeSlots(): void {
    if (availableSlots.value.length > 0) {
        const startTimes = availableSlots.value.map((slot) => slot.startTime);
        const endTimes = availableSlots.value.map((slot) => slot.endTime);
        timeSlots.value = [...new Set([...startTimes, ...endTimes])].sort();
    } else {
        timeSlots.value = [];
    }
}

function exportCSV(): void {
    const data = bookingStore.bookings.map((booking) => ({
        Status: statusOptions.find((o) => o.value === booking.bookingStatus)?.label || booking.bookingStatus,
        'Reserva N°': booking.bookingNumber,
        Data: booking.bookingDate,
        'Horário Início': booking.startTime,
        'Horário Fim': booking.endTime,
        Duração: booking.durationMinutes,
        'Data de Criação': booking.createdAt,
        Arena: booking.field?.name || '',
        'Preço Total': formatCurrency(booking.totalAmount),
        'Valor por Hora': formatCurrency(parseFloat(booking.field?.pricePerHour || '0')),
        '30 Min Extra': booking.isExtraHour ? 'Sim' : 'Não',
        'Valor Extra 30 Min': booking.isExtraHour ? formatCurrency(parseFloat(booking.field?.extraHourPrice || '0')) : '-',
        Usuário: booking.user?.name || '-',
        Telefone: booking.user?.phone ? formatPhone(booking.user.phone) : '-',
        'Forma de Pagamento': booking.paymentForm?.name || '-',
        'Tipo de Pagamento': booking.paymentForm?.type ? booking.paymentForm.type.charAt(0).toUpperCase() + booking.paymentForm.type.slice(1) : '-',
        Tipo: booking.paymentType || '-',
        Cupom: booking.coupon?.code || '-',
        Observações: booking.notes || '-',
        'Motivo do Cancelamento': booking.cancellationFeason || '-'
    }));

    const headers = Object.keys(data[0]);
    const csvContent = [headers.join(','), ...data.map((row) => headers.map((header) => `"${row[header as keyof typeof row]}"`).join(','))].join('\n');

    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', 'reservas.csv');
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function getStatusSeverity(status: string): string {
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
}

function getStatusLabel(status: string): string {
    const statusMap: Record<string, string> = {
        pendente: 'Pendente',
        confirmado: 'Confirmado',
        cancelado: 'Cancelado',
        concluido: 'Concluído'
    };
    return statusMap[status] || status;
}

function setToday(): void {
    filtersObj.startDate = new Date();
    filtersObj.endDate = new Date();
}

function resetFilters(): void {
    filtersObj.status = '';
    filtersObj.field = null;
    filtersObj.startDate = null;
    filtersObj.endDate = null;
    filtersObj.paymentType = '';
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
                            <div class="hidden md:flex flex-wrap gap-2 items-center">
                                <Button label="Nova Reserva (F1)" icon="pi pi-plus" severity="secondary" class="mr-2" @click="openCreateBooking" v-tooltip.top="'Cadastrar nova reserva'" />
                                <DatePicker v-model="filtersObj.startDate" dateFormat="dd/mm/yy" placeholder="Data Início" class="mr-2" fluid style="width: 140px" v-tooltip.top="'Filtrar por data inicial da reserva'" />
                                <DatePicker v-model="filtersObj.endDate" dateFormat="dd/mm/yy" placeholder="Data Fim" class="mr-2" fluid style="width: 140px" v-tooltip.top="'Filtrar por data final da reserva'" />
                                <Button label="Hoje" @click="setToday" severity="secondary" class="mr-2" v-tooltip.top="'Definir filtros para data de hoje'" />
                                <Select v-model="filtersObj.field" :options="fieldOptions" optionLabel="label" optionValue="value" placeholder="Arena" class="mr-2" style="width: 140px" v-tooltip.top="'Filtrar por arena'" />
                                <Select v-model="filtersObj.status" :options="statusOptions" optionLabel="label" optionValue="value" placeholder="Status" class="mr-2" style="width: 140px" v-tooltip.top="'Filtrar por status da reserva'" />
                                <Select
                                    v-model="filtersObj.paymentType"
                                    :options="paymentTypeOptions"
                                    optionLabel="label"
                                    optionValue="value"
                                    placeholder="Tipo Pagamento"
                                    class="mr-2"
                                    style="width: 140px"
                                    v-tooltip.top="'Filtrar por tipo de pagamento'"
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

                    <div class="block md:hidden mb-6">
                        <div class="grid grid-cols-1 gap-4">
                            <Button label="Nova Reserva" icon="pi pi-plus" severity="secondary" @click="openCreateBooking" class="w-full" />
                        </div>
                        <div class="flex gap-2 mt-4">
                            <IconField class="flex-1">
                                <InputIcon>
                                    <i class="pi pi-search" />
                                </InputIcon>
                                <InputText v-model="searchTerm" placeholder="Buscar reserva..." @keyup.enter="onSearch" class="w-full" />
                            </IconField>
                            <Button icon="pi pi-search" severity="secondary" @click="onSearch" :loading="bookingStore.loading" />
                        </div>
                    </div>

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
                                <label for="field">Arena</label>
                                <Select v-model="filtersObj.field" :options="fieldOptions" optionLabel="label" optionValue="value" placeholder="Arena" class="w-full" />
                            </div>
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
                        <DataTable
                            ref="dt"
                            :value="bookingStore.bookings || []"
                            dataKey="id"
                            :paginator="true"
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
                                    <h4 class="m-0 flex items-center gap-2">
                                        <span class="bg-gray-100 text-gray-600 rounded-full w-8 h-8 flex items-center justify-center">
                                            <i class="pi pi-fw pi-calendar"></i>
                                        </span>
                                        Reservas
                                    </h4>
                                    <div class="flex gap-2">
                                        <IconField>
                                            <InputIcon>
                                                <i class="pi pi-search" />
                                            </InputIcon>
                                            <InputText v-model="searchTerm" placeholder="Buscar reservas..." @keyup.enter="onSearch" />
                                        </IconField>
                                        <Button icon="pi pi-search" severity="secondary" @click="onSearch" :loading="bookingStore.loading" v-tooltip.top="'Buscar'" />
                                    </div>
                                </div>
                            </template>

                            <Column field="booking_status" header="Status" sortable style="min-width: 6rem">
                                <template #body="slotProps">
                                    <Tag :value="getStatusLabel(slotProps.data.bookingStatus)" :severity="getStatusSeverity(slotProps.data.bookingStatus)" />
                                </template>
                            </Column>

                            <Column field="booking_number" header="Reserva N°" sortable style="min-width: 6rem">
                                <template #body="slotProps">
                                    {{ slotProps.data.bookingNumber }}
                                </template>
                            </Column>

                            <Column field="booking_date" header="Data" sortable style="min-width: 6rem">
                                <template #body="slotProps">
                                    {{ slotProps.data.bookingDate }}
                                </template>
                            </Column>

                            <Column field="start_time" header="Horário Início" sortable style="min-width: 6rem">
                                <template #body="slotProps">
                                    {{ slotProps.data.startTime }}
                                </template>
                            </Column>

                            <Column field="end_time" header="Horário Fim" sortable style="min-width: 6rem">
                                <template #body="slotProps">
                                    {{ slotProps.data.endTime }}
                                </template>
                            </Column>

                            <Column field="duration_minutes" header="Duração" sortable style="min-width: 6rem">
                                <template #body="slotProps">
                                    {{ slotProps.data.durationMinutes }}
                                </template>
                            </Column>

                            <Column field="field_id" header="Arena" sortable style="min-width: 6rem">
                                <template #body="slotProps">
                                    {{ slotProps.data.field?.name }}
                                </template>
                            </Column>

                            <Column field="total_amount" header="Preço Total" sortable style="min-width: 6rem">
                                <template #body="slotProps">
                                    {{ formatCurrency(slotProps.data.totalAmount) }}
                                </template>
                            </Column>

                            <Column field="user_id" header="Usuário" sortable style="min-width: 6rem">
                                <template #body="slotProps">
                                    {{ slotProps.data.user?.name || '-' }}
                                </template>
                            </Column>

                            <Column :exportable="false" style="min-width: 6rem">
                                <template #body="slotProps">
                                    <div class="flex gap-2 p-2">
                                        <Button
                                            icon="pi pi-arrow-right-arrow-left"
                                            v-tooltip.top="'Alterar Status'"
                                            rounded
                                            outlined
                                            severity="warning"
                                            @click="openStatusEditDialog(slotProps.data)"
                                            :disabled="slotProps.data.bookingStatus === 'cancelado' || slotProps.data.bookingStatus === 'concluido'"
                                        />
                                        <Button icon="pi pi-eye" v-tooltip.top="'Ver Detalhes'" rounded outlined severity="info" @click="openBookingDetails(slotProps.data)" />
                                        <Button icon="pi pi-send" v-tooltip.top="'Enviar Reserva'" rounded outlined severity="success" @click="openSendBookingDialog(slotProps.data)" :disabled="slotProps.data.status === 'cancelado'" />
                                    </div>
                                </template>
                            </Column>

                            <template #empty>
                                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                                    <i class="pi pi-fw pi-calendar text-4xl mb-2"></i>
                                    <p>Nenhuma reserva encontrada</p>
                                </div>
                            </template>
                        </DataTable>
                    </div>

                    <div class="block md:hidden">
                        <div v-if="bookingStore.loading" class="text-center py-8">
                            <ProgressSpinner />
                        </div>
                        <div v-else-if="bookingStore.bookings.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400">
                            <i class="pi pi-fw pi-calendar text-4xl mb-2"></i>
                            <p>Nenhuma reserva encontrada</p>
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
                                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ booking.durationMinutes }} min</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Valor</p>
                                                <p class="font-medium text-green-600 dark:text-green-400">{{ formatCurrency(booking.totalAmount) }}</p>
                                            </div>
                                        </div>

                                        <div class="flex flex-col gap-2 pt-3 border-t border-gray-100 dark:border-gray-700">
                                            <Button label="Ver Detalhes" size="small" outlined severity="info" class="w-full" @click="openBookingDetails(booking)" />
                                            <Button
                                                label="Alterar Status"
                                                size="small"
                                                outlined
                                                severity="warning"
                                                class="w-full"
                                                @click="openStatusEditDialog(booking)"
                                                :disabled="booking.bookingStatus === 'cancelado' || booking.bookingStatus === 'concluido'"
                                            />
                                            <Button label="Enviar Reserva" size="small" outlined severity="success" class="w-full" @click="openSendBookingDialog(booking)" :disabled="booking.bookingStatus === 'cancelado'" />
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

                <Dialog v-model:visible="bookingDialog" modal header="Detalhes da Reserva" :style="{ width: '50vw' }" :breakpoints="{ '960px': '75vw', '640px': '100vw' }" :maximizable="true">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="field_id" class="flex items-center justify-between font-bold mb-3">
                                <span>Arena <span class="text-red-500">*</span></span>
                                <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:inline" v-tooltip.top="'Arena a ser reservado'" />
                            </label>
                            <Select
                                id="field_id"
                                v-model="bookingStore.booking.field!.id"
                                :options="fieldStore.fields?.map((f: { name: any; id: any }) => ({ label: f.name, value: f.id })) || []"
                                optionLabel="label"
                                optionValue="value"
                                placeholder="Selecione a arena"
                                :invalid="submitted && !bookingStore.booking?.field?.id"
                                fluid
                            />
                            <small v-if="submitted && !bookingStore.booking?.field?.id" class="text-red-500">Arena é obrigatório.</small>
                        </div>

                        <div>
                            <label for="date" class="flex items-center justify-between font-bold mb-3">
                                <span>Data <span class="text-red-500">*</span></span>
                                <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:inline" v-tooltip.top="'Data da reserva (não pode ser no passado)'" />
                            </label>
                            <DatePicker
                                id="date"
                                v-model="bookingStore.booking.bookingDate"
                                dateFormat="dd/mm/yy"
                                :minDate="new Date()"
                                :invalid="submitted && !bookingStore.booking?.bookingDate"
                                :disabled="!bookingStore.booking?.field?.id"
                                placeholder="Selecione a data"
                                fluid
                                @date-select="checkAvailability"
                            />
                            <small v-if="submitted && !bookingStore.booking?.bookingDate" class="text-red-500">Data é obrigatória.</small>
                        </div>

                        <div>
                            <label for="start_time" class="flex items-center justify-between font-bold mb-3">
                                <span>Horário Início <span class="text-red-500">*</span></span>
                                <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:inline" v-tooltip.top="'Horário de início da reserva'" />
                            </label>
                            <Select
                                id="start_time"
                                v-model="bookingStore.booking.startTime"
                                :options="timeSlots?.map((slot: any) => ({ label: slot, value: slot })) || []"
                                optionLabel="label"
                                optionValue="value"
                                placeholder="Selecione o horário de início"
                                :invalid="submitted && !bookingStore.booking?.startTime"
                                fluid
                                @change="calculatePrice"
                            />
                            <small v-if="submitted && !bookingStore.booking?.startTime" class="text-red-500">Horário de início é obrigatório.</small>
                        </div>

                        <div>
                            <label for="end_time" class="flex items-center justify-between font-bold mb-3">
                                <span>Horário Fim <span class="text-red-500">*</span></span>
                                <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:inline" v-tooltip.top="'Horário de término da reserva'" />
                            </label>
                            <Select
                                id="end_time"
                                v-model="bookingStore.booking.endTime"
                                :options="timeSlots?.map((slot: any) => ({ label: slot, value: slot })) || []"
                                optionLabel="label"
                                optionValue="value"
                                placeholder="Selecione o horário de fim"
                                :invalid="submitted && !bookingStore.booking?.endTime"
                                fluid
                                @change="calculatePrice"
                            />
                            <small v-if="submitted && !bookingStore.booking?.endTime" class="text-red-500">Horário de fim é obrigatório.</small>
                        </div>

                        <div>
                            <label for="payment_type" class="flex items-center justify-between font-bold mb-3">
                                <span>Tipo de Pagamento <span class="text-red-500">*</span></span>
                                <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:inline" v-tooltip.top="'Local onde o pagamento será realizado (presencial ou online)'" />
                            </label>
                            <Select
                                id="payment_type"
                                v-model="bookingStore.booking.paymentType"
                                :options="paymentTypeOptions || []"
                                optionLabel="label"
                                optionValue="value"
                                placeholder="Selecione o tipo de pagamento"
                                :invalid="submitted && !bookingStore.booking?.paymentType"
                                fluid
                            />
                            <small v-if="submitted && !bookingStore.booking?.paymentType" class="text-red-500">Tipo de pagamento é obrigatório.</small>
                        </div>

                        <div>
                            <label for="payment_form_id" class="flex items-center justify-between font-bold mb-3">
                                <span>Forma de Pagamento <span class="text-red-500">*</span></span>
                                <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:inline" v-tooltip.top="'Método de pagamento (dinheiro, cartão, PIX, etc.)'" />
                            </label>
                            <Select
                                id="payment_form_id"
                                v-model="bookingStore.booking.paymentFormId"
                                :options="filteredPaymentFormOptions || []"
                                optionLabel="label"
                                optionValue="value"
                                placeholder="Selecione a forma de pagamento"
                                :invalid="submitted && !bookingStore.booking?.paymentFormId"
                                fluid
                            />
                            <small v-if="submitted && !bookingStore.booking?.paymentFormId" class="text-red-500">Forma de pagamento é obrigatória.</small>
                        </div>

                        <div>
                            <label for="assign_to_user" class="flex items-center justify-between font-bold mb-3">
                                <span>Atribuir a Usuário do Sistema</span>
                                <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:inline" v-tooltip.top="'Escolha se deseja atribuir a reserva a um usuário do sistema SeuRacha'" />
                            </label>
                            <Select
                                id="assign_to_user"
                                v-model="assignToUser"
                                :options="[
                                    { label: 'Não', value: false },
                                    { label: 'Sim', value: true }
                                ]"
                                optionLabel="label"
                                optionValue="value"
                                placeholder="Selecione uma opção"
                                fluid
                            />
                        </div>

                        <div v-if="assignToUser">
                            <label for="user_phone" class="flex items-center justify-between font-bold mb-3">
                                <span>Telefone do Usuário <span class="text-red-500">*</span></span>
                                <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:inline" v-tooltip.top="'Digite o telefone do usuário do sistema SeuRacha para atribuir a reserva.'" />
                            </label>
                            <InputMask id="user_phone" v-model="userPhone" mask="99 99999-9999" placeholder="00 00000-0000" :invalid="submitted && assignToUser && !userPhone" fluid type="tel" />
                            <small v-if="submitted && assignToUser && !userPhone" class="text-red-500">Telefone é obrigatório quando atribuir a usuário.</small>
                        </div>

                        <div class="col-span-1 md:col-span-2">
                            <label for="include_extra_hour" class="block font-bold mb-3">Incluir Hora Extra</label>
                            <ToggleSwitch v-model="includeExtraHour" @change="calculatePrice">
                                <template #handle="{ checked }">
                                    <i :class="['!text-xs pi', { 'pi-check': checked, 'pi-times': !checked }]" />
                                </template>
                            </ToggleSwitch>
                        </div>

                        <div class="col-span-1 md:col-span-2">
                            <label for="notes" class="block font-bold mb-3">Observações</label>
                            <Textarea id="notes" v-model="bookingStore.booking.notes" placeholder="Digite observações sobre a reserva" :autoResize="true" rows="3" fluid />
                        </div>

                        <div v-if="availableSlots.length > 0" class="col-span-1 md:col-span-2">
                            <label class="block font-bold mb-3">Horários Disponíveis</label>
                            <div class="flex flex-wrap gap-2">
                                <Tag v-for="slot in availableSlots" :key="slot.startTime" :value="`${slot.startTime} - ${slot.endTime}`" severity="info" />
                            </div>
                        </div>

                        <div v-if="calculatedDetails" class="col-span-1 md:col-span-2">
                            <label class="block font-semibold mb-4 text-lg text-gray-700 dark:text-gray-200"> Detalhes do Preço </label>

                            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="flex justify-between items-center px-4 py-3 rounded-lg bg-gray-50 dark:bg-gray-800">
                                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400"> Duração </span>
                                        <span class="text-base font-semibold text-gray-800 dark:text-gray-100"> {{ calculatedDetails.durationHours }}h ({{ calculatedDetails.durationMinutes }} min) </span>
                                    </div>

                                    <div class="flex justify-between items-center px-4 py-3 rounded-lg bg-gray-50 dark:bg-gray-800">
                                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400"> Preço Base </span>
                                        <span class="text-base font-semibold text-gray-800 dark:text-gray-100">
                                            {{ calculatedDetails.formattedBasePrice }}
                                        </span>
                                    </div>

                                    <div class="flex justify-between items-center px-4 py-3 rounded-lg bg-gray-50 dark:bg-gray-800">
                                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400"> Hora Extra </span>
                                        <span class="text-base font-semibold text-gray-800 dark:text-gray-100">
                                            {{ calculatedDetails.formattedExtraHourPrice }}
                                        </span>
                                    </div>

                                    <div class="flex justify-between items-center px-4 py-4 rounded-lg bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800">
                                        <span class="text-base font-semibold text-blue-700 dark:text-blue-300"> Total </span>
                                        <span class="text-xl font-bold text-blue-700 dark:text-blue-200">
                                            {{ calculatedDetails.formattedTotalPrice }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <template #footer>
                        <Button label="Cancelar" icon="pi pi-times" text @click="hideDialog" />
                        <Button label="Salvar" icon="pi pi-check" @click="saveBooking" />
                    </template>
                </Dialog>
            </div>

            <Dialog v-model:visible="dialogState.sendBookingDialog" modal header="Enviar Reserva" :style="{ width: '30vw' }" :breakpoints="{ '960px': '75vw', '640px': '100vw' }" :maximizable="true">
                <div class="space-y-4">
                    <div>
                        <label for="sendMethod" class="flex items-center justify-between text-900 font-medium mb-2">
                            <span>Método de Envio <span class="text-red-500">*</span></span>
                            <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:inline" v-tooltip.top="'Escolha como enviar a reserva: por email, para usuário do sistema ou WhatsApp'" />
                        </label>
                        <Select
                            id="sendMethod"
                            v-model="sendBookingState.sendMethod"
                            :options="[
                                { label: 'Email', value: 'email' },
                                { label: 'Sistema', value: 'system' },
                                { label: 'WhatsApp', value: 'whatsapp' }
                            ]"
                            optionLabel="label"
                            optionValue="value"
                            placeholder="Selecione o método de envio"
                            :invalid="submitted && !sendBookingState.sendMethod"
                            fluid
                        />
                        <small v-if="submitted && !sendBookingState.sendMethod" class="text-red-500">Método de envio é obrigatório.</small>
                    </div>

                    <div v-if="sendBookingState.sendMethod === 'email'">
                        <label for="email" class="flex items-center justify-between text-900 font-medium mb-2">
                            <span>Endereço de Email <span class="text-red-500">*</span></span>
                            <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:inline" v-tooltip.top="'Digite o endereço de email para onde a reserva será enviada'" />
                        </label>
                        <InputText id="email" type="email" v-model="sendBookingState.email" placeholder="Digite o endereço de email" :invalid="submitted && sendBookingState.sendMethod === 'email' && !sendBookingState.email" fluid />
                        <small v-if="submitted && sendBookingState.sendMethod === 'email' && !sendBookingState.email" class="text-red-500">Endereço de email é obrigatório.</small>
                    </div>

                    <div v-if="sendBookingState.sendMethod === 'system'">
                        <label for="phone" class="flex items-center justify-between text-900 font-medium mb-2">
                            <span>Telefone do Usuário <span class="text-red-500">*</span></span>
                            <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:inline" v-tooltip.top="'Digite o telefone do usuário do sistema SeuRacha para enviar e atribuir a comanda.'" />
                        </label>
                        <InputMask id="phone" v-model="sendBookingState.phone" mask="99 99999-9999" placeholder="00 00000-0000" class="w-full mb-4" type="tel" />
                        <div class="flex flex-col items-center mt-4 text-center border border-orange-400 dark:border-orange-500 bg-orange-50 dark:bg-orange-900/40 rounded-lg p-4 shadow-sm">
                            <i class="pi pi-exclamation-triangle text-orange-600 dark:text-orange-400 text-5xl mb-2"></i>
                            <span class="text-orange-700 dark:text-orange-300 text-base font-medium">
                                <strong>Atenção:</strong> Ao enviar pelo sistema, o usuário será automaticamente definido como responsável pelo racha e receberá um e-mail de confirmação.<br />
                                Envios por <b>e-mail</b> ou <b>WhatsApp</b> não atribuem responsabilidade ao usuário.
                            </span>
                        </div>
                    </div>

                    <div v-if="sendBookingState.sendMethod === 'whatsapp'">
                        <label for="whatsappPhone" class="flex items-center justify-between text-900 font-medium mb-2">
                            <span>Número do WhatsApp <span class="text-red-500">*</span></span>
                            <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:inline" v-tooltip.top="'Digite o número do WhatsApp para onde a reserva será enviada'" />
                        </label>
                        <InputMask id="whatsappPhone" v-model="sendBookingState.phone" mask="99 99999-9999" placeholder="00 00000-0000" :invalid="submitted && sendBookingState.sendMethod === 'whatsapp' && !sendBookingState.phone" fluid type="tel" />
                        <small v-if="submitted && sendBookingState.sendMethod === 'whatsapp' && !sendBookingState.phone" class="text-red-500">Número do WhatsApp é obrigatório.</small>
                    </div>
                </div>

                <template #footer>
                    <Button label="Cancelar" icon="pi pi-times" text @click="hideSendBookingDialog" />
                    <Button
                        label="Enviar"
                        icon="pi pi-send"
                        @click="sendBooking"
                        :disabled="
                            !sendBookingState.sendMethod ||
                            (sendBookingState.sendMethod === 'email' && !sendBookingState.email) ||
                            (sendBookingState.sendMethod === 'system' && !sendBookingState.phone) ||
                            (sendBookingState.sendMethod === 'whatsapp' && !sendBookingState.phone)
                        "
                    />
                </template>
            </Dialog>

            <Dialog v-model:visible="dialogState.viewBookingDialog" modal header="Detalhes da Reserva" :style="{ width: '50vw' }" :breakpoints="{ '960px': '75vw', '640px': '100vw' }" :maximizable="true">
                <div v-if="viewBookingState.selectedBooking" class="space-y-6">
                    <div class="p-4 rounded-lg border dark:border-gray-700">
                        <div class="flex items-center mb-3">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200"><i class="pi pi-info-circle text-primary dark:text-primary mr-2"></i> Informações da Reserva</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-white dark:bg-gray-800 p-3 rounded-md shadow-sm dark:shadow-lg">
                                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Número da Reserva</label>
                                <p class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ viewBookingState.selectedBooking.bookingNumber }}</p>
                            </div>
                            <div class="bg-white dark:bg-gray-800 p-3 rounded-md shadow-sm dark:shadow-lg">
                                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Data da Reserva</label>
                                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ viewBookingState.selectedBooking.bookingDate }}</p>
                            </div>
                            <div class="bg-white dark:bg-gray-800 p-3 rounded-md shadow-sm dark:shadow-lg">
                                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Status</label>
                                <div class="mt-1">
                                    <Tag :value="getStatusLabel(viewBookingState.selectedBooking.bookingStatus)" :severity="getStatusSeverity(viewBookingState.selectedBooking.bookingStatus)" class="text-sm" />
                                </div>
                            </div>
                            <div class="bg-white dark:bg-gray-800 p-3 rounded-md shadow-sm dark:shadow-lg md:col-span-3">
                                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Observações</label>
                                <p class="text-base text-gray-900 dark:text-gray-100">{{ viewBookingState.selectedBooking.notes || '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 rounded-lg border dark:border-gray-700">
                        <div class="flex items-center mb-3">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200"><i class="pi pi-clock text-primary dark:primary mr-2"></i> Horários e Duração</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="bg-white dark:bg-gray-800 p-3 rounded-md shadow-sm dark:shadow-lg">
                                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Horário Início</label>
                                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ viewBookingState.selectedBooking.startTime }}</p>
                            </div>
                            <div class="bg-white dark:bg-gray-800 p-3 rounded-md shadow-sm dark:shadow-lg">
                                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Horário Fim</label>
                                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ viewBookingState.selectedBooking.endTime }}</p>
                            </div>
                            <div class="bg-white dark:bg-gray-800 p-3 rounded-md shadow-sm dark:shadow-lg">
                                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Duração</label>
                                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ viewBookingState.selectedBooking.durationMinutes }}</p>
                            </div>
                            <div class="bg-white dark:bg-gray-800 p-3 rounded-md shadow-sm dark:shadow-lg">
                                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Data de Criação</label>
                                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ viewBookingState.selectedBooking.createdAt }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 rounded-lg border dark:border-gray-700">
                        <div class="flex items-center mb-3">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200"><i class="pi pi-map-marker text-primary dark:primary mr-2"></i> Arena e Participante</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-white dark:bg-gray-800 p-3 rounded-md shadow-sm dark:shadow-lg">
                                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Arena</label>
                                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ viewBookingState.selectedBooking.field?.name }}</p>
                            </div>
                            <div class="bg-white dark:bg-gray-800 p-3 rounded-md shadow-sm dark:shadow-lg">
                                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Usuário</label>
                                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ viewBookingState.selectedBooking.user?.name || '-' }}</p>
                            </div>
                            <div class="bg-white dark:bg-gray-800 p-3 rounded-md shadow-sm dark:shadow-lg">
                                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Telefone</label>
                                <div class="flex items-center space-x-2">
                                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ viewBookingState.selectedBooking.user?.phone ? formatPhone(viewBookingState.selectedBooking.user.phone) : '-' }}</p>
                                    <a
                                        v-if="viewBookingState.selectedBooking.user?.phone"
                                        :href="`https://wa.me/55${viewBookingState.selectedBooking.user.phone.replace(/\D/g, '')}`"
                                        target="_blank"
                                        class="text-green-500 hover:text-green-600 transition-colors"
                                    >
                                        <i class="pi pi-whatsapp text-xl"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 rounded-lg border dark:border-gray-700">
                        <div class="flex items-center mb-3">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200"><i class="pi pi-credit-card text-primary dark:primary mr-2"></i> Informações de Pagamento</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-white dark:bg-gray-800 p-3 rounded-md shadow-sm dark:shadow-lg">
                                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Preço Total</label>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">30 Min Extra: {{ viewBookingState.selectedBooking.isExtraHour ? 'Sim' : 'Não' }}</p>
                                <div v-if="viewBookingState.selectedBooking.isExtraHour" class="mb-2">
                                    <p class="text-sm text-gray-700 dark:text-gray-300">
                                        Valor por hora: <strong>{{ formatCurrency(parseFloat(viewBookingState.selectedBooking.field?.pricePerHour)) }}</strong>
                                    </p>
                                    <p class="text-sm text-gray-700 dark:text-gray-300">
                                        + 30 Min Extra: <strong>{{ formatCurrency(parseFloat(viewBookingState.selectedBooking.field?.extraHourPrice)) }}</strong>
                                    </p>
                                </div>
                                <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ formatCurrency(viewBookingState.selectedBooking.totalAmount) }}</p>
                            </div>
                            <div class="bg-white dark:bg-gray-800 p-3 rounded-md shadow-sm dark:shadow-lg">
                                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Forma de Pagamento</label>
                                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ viewBookingState.selectedBooking.paymentForm?.name }}</p>
                            </div>
                            <div class="bg-white dark:bg-gray-800 p-3 rounded-md shadow-sm dark:shadow-lg">
                                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Tipo</label>
                                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    {{ viewBookingState.selectedBooking.paymentForm?.type ? viewBookingState.selectedBooking.paymentForm.type.charAt(0).toUpperCase() + viewBookingState.selectedBooking.paymentForm.type.slice(1) : '' }}
                                </p>
                            </div>
                        </div>
                        <div v-if="viewBookingState.selectedBooking.coupon" class="mt-4 bg-white dark:bg-gray-800 p-3 rounded-md shadow-sm dark:shadow-lg">
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Cupom Utilizado</label>
                            <p class="text-lg font-semibold text-primary dark:primary">{{ viewBookingState.selectedBooking.coupon }}</p>
                        </div>
                    </div>

                    <div v-if="viewBookingState.selectedBooking.bookingStatus === 'cancelado' && viewBookingState.selectedBooking.cancellationFeason" class="p-4 rounded-lg border dark:border-gray-700">
                        <div class="flex items-center mb-3">
                            <h3 class="text-lg font-semibold text-red-800 dark:text-red-200">
                                <i class="pi pi-exclamation-triangle text-red-600 dark:text-red-400 mr-2"></i>
                                Motivo do Cancelamento
                            </h3>
                        </div>
                        <div class="bg-white dark:bg-gray-800 p-3 rounded-md shadow-sm dark:shadow-lg">
                            <p class="text-gray-900 dark:text-gray-100">{{ viewBookingState.selectedBooking.cancellationFeason }}</p>
                        </div>
                    </div>
                </div>

                <template #footer>
                    <Button label="Fechar" icon="pi pi-times" @click="hideViewBookingDialog" class="p-button-secondary" />
                </template>
            </Dialog>

            <Dialog v-model:visible="dialogState.statusEditDialog" modal header="Alterar Status da Reserva" :style="{ width: '30vw' }" :breakpoints="{ '960px': '75vw', '640px': '100vw' }" :maximizable="true">
                <div class="flex flex-col items-center gap-4 mb-4 text-center">
                    <i class="pi pi-info-circle text-green-500 text-8xl" />
                    <span class="text-gray-700 dark:text-gray-200">
                        Você está prestes a alterar o status da reserva
                        <b class="font-semibold">{{ statusChangeState.selectedBooking?.bookingNumber }}</b
                        >. Selecione abaixo o novo status que deseja aplicar.
                    </span>
                </div>

                <div class="p-field">
                    <label for="statusSelect" class="flex items-center justify-between text-900 font-medium mb-2">
                        <span>Status <span class="text-red-500">*</span></span>
                        <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:inline" v-tooltip.top="'Selecione o novo status da reserva'" />
                    </label>

                    <Select id="statusSelect" v-model="statusChangeState.newStatus" :options="statusOptions.filter((option: { value: string }) => option.value !== '')" optionLabel="label" optionValue="value" placeholder="Selecione o status" fluid>
                        <template #value="{ value }">
                            {{ statusOptions.find((option: { value: any }) => option.value === value)?.label || value }}
                        </template>
                        <template #option="{ option }">
                            {{ option.label }}
                        </template>
                    </Select>
                </div>
                <div v-if="statusChangeState.newStatus === 'cancelado'" class="p-field mt-6">
                    <label for="cancellationReason" class="flex items-center justify-between text-900 font-medium mb-2">
                        <span>Razão de Cancelamento <span class="text-red-500">*</span></span>
                        <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:inline" v-tooltip.top="'Informe a razão do cancelamento'" />
                    </label>
                    <Textarea id="cancellationReason" v-model="statusChangeState.cancellationReason" placeholder="Digite a razão do cancelamento" :autoResize="true" rows="3" fluid />
                </div>
                <template #footer>
                    <Button label="Cancelar" icon="pi pi-times" text @click="hideStatusEditDialog" />
                    <Button
                        label="Salvar"
                        icon="pi pi-check"
                        @click="confirmStatusUpdate"
                        :disabled="statusChangeState.newStatus === statusChangeState.oldStatus || (statusChangeState.newStatus === 'cancelado' && !statusChangeState.cancellationReason.trim())"
                    />
                </template>
            </Dialog>
        </div>
    </div>
</template>
