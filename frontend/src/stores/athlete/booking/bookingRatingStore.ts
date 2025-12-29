import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import type { BookingRating, BookingRatingPayload } from '@/types/athlete/booking/bookingRating';
import { BookingRatingService } from '@/services/athlete/booking/bookingRatingService';
import { errorMessageHttpResquestUtils } from '@/utils/errorMessageHttpResquestUtils';

export const useBookingRatingStore = defineStore('athlete-booking-rating', () => {
    const ratings = ref<BookingRating[]>([]);
    const rating = ref<BookingRating | null>(null);
    const loading = ref(false);
    const error = ref<string | null>(null);

    const hasRatings = computed(() => ratings.value.length > 0);
    const ratingsByRated = computed(() => {
        const grouped: Record<number, BookingRating[]> = {};
        ratings.value.forEach((rat) => {
            if (rat.participant?.id) {
                if (!grouped[rat.participant.id]) {
                    grouped[rat.participant.id] = [];
                }
                grouped[rat.participant.id].push(rat);
            }
        });
        return grouped;
    });

    const averageRatings = computed(() => {
        const avgByUser: Record<number, { average: number; count: number; userName: string }> = {};
        Object.entries(ratingsByRated.value).forEach(([participantId, userRatings]) => {
            const average = userRatings.reduce((sum, rat) => sum + rat.rating, 0) / userRatings.length;
            avgByUser[Number(participantId)] = {
                average: Math.round(average * 10) / 10,
                count: userRatings.length,
                userName: userRatings[0]?.participant?.name || 'Participante desconhecido'
            };
        });
        return avgByUser;
    });

    async function getRatings(bookingId: number, forceReload = false) {
        if (!forceReload && ratings.value.length > 0 && !loading.value) {
            return;
        }

        loading.value = true;
        error.value = null;
        try {
            const response = await BookingRatingService.getRatings(bookingId);
            ratings.value = response;
        } catch (err: unknown) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao carregar avaliações');
            ratings.value = [];
        } finally {
            loading.value = false;
        }
    }

    async function createRating(bookingId: number, payload: BookingRatingPayload) {
        loading.value = true;
        error.value = null;
        try {
            const newRating = await BookingRatingService.createRating(bookingId, payload);
            ratings.value.unshift(newRating);
            return newRating;
        } catch (err: unknown) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao adicionar avaliação');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function updateRating(bookingId: number, ratingId: number, payload: BookingRatingPayload) {
        loading.value = true;
        error.value = null;
        try {
            const updatedRating = await BookingRatingService.updateRating(bookingId, ratingId, payload);
            const index = ratings.value.findIndex((r) => r.id === ratingId);
            if (index !== -1) {
                ratings.value[index] = updatedRating;
            }
            return updatedRating;
        } catch (err: unknown) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao atualizar avaliação');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function deleteRating(bookingId: number, ratingId: number) {
        loading.value = true;
        error.value = null;
        try {
            await BookingRatingService.deleteRating(bookingId, ratingId);
            ratings.value = ratings.value.filter((r) => r.id !== ratingId);
        } catch (err: unknown) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao remover avaliação');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    function clearRatings() {
        ratings.value = [];
        rating.value = null;
        error.value = null;
    }

    return {
        ratings,
        rating,
        loading,
        error,
        hasRatings,
        ratingsByRated,
        averageRatings,
        getRatings,
        createRating,
        updateRating,
        deleteRating,
        clearRatings
    };
});
