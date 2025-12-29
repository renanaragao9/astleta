import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import type { Booking, BookingResponse, BookingPayload } from '@/types/company/booking';
import type { Filters } from '@/types/global/filter';
import type { SendBookingData } from '@/types/company/booking/sendBooking';
import { BookingService } from '@/services/company/bookingService';
import { errorMessageHttpResquestUtils } from '@/utils/errorMessageHttpResquestUtils';

export const useBookingStore = defineStore('booking', () => {
    const loading = ref(false);
    const error = ref<string | null>(null);
    const bookings = ref<Booking[]>([]);
    const booking = ref<Booking>({
        id: 0,
        bookingNumber: '',
        bookingDate: null,
        startTime: '',
        endTime: '',
        durationMinutes: 0,
        totalAmount: 0,
        createdAt: '',
        paymentType: '',
        paymentFormId: 0,
        bookingStatus: 'pendente',
        isExtraHour: false,
        notes: '',
        cancellationFeason: '',
        field: {
            id: 0,
            name: '',
            pricePerHour: '',
            extraHourPrice: ''
        },
        user: {
            id: 0,
            name: '',
            phone: ''
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
    const getBookingById = computed(() => (id: number) => {
        return bookings.value.find((booking) => booking.id === id);
    });

    async function fetchBookings(filters: Partial<Filters> = {}) {
        loading.value = true;
        error.value = null;

        try {
            const response: BookingResponse = await BookingService.getBookings(filters);
            bookings.value = response.data;
            pagination.value = {
                currentPage: response.meta.currentPage,
                lastPage: response.meta.lastPage,
                perPage: response.meta.perPage,
                total: response.meta.total
            };
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao carregar reservas');
            bookings.value = [];
        } finally {
            loading.value = false;
        }
    }

    async function fetchBooking(id: number) {
        loading.value = true;
        error.value = null;

        try {
            const response = await BookingService.getBooking(id);
            booking.value = response.data;
            booking.value.bookingDate = response.data.bookingDate ? new Date(response.data.bookingDate) : null;
            return response;
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao carregar reserva');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function createBooking(bookingData: BookingPayload) {
        loading.value = true;
        error.value = null;

        try {
            const response = await BookingService.createBooking(bookingData);
            bookings.value.unshift(response.data);
            return response;
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao criar reserva');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function updateBookingStatus(id: number, status: 'pendente' | 'confirmado' | 'cancelado' | 'concluido', cancellationReason?: string) {
        loading.value = true;
        error.value = null;

        try {
            const response = await BookingService.updateBookingStatus(id, status, cancellationReason);
            const index = bookings.value.findIndex((booking) => booking.id === id);
            if (index !== -1) {
                bookings.value[index].bookingStatus = status;
                if (cancellationReason) {
                    bookings.value[index].cancellationFeason = cancellationReason;
                }
            }
            return response;
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao atualizar status da reserva');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function getAvailability(fieldId: number, date: Date) {
        loading.value = true;
        error.value = null;

        try {
            const response = await BookingService.getAvailability(fieldId, date);
            return response.data;
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao consultar disponibilidade');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function calculatePrice(calculationData: { field_id: number; start_time: string; end_time: string; include_extra_hour?: boolean }) {
        loading.value = true;
        error.value = null;

        try {
            const response = await BookingService.calculatePrice(calculationData);
            return response.data;
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao calcular preço');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function sendBooking(sendData: SendBookingData) {
        loading.value = true;
        error.value = null;

        try {
            const response = await BookingService.sendBooking(sendData);
            return response;
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao enviar reserva');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    function clearBooking() {
        booking.value = {
            id: 0,
            bookingStatus: 'pendente',
            bookingNumber: '',
            bookingDate: null,
            startTime: '',
            endTime: '',
            durationMinutes: 0,
            totalAmount: 0,
            createdAt: '',
            paymentType: '',
            paymentFormId: 0,
            isExtraHour: false,
            notes: '',
            cancellationFeason: '',
            field: {
                id: 0,
                name: '',
                pricePerHour: '',
                extraHourPrice: ''
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

    function clearError() {
        error.value = null;
    }

    return {
        // State
        bookings,
        booking,
        loading,
        error,
        pagination,

        // Getters
        getBookingById,
        hasBookings,

        // Actions
        fetchBookings,
        fetchBooking,
        createBooking,
        getAvailability,
        calculatePrice,
        updateBookingStatus,
        sendBooking,
        clearBooking,
        clearError
    };
});
