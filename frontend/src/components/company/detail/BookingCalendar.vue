<script lang="ts" setup>
import { ref, computed } from 'vue';
import type { SimpleBooking } from '@/types/company/booking';
import Card from 'primevue/card';
import Button from 'primevue/button';
import ProgressSpinner from 'primevue/progressspinner';
import Dialog from 'primevue/dialog';
import Tag from 'primevue/tag';

const props = defineProps<{
    bookings?: SimpleBooking[];
    loading?: boolean;
    currentDate: Date;
}>();

const emit = defineEmits<{
    'update:currentDate': [date: Date];
}>();

const daysOfWeek = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
const showModal = ref(false);
const selectedDay = ref<{ date: Date; bookings: SimpleBooking[] } | null>(null);

const calendarDays = computed(() => {
    const year = props.currentDate.getFullYear();
    const month = props.currentDate.getMonth();

    const firstDayOfMonth = new Date(year, month, 1);
    const lastDayOfMonth = new Date(year, month + 1, 0);

    const startDate = new Date(firstDayOfMonth);
    startDate.setDate(startDate.getDate() - startDate.getDay());

    const endDate = new Date(lastDayOfMonth);
    const daysToAdd = 6 - endDate.getDay();
    endDate.setDate(endDate.getDate() + daysToAdd);

    const days = [];
    const currentDateIter = new Date(startDate);
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    while (currentDateIter <= endDate) {
        const dayBookings = getBookingsForDay(currentDateIter);
        const isCurrentMonth = currentDateIter.getMonth() === month;
        const isToday = currentDateIter.getTime() === today.getTime();

        days.push({
            date: new Date(currentDateIter),
            isCurrentMonth,
            isToday,
            bookings: dayBookings
        });

        currentDateIter.setDate(currentDateIter.getDate() + 1);
    }

    return days;
});

const formatMonthYear = (date: Date): string => {
    return date.toLocaleDateString('pt-BR', { month: 'long', year: 'numeric' });
};

const getBookingsForDay = (date: Date) => {
    const dateString = date.toISOString().split('T')[0];

    return (props.bookings || []).filter((booking) => {
        return booking.bookingDate === dateString;
    });
};

