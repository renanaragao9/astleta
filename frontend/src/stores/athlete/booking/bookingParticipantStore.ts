import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import type { BookingParticipant, BookingParticipantPayload } from '@/types/athlete/booking/bookingParticipant';
import { BookingParticipantService } from '@/services/athlete/booking/bookingParticipantService';
import { errorMessageHttpResquestUtils } from '@/utils/errorMessageHttpResquestUtils';

export const useBookingParticipantStore = defineStore('athlete-booking-participant', () => {
    const participants = ref<BookingParticipant[]>([]);
    const participant = ref<BookingParticipant | null>(null);
    const loading = ref(false);
    const error = ref<string | null>(null);

    const hasParticipants = computed(() => participants.value.length > 0);
    const paidParticipants = computed(() => participants.value.filter((p) => p.isPaid));
    const unpaidParticipants = computed(() => participants.value.filter((p) => !p.isPaid));
    const confirmedParticipants = computed(() => participants.value.filter((p) => p.status === 'confirmado'));
    const pendingParticipants = computed(() => participants.value.filter((p) => p.status === 'pendente'));
    const totalPaid = computed(() => paidParticipants.value.reduce((sum, p) => sum + parseFloat(String(p.amountPaid || 0)), 0));

    async function getParticipants(bookingId: number, forceReload = false) {
        if (!forceReload && participants.value.length > 0 && !loading.value) {
            return;
        }

        loading.value = true;
        error.value = null;
        try {
            const response = await BookingParticipantService.getParticipants(bookingId);
            participants.value = response;
        } catch (err: unknown) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao carregar participantes');
            participants.value = [];
        } finally {
            loading.value = false;
        }
    }

    async function createParticipant(bookingId: number, payload: BookingParticipantPayload) {
        loading.value = true;
        error.value = null;
        try {
            const newParticipant = await BookingParticipantService.createParticipant(bookingId, payload);
            participants.value.unshift(newParticipant);
            return newParticipant;
        } catch (err: unknown) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao adicionar participante');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function updateParticipant(bookingId: number, participantId: number, payload: BookingParticipantPayload) {
        loading.value = true;
        error.value = null;
        try {
            const updatedParticipant = await BookingParticipantService.updateParticipant(bookingId, participantId, payload);

            const index = participants.value.findIndex((p) => p.id === participantId);
            if (index !== -1) {
                participants.value[index] = updatedParticipant;
            }

            return updatedParticipant;
        } catch (err: unknown) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao atualizar participante');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function deleteParticipant(bookingId: number, participantId: number) {
        loading.value = true;
        error.value = null;
        try {
            await BookingParticipantService.deleteParticipant(bookingId, participantId);
            participants.value = participants.value.filter((p) => p.id !== participantId);
        } catch (err: unknown) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao remover participante');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    function clearParticipants() {
        participants.value = [];
        participant.value = null;
        error.value = null;
    }

    function setParticipant(participantData: BookingParticipant | null) {
        participant.value = participantData;
    }

    return {
        participants,
        participant,
        loading,
        error,
        hasParticipants,
        paidParticipants,
        unpaidParticipants,
        confirmedParticipants,
        pendingParticipants,
        totalPaid,
        getParticipants,
        createParticipant,
        updateParticipant,
        deleteParticipant,
        clearParticipants,
        setParticipant
    };
});
