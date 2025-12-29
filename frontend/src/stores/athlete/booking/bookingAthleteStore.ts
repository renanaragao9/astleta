import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import type { Booking } from '@/types/athlete/booking/bookingAthlete';
import type { BookingFilters } from '@/types/athlete/filters/bookingFilters';
import { BookingService } from '@/services/athlete/booking/BookingAthleteService';
import { errorMessageHttpResquestUtils } from '@/utils/errorMessageHttpResquestUtils';

export const useBookingStore = defineStore('athlete-booking', () => {
    const loading = ref(false);
    const error = ref<string | null>(null);
    const bookings = ref<Booking[]>([]);
    const booking = ref<Booking>({
        id: 0,
        bookingNumber: '',
        bookingDate: '',
        startTime: '',
        endTime: '',
        durationMinutes: '',
        totalAmount: 0,
        bookingStatus: 'pendente',
        createdAt: '',
        isExtraHour: false,
        field: {
            id: 0,
            name: '',
            pricePerHour: '0',
            extraHourPrice: '0'
        },
        user: {
            id: 0,
            name: ''
        },
        coupon: {
            id: 0,
            code: '',
            discountAmount: 0,
            discountType: ''
        },
        paymentForm: {
            id: 0,
            name: '',
            type: ''
        }
    });
    const pagination = ref({
        currentPage: 1,
        lastPage: 1,
        perPage: 15,
        total: 0
    });

    const hasBookings = computed(() => bookings.value.length > 0);

    async function getBookings(filters: Partial<BookingFilters> = {}) {
        loading.value = true;
        error.value = null;
        try {
            const response = await BookingService.getBookings(filters);

            bookings.value = response.data.map((b: Booking) => ({
                ...b,
                totalAmount: Number(b.totalAmount)
            }));
            pagination.value = {
                currentPage: response.meta.currentPage,
                lastPage: response.meta.lastPage,
                perPage: response.meta.perPage,
                total: response.meta.total
            };
        } catch (err: unknown) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao carregar reservas');
            bookings.value = [];
        } finally {
            loading.value = false;
        }
    }

    async function getBooking(id: number) {
        loading.value = true;
        error.value = null;
        try {
            const response = await BookingService.getBooking(id);
            booking.value = response.data;
            booking.value.totalAmount = Number(booking.value.totalAmount);
        } catch (err: unknown) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao carregar reserva');
        } finally {
            loading.value = false;
        }
    }

    async function downloadReceipt(id: number): Promise<Blob> {
        error.value = null;
        try {
            return await BookingService.downloadReceipt(id);
        } catch (err: unknown) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao baixar recibo');
            throw err;
        }
    }

    function clearError() {
        error.value = null;
    }

    function resetBooking() {
        booking.value = {
            id: 0,
            bookingNumber: '',
            bookingDate: '',
            startTime: '',
            endTime: '',
            durationMinutes: '',
            totalAmount: 0,
            bookingStatus: 'pendente',
            createdAt: '',
            isExtraHour: false,
            field: {
                id: 0,
                name: '',
                pricePerHour: '0',
                extraHourPrice: '0'
            },
            user: {
                id: 0,
                name: ''
            },
            coupon: {
                id: 0,
                code: '',
                discountAmount: 0,
                discountType: ''
            },
            paymentForm: {
                id: 0,
                name: '',
                type: ''
            }
        };
    }

    return {
        bookings,
        booking,
        loading,
        error,
        pagination,
        hasBookings,
        getBookings,
        getBooking,
        downloadReceipt,
        clearError,
        resetBooking
    };
});