const getBookingStatusClass = (status: string): string => {
    switch (status.toLowerCase()) {
        case 'confirmado':
            return 'booking-status-confirmed';
        case 'pendente':
            return 'booking-status-pending';
        case 'concluido':
            return 'booking-status-completed';
        case 'cancelado':
            return 'booking-status-cancelled';
        default:
            return 'booking-status-default';
    }
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

const statusCounts = computed(() => {
    const counts: Record<string, number> = {
        confirmado: 0,
        pendente: 0,
        concluido: 0,
        cancelado: 0
    };
    (props.bookings || []).forEach((booking) => {
        const status = booking.bookingStatus.toLowerCase();
        if (status in counts) {
            counts[status]++;
        }
    });
    return counts;
});

const previousMonth = (): void => {
    if (props.loading) return;

    const newDate = new Date(props.currentDate);
    newDate.setMonth(newDate.getMonth() - 1);
    emit('update:currentDate', newDate);
};

const nextMonth = (): void => {
    if (props.loading) return;

    const newDate = new Date(props.currentDate);
    newDate.setMonth(newDate.getMonth() + 1);
    emit('update:currentDate', newDate);
};

const openDayModal = (day: { date: Date; bookings: SimpleBooking[] }): void => {
    selectedDay.value = day;
    showModal.value = true;
};
</script>

<template>
    <Card class="booking-calendar-card group hover:shadow-lg transition-all duration-300 border border-gray-200 dark:border-gray-700">
        <template #header>
            <div class="p-4 pb-0 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                    <span class="bg-green-100 text-green-600 rounded-full w-8 h-8 flex items-center justify-center">
                        <i class="pi pi-calendar"></i>
                    </span>
                    Calendário de Reservas
                </h3>
                <div class="flex items-center gap-2">
                    <Button icon="pi pi-chevron-left" class="p-button-text p-button-sm" @click="previousMonth" :disabled="loading" />
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400 min-w-[120px] text-center">
                        {{ formatMonthYear(props.currentDate).charAt(0).toUpperCase() + formatMonthYear(props.currentDate).slice(1) }}
                    </span>
                    <Button icon="pi pi-chevron-right" class="p-button-text p-button-sm" @click="nextMonth" :disabled="loading" />
                </div>
            </div>
        </template>

        <template #content>
            <div class="px-4 pb-4 bg-white dark:bg-gray-900">
                <div v-if="loading" class="flex items-center justify-center py-8">
                    <ProgressSpinner strokeWidth="4" class="w-8 h-8" />
                </div>

                <div v-else>
                    <div class="block md:hidden text-center py-4 text-sm text-gray-600 dark:text-gray-400">
                        <i class="pi pi-mobile mr-2 text-gray-600 dark:text-gray-400"></i>
                        Para uma melhor visualização, vire o celular na horizontal
                    </div>
                    <div class="calendar-container">
                        <div class="grid grid-cols-7 gap-1 mb-2">
                            <div v-for="day in daysOfWeek" :key="day" class="text-center text-xs font-semibold text-gray-500 dark:text-gray-400 py-2">
                                {{ day }}
                            </div>
                        </div>

                        <div class="grid grid-cols-7 gap-1">
                            <div
                                v-for="(day, index) in calendarDays"
                                :key="index"
                                class="calendar-day-cell"
                                :class="{
                                    'other-month': !day.isCurrentMonth,
                                    today: day.isToday,
                                    'has-bookings': day.bookings.length > 0,
                                    clickable: day.bookings.length > 0
                                }"
                                @click="day.bookings.length > 0 && openDayModal(day)"
                            >
                                <div class="day-number">{{ day.date.getDate() }}</div>

                                <div v-if="day.bookings.length > 0" class="booking-indicators">
                                    <div
                                        v-for="booking in day.bookings.slice(0, 3)"
                                        :key="booking.id"
                                        class="booking-indicator"
                                        :class="getBookingStatusClass(booking.bookingStatus)"
                                        :title="`${booking.fieldName} - ${booking.startTime} às ${booking.endTime}`"
                                    >
                                        <span class="booking-time"><i class="pi pi-clock mr-1" style="font-size: 0.7rem"></i>{{ booking.startTime }} - {{ booking.endTime }}</span>
                                    </div>
                                    <div v-if="day.bookings.length > 3" class="booking-more" :title="`+${day.bookings.length - 3} reservas`">+{{ day.bookings.length - 3 }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-center mt-6">
                    <div class="booking-legend flex gap-6 text-xs">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded bg-green-500"></div>
                            <span class="text-gray-600 dark:text-gray-400">Confirmado ({{ statusCounts.confirmado }})</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded bg-yellow-500"></div>
                            <span class="text-gray-600 dark:text-gray-400">Pendente ({{ statusCounts.pendente }})</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded bg-blue-500"></div>
                            <span class="text-gray-600 dark:text-gray-400">Concluído ({{ statusCounts.concluido }})</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded bg-red-500"></div>
                            <span class="text-gray-600 dark:text-gray-400">Cancelado ({{ statusCounts.cancelado }})</span>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </Card>

    <Dialog v-model:visible="showModal" modal header="Reservas do Dia" :style="{ width: '50vw' }" :breakpoints="{ '960px': '75vw', '640px': '100vw' }" :maximizable="true">
        <div v-if="selectedDay">
            <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-100">
                {{ selectedDay.date.toLocaleDateString('pt-BR') }} - {{ selectedDay.date.toLocaleDateString('pt-BR', { weekday: 'long' }) }} | {{ new Date().toLocaleTimeString('pt-BR') }}
            </h3>
            <div class="space-y-2">
                <div v-for="booking in selectedDay.bookings" :key="booking.id" class="p-4 bg-white border border-gray-300 rounded-lg shadow-sm">
                    <div class="flex items-center mb-2">
                        <i class="pi pi-building mr-2 text-black" style="font-size: 1.2rem; color: var(--primary-color)"></i>
                        <div class="font-semibold text-lg text-black">{{ booking.fieldName }}</div>
                        <Tag :value="booking.bookingStatus" :severity="getStatusSeverity(booking.bookingStatus)" class="text-sm ml-2" />
                    </div>
                    <div class="flex items-center mb-2">
                        <i class="pi pi-clock mr-2 text-black" style="font-size: 1rem; color: var(--primary-color)"></i>
                        <div class="text-sm text-black">{{ booking.startTime }} - {{ booking.endTime }}</div>
                    </div>
                    <div class="flex items-center mb-2">
                        <i class="pi pi-user mr-2 text-black" style="font-size: 1rem; color: var(--primary-color)"></i>
                        <div class="text-sm text-black">{{ booking.userName }}</div>
                    </div>
                    <div class="flex items-center mb-2">
                        <i class="pi pi-dollar mr-2 text-black" style="font-size: 1rem; color: var(--primary-color)"></i>
                        <div class="text-sm text-black">R$ {{ booking.totalAmount }}</div>
                    </div>
                </div>
            </div>
        </div>
    </Dialog>
</template>

<style scoped>
.calendar-container {
    width: 100%;
}

.calendar-day-cell {
    position: relative;
    min-height: 80px;
    padding: 8px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    transition: all 0.2s;
    background: white;
}

.dark .calendar-day-cell {
    background: #1f2937;
    border-color: #374151;
}

.calendar-day-cell:hover {
    background-color: #f9fafb;
}

.dark .calendar-day-cell:hover {
    background-color: #1f2937;
}

.calendar-day-cell.other-month {
    opacity: 0.3;
}

.calendar-day-cell.today {
    border: 2px solid #3b82f6;
    background-color: #eff6ff;
}

.dark .calendar-day-cell.today {
    background-color: rgba(59, 130, 246, 0.2);
}

.calendar-day-cell.has-bookings {
    background: linear-gradient(135deg, #eff6ff 0%, #e0e7ff 100%);
}

.dark .calendar-day-cell.has-bookings {
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.2) 0%, rgba(99, 102, 241, 0.2) 100%);
}

