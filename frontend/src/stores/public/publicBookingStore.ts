import { defineStore } from 'pinia';
import { ref } from 'vue';
import type { PublicBooking, PublicBookingPayload } from '@/types/public/booking/booking';
import type { PriceDetails } from '@/types/public/booking/priceCalculation';
import { PublicBookingService } from '@/services/public/publicBookingService';
import { getErrorMessage } from '@/utils/errorUtils';

export const usePublicBookingStore = defineStore('publicBooking', () => {
    const loading = ref(false);
    const error = ref<string | null>(null);

    const booking = ref<PublicBooking | null>(null);

    const createdBooking = ref<PublicBooking | null>(null);

    async function getAvailability(fieldId: number, date: Date) {
        loading.value = true;
        error.value = null;

        try {
            const response = await PublicBookingService.getAvailability(fieldId, date);
            return response.data;
        } catch (err) {
            error.value = getErrorMessage(err, 'Erro ao consultar disponibilidade');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function calculatePrice(calculationData: { field_id: number; start_time: string; end_time: string; include_extra_hour?: boolean }): Promise<PriceDetails> {
        loading.value = true;
        error.value = null;

        try {
            const response = await PublicBookingService.calculatePrice(calculationData);
            return response.data;
        } catch (err) {
            error.value = getErrorMessage(err, 'Erro ao calcular preço');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function createBooking(payload: PublicBookingPayload): Promise<{ data: PublicBooking; message: string }> {
        loading.value = true;
        error.value = null;

        try {
            const response = await PublicBookingService.createBooking(payload);
            createdBooking.value = response.data;
            return response;
        } catch (err) {
            error.value = getErrorMessage(err, 'Erro ao criar reserva');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    function clearBooking() {
        booking.value = null;
        createdBooking.value = null;
    }

    function clearError() {
        error.value = null;
    }

    return {
        booking,
        createdBooking,
        loading,
        error,

        getAvailability,
        calculatePrice,
        createBooking,
        clearBooking,
        clearError
    };
});