.calendar-day-cell.clickable {
    cursor: pointer;
}

.calendar-day-cell.clickable:hover {
    transform: scale(1.02);
}

.day-number {
    font-size: 0.875rem;
    font-weight: 500;
    color: #374151;
    margin-bottom: 4px;
}

.dark .day-number {
    color: #d1d5db;
}

.calendar-day-cell.today .day-number {
    color: #1d4ed8;
    font-weight: 700;
}

.dark .calendar-day-cell.today .day-number {
    color: #93c5fd;
}

.booking-indicators {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.booking-indicator {
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 500;
    color: white;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    transition: all 0.2s;
    cursor: pointer;
}

.booking-indicator:hover {
    transform: scale(1.05);
}

.booking-status-confirmed {
    background-color: #10b981;
}

.booking-status-confirmed:hover {
    background-color: #059669;
}

.booking-status-pending {
    background-color: #f59e0b;
}

.booking-status-pending:hover {
    background-color: #d97706;
}

.booking-status-completed {
    background-color: #3b82f6;
}

.booking-status-completed:hover {
    background-color: #2563eb;
}

.booking-status-cancelled {
    background-color: #ef4444;
}

.booking-status-cancelled:hover {
    background-color: #dc2626;
}

.booking-status-default {
    background-color: #6b7280;
}

.booking-status-default:hover {
    background-color: #4b5563;
}

.booking-time {
    font-size: 0.75rem;
    font-weight: 700;
}

.booking-more {
    font-size: 0.75rem;
    color: #4b5563;
    background-color: #f3f4f6;
    padding: 2px 8px;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.2s;
}

.dark .booking-more {
    color: #9ca3af;
    background-color: #374151;
}

.booking-more:hover {
    background-color: #e5e7eb;
}

.dark .booking-more:hover {
    background-color: #4b5563;
}

@media (max-width: 768px) {
    .calendar-day-cell {
        min-height: 100px;
        padding: 4px;
    }

    .booking-indicator {
        padding: 1px 4px;
        font-size: 0.75rem;
    }

    .booking-time {
        display: none;
    }

    .booking-indicator::after {
        content: '•';
        color: white;
    }

    .booking-legend {
        flex-direction: column;
        gap: 4px;
    }
}
</style>
