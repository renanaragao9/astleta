<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { useBookingRatingStore } from '@/stores/athlete/booking/bookingRatingStore';
import { useBookingParticipantStore } from '@/stores/athlete/booking/bookingParticipantStore';
import { useToast } from 'primevue/usetoast';
import type { BookingRating, BookingRatingPayload } from '@/types/athlete/booking/bookingRating';

interface Props {
    bookingId: number;
    bookingStatus: string;
}

const props = defineProps<Props>();
const ratingStore = useBookingRatingStore();
const participantStore = useBookingParticipantStore();
const toast = useToast();

const showDialog = ref(false);
const isEditMode = ref(false);
const editingRating = ref<BookingRating | null>(null);
const showDeleteDialog = ref(false);
const ratingToDelete = ref<BookingRating | null>(null);
const submitted = ref(false);
const searchTerm = ref('');
const ratingForm = ref<BookingRatingPayload & { id?: number }>({
    user_id: undefined,
    booking_participant_id: 0,
    rating: 5,
    technical_rating: 5,
    tactical_rating: 5,
    physical_rating: 5,
    mental_rating: 5,
    teamwork_rating: 5,
    comment: ''
});

const canManageRatings = computed<boolean>(() => props.bookingStatus === 'concluido');
import type { BookingParticipant } from '@/types/athlete/booking/bookingParticipant';

const participantOptions = computed<{ label: string; value: number; participant: BookingParticipant }[]>(() =>
    participantStore.participants.map((p) => ({
        label: p.user?.name || p.name,
        value: p.id,
        participant: p
    }))
);

const filteredRatings = computed(() => {
    if (!searchTerm.value.trim()) {
        return ratingStore.ratings;
    }
    return ratingStore.ratings.filter((rating) => (rating.participant?.name || '').toLowerCase().includes(searchTerm.value.toLowerCase()));
});

onMounted(() => {
    loadRatings();
});

const loadRatings = async (): Promise<void> => {
    try {
        await ratingStore.getRatings(props.bookingId);
    } catch {
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: 'Erro ao carregar avaliações',
            life: 5000
        });
    }
};

const saveRating = async (): Promise<void> => {
    submitted.value = true;

    if (
        !isEditMode.value &&
        (!ratingForm.value.booking_participant_id ||
            !ratingForm.value.rating ||
            !ratingForm.value.technical_rating ||
            !ratingForm.value.tactical_rating ||
            !ratingForm.value.physical_rating ||
            !ratingForm.value.mental_rating ||
            !ratingForm.value.teamwork_rating)
    ) {
        toast.add({
            severity: 'warn',
            summary: 'Atenção',
            detail: 'Preencha todos os campos obrigatórios',
            life: 5000
        });
        return;
    }

    try {
        if (isEditMode.value && editingRating.value) {
            const updatePayload: BookingRatingPayload = {
                rating: ratingForm.value.rating,
                technical_rating: ratingForm.value.technical_rating,
                tactical_rating: ratingForm.value.tactical_rating,
                physical_rating: ratingForm.value.physical_rating,
                mental_rating: ratingForm.value.mental_rating,
                teamwork_rating: ratingForm.value.teamwork_rating,
                comment: ratingForm.value.comment
            };

            await ratingStore.updateRating(props.bookingId, editingRating.value.id, updatePayload);
            await ratingStore.getRatings(props.bookingId, true);

            toast.add({
                severity: 'success',
                summary: 'Sucesso',
                detail: 'Avaliação atualizada com sucesso',
                life: 5000
            });
        } else {
            const createPayload: BookingRatingPayload = {
                user_id: ratingForm.value.user_id,
                booking_participant_id: ratingForm.value.booking_participant_id,
                rating: ratingForm.value.rating,
                technical_rating: ratingForm.value.technical_rating,
                tactical_rating: ratingForm.value.tactical_rating,
                physical_rating: ratingForm.value.physical_rating,
                mental_rating: ratingForm.value.mental_rating,
                teamwork_rating: ratingForm.value.teamwork_rating,
                comment: ratingForm.value.comment
            };

            await ratingStore.createRating(props.bookingId, createPayload);
            await ratingStore.getRatings(props.bookingId, true);

            toast.add({
                severity: 'success',
                summary: 'Sucesso',
                detail: 'Avaliação adicionada com sucesso',
                life: 5000
            });
        }

        showDialog.value = false;
    } catch {
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: ratingStore.error,
            life: 5000
        });
    }
};

const confirmDeleteRating = async (): Promise<void> => {
    if (!ratingToDelete.value) return;

    try {
        await ratingStore.deleteRating(props.bookingId, ratingToDelete.value.id);
        await ratingStore.getRatings(props.bookingId, true);

        toast.add({
            severity: 'success',
            summary: 'Sucesso',
            detail: 'Avaliação removida com sucesso',
            life: 5000
        });

        showDeleteDialog.value = false;
        ratingToDelete.value = null;
    } catch {
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: ratingStore.error,
            life: 5000
        });
    }
};

const openCreateRatingDialog = (): void => {
    isEditMode.value = false;
    editingRating.value = null;
    submitted.value = false;
    ratingForm.value = {
        user_id: undefined,
        booking_participant_id: 0,
        rating: 5,
        technical_rating: 5,
        tactical_rating: 5,
        physical_rating: 5,
        mental_rating: 5,
        teamwork_rating: 5,
        comment: ''
    };
    showDialog.value = true;
};

const openUpdateRatingDialog = (rating: BookingRating): void => {
    isEditMode.value = true;
    editingRating.value = rating;
    ratingForm.value = {
        id: rating.id,
        user_id: rating.user?.id,
        booking_participant_id: rating.participant?.id || 0,
        rating: rating.rating,
        technical_rating: rating.technicalRating || 5,
        tactical_rating: rating.tacticalRating || 5,
        physical_rating: rating.physicalRating || 5,
        mental_rating: rating.mentalRating || 5,
        teamwork_rating: rating.teamworkRating || 5,
        comment: rating.comment || ''
    };
    showDialog.value = true;
};

const openDeleteRatingDialog = (rating: BookingRating): void => {
    ratingToDelete.value = rating;
    showDeleteDialog.value = true;
};

const getStarIcon = (rating: number, position: number): string => {
    const normalizedRating = rating / 2;
    if (normalizedRating >= position) {
        return 'pi pi-star-fill';
    } else if (normalizedRating >= position - 0.5) {
        return 'pi pi-star-half';
    } else {
        return 'pi pi-star';
    }
};

const getStarClass = (rating: number, position: number): string => {
    const normalizedRating = rating / 2;
    if (normalizedRating >= position - 0.5) {
        return 'text-primary';
    } else {
        return 'text-gray-300';
    }
};
</script>

<template>
    <div class="space-y-6">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
            <div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Avaliações dos Jogadores</h3>
                <div class="mt-3 p-3 border border-blue-200 dark:border-blue-700 rounded-lg bg-blue-50 dark:bg-blue-900/20">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="pi pi-info-circle text-blue-600 dark:text-blue-400"></i>
                        <span class="text-sm font-medium text-blue-800 dark:text-blue-200">Importante</span>
                    </div>
                    <p class="text-sm text-blue-700 dark:text-blue-300">As avaliações devem ser inseridas em conjunto pelos integrantes da equipe, colocando os dados em consenso com todos para garantir precisão e justiça.</p>
                </div>
            </div>

            <Button v-if="canManageRatings && participantOptions.length > 0" label="Avaliar Jogador" icon="pi pi-star" @click="openCreateRatingDialog" class="px-4 py-2" />

            <div v-else-if="!canManageRatings" class="text-sm text-gray-500 dark:text-gray-400">Avaliações só podem ser feitas em reservas concluídas</div>
        </div>

        <div v-if="ratingStore.loading" class="text-center py-8">
            <ProgressSpinner />
            <p class="mt-4 text-gray-600 dark:text-gray-400">Carregando avaliações...</p>
        </div>

        <div v-if="ratingStore.hasRatings" class="space-y-4">
            <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                <i class="pi pi-list text-primary"></i>
                Todas as Avaliações
            </h4>

            <div class="flex justify-center">
                <IconField iconPosition="left">
                    <InputIcon class="pi pi-search" />
                    <InputText v-model="searchTerm" placeholder="Buscar por atleta..." class="w-full max-w-md" />
                </IconField>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div v-for="rating in filteredRatings" :key="rating.id" class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-sm transition-shadow duration-200">
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-primary/10 border border-primary/20 flex items-center justify-center overflow-hidden">
                                <img v-if="rating.participant?.imagePath" :src="rating.participant.imagePath" :alt="rating.participant.name" class="w-full h-full object-cover" />
                                <i v-else class="pi pi-user text-primary"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-gray-100 truncate">
                                    {{ rating.participant?.name }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ rating.createdAt }}</p>
                            </div>
                        </div>

                        <div v-if="canManageRatings" class="flex justify-center gap-2 pt-2 border-t border-gray-200 dark:border-gray-700 rounded-b-lg">
                            <Tag value="Editar" icon="pi pi-pencil" severity="info" class="cursor-pointer px-4 py-2 text-base rounded-lg" @click="openUpdateRatingDialog(rating)" style="user-select: none" />
                            <Tag value="Remover" icon="pi pi-trash" severity="danger" class="cursor-pointer px-4 py-2 text-base rounded-lg" @click="openDeleteRatingDialog(rating)" style="user-select: none" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div class="text-center border border-gray-200 dark:border-gray-700 rounded-lg p-2">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Geral</p>
                            <div class="flex justify-center mb-1">
                                <i v-for="star in 5" :key="star" :class="[getStarIcon(rating.rating, star), getStarClass(rating.rating, star)]" class="text-sm" />
                            </div>
                            <p class="text-sm font-bold text-primary">{{ rating.rating }}/10</p>
                        </div>

                        <div class="text-center border border-gray-200 dark:border-gray-700 rounded-lg p-2">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Técnico</p>
                            <div class="flex justify-center mb-1">
                                <i v-for="star in 5" :key="star" :class="[getStarIcon(rating.technicalRating, star), getStarClass(rating.technicalRating, star)]" class="text-sm" />
                            </div>
                            <p class="text-sm font-bold text-primary">{{ rating.technicalRating }}/10</p>
                        </div>

                        <div class="text-center border border-gray-200 dark:border-gray-700 rounded-lg p-2">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Tático</p>
                            <div class="flex justify-center mb-1">
                                <i v-for="star in 5" :key="star" :class="[getStarIcon(rating.tacticalRating, star), getStarClass(rating.tacticalRating, star)]" class="text-sm" />
                            </div>
                            <p class="text-sm font-bold text-primary">{{ rating.tacticalRating }}/10</p>
                        </div>

                        <div class="text-center border border-gray-200 dark:border-gray-700 rounded-lg p-2">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Físico</p>
                            <div class="flex justify-center mb-1">
                                <i v-for="star in 5" :key="star" :class="[getStarIcon(rating.physicalRating, star), getStarClass(rating.physicalRating, star)]" class="text-sm" />
                            </div>
                            <p class="text-sm font-bold text-primary">{{ rating.physicalRating }}/10</p>
                        </div>

                        <div class="text-center border border-gray-200 dark:border-gray-700 rounded-lg p-2">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Mental</p>
                            <div class="flex justify-center mb-1">
                                <i v-for="star in 5" :key="star" :class="[getStarIcon(rating.mentalRating, star), getStarClass(rating.mentalRating, star)]" class="text-sm" />
                            </div>
                            <p class="text-sm font-bold text-primary">{{ rating.mentalRating }}/10</p>
                        </div>

                        <div class="text-center border border-gray-200 dark:border-gray-700 rounded-lg p-2">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Trabalho em Equipe</p>
                            <div class="flex justify-center mb-1">
                                <i v-for="star in 5" :key="star" :class="[getStarIcon(rating.teamworkRating, star), getStarClass(rating.teamworkRating, star)]" class="text-sm" />
                            </div>
                            <p class="text-sm font-bold text-primary">{{ rating.teamworkRating }}/10</p>
                        </div>
                    </div>

                    <div v-if="rating.comment" class="border border-gray-200 dark:border-gray-700 rounded-lg p-3">
                        <p class="text-sm text-gray-700 dark:text-gray-300">{{ rating.comment }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div v-else-if="!ratingStore.loading" class="text-center py-12 px-4">
            <div class="bg-gray-100 dark:bg-gray-800 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                <i class="pi pi-star text-2xl text-gray-400 dark:text-gray-500"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Nenhuma avaliação registrada</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                <span v-if="canManageRatings">Clique em "Avaliar Jogador" para começar a avaliar os participantes.</span>
                <span v-else>Avaliações serão exibidas quando disponíveis.</span>
            </p>
        </div>

        <Dialog v-model:visible="showDialog" :header="isEditMode ? 'Editar Avaliação' : 'Avaliar Jogador'" modal :style="{ width: '50vw' }" :breakpoints="{ '960px': '75vw', '640px': '100vw' }" :maximizable="true">
            <div class="space-y-4">
                <div v-if="!isEditMode">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"> Jogador * </label>
                    <Select
                        v-model="ratingForm.booking_participant_id"
                        :options="participantOptions"
                        optionLabel="label"
                        optionValue="value"
                        placeholder="Selecione o jogador"
                        :filter="true"
                        class="w-full"
                        :class="{ 'p-invalid': submitted && !ratingForm.booking_participant_id }"
                    >
                        <template #option="{ option }">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-primary/10 border border-primary/20 flex items-center justify-center overflow-hidden flex-shrink-0">
                                    <img v-if="option.participant.imagePath" :src="option.participant.imagePath" :alt="option.label" class="w-full h-full object-cover" />
                                    <i v-else class="pi pi-user text-primary text-xs"></i>
                                </div>
                                <span>{{ option.label }}</span>
                            </div>
                        </template>
                        <template #value="{ value, placeholder }">
                            <div v-if="value" class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-primary/10 border border-primary/20 flex items-center justify-center overflow-hidden flex-shrink-0">
                                    <img
                                        v-if="participantOptions.find((opt) => opt.value === value)?.participant.imagePath"
                                        :src="participantOptions.find((opt) => opt.value === value)?.participant.imagePath"
                                        :alt="participantOptions.find((opt) => opt.value === value)?.label"
                                        class="w-full h-full object-cover"
                                    />
                                    <i v-else class="pi pi-user text-primary text-xs"></i>
                                </div>
                                <span>{{ participantOptions.find((opt) => opt.value === value)?.label }}</span>
                            </div>
                            <span v-else class="text-gray-500">{{ placeholder }}</span>
                        </template>
                    </Select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"> Avaliação Geral * (1-10) </label>
                    <InputNumber v-model="ratingForm.rating" :min="1" :max="10" showButtons buttonLayout="horizontal" :step="1" fluid :class="{ 'p-invalid': submitted && !ratingForm.rating }">
                        <template #incrementicon>
                            <span class="pi pi-plus" />
                        </template>
                        <template #decrementicon>
                            <span class="pi pi-minus" />
                        </template>
                    </InputNumber>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"> Técnico (1-10) </label>
                        <InputNumber v-model="ratingForm.technical_rating" :min="1" :max="10" showButtons buttonLayout="horizontal" :step="1" fluid>
                            <template #incrementicon>
                                <span class="pi pi-plus" />
                            </template>
                            <template #decrementicon>
                                <span class="pi pi-minus" />
                            </template>
                        </InputNumber>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"> Tático (1-10) </label>
                        <InputNumber v-model="ratingForm.tactical_rating" :min="1" :max="10" showButtons buttonLayout="horizontal" :step="1" fluid>
                            <template #incrementicon>
                                <span class="pi pi-plus" />
                            </template>
                            <template #decrementicon>
                                <span class="pi pi-minus" />
                            </template>
                        </InputNumber>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"> Físico (1-10) </label>
                        <InputNumber v-model="ratingForm.physical_rating" :min="1" :max="10" showButtons buttonLayout="horizontal" :step="1" fluid>
                            <template #incrementicon>
                                <span class="pi pi-plus" />
                            </template>
                            <template #decrementicon>
                                <span class="pi pi-minus" />
                            </template>
                        </InputNumber>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"> Mental (1-10) </label>
                        <InputNumber v-model="ratingForm.mental_rating" :min="1" :max="10" showButtons buttonLayout="horizontal" :step="1" fluid>
                            <template #incrementicon>
                                <span class="pi pi-plus" />
                            </template>
                            <template #decrementicon>
                                <span class="pi pi-minus" />
                            </template>
                        </InputNumber>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"> Trabalho em Equipe (1-10) </label>
                        <InputNumber v-model="ratingForm.teamwork_rating" :min="1" :max="10" showButtons buttonLayout="horizontal" :step="1" fluid>
                            <template #incrementicon>
                                <span class="pi pi-plus" />
                            </template>
                            <template #decrementicon>
                                <span class="pi pi-minus" />
                            </template>
                        </InputNumber>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"> Comentário </label>
                    <Textarea v-model="ratingForm.comment" rows="3" placeholder="Comentário opcional sobre a performance..." class="w-full" :maxlength="1000" />
                </div>
            </div>

            <template #footer>
                <div class="flex gap-3 justify-end">
                    <Button label="Cancelar" severity="secondary" @click="showDialog = false" />
                    <Button
                        :label="isEditMode ? 'Atualizar' : 'Avaliar'"
                        @click="saveRating"
                        :disabled="
                            !ratingForm.rating ||
                            !ratingForm.technical_rating ||
                            !ratingForm.tactical_rating ||
                            !ratingForm.physical_rating ||
                            !ratingForm.mental_rating ||
                            !ratingForm.teamwork_rating ||
                            (!isEditMode && !ratingForm.booking_participant_id)
                        "
                    />
                </div>
            </template>
        </Dialog>

        <Dialog v-model:visible="showDeleteDialog" header="Confirmar Remoção" modal :style="{ width: '90vw', maxWidth: '400px' }">
            <div class="space-y-4">
                <p class="text-gray-700 dark:text-gray-300">Tem certeza que deseja remover esta avaliação?</p>
                <div v-if="ratingToDelete" class="bg-gray-50 dark:bg-gray-700 p-3 rounded">
                    <p><strong>Jogador Avaliado:</strong> {{ ratingToDelete.participant?.name }}</p>
                    <p><strong>Avaliação:</strong> {{ ratingToDelete.rating }}/10</p>
                    <p v-if="ratingToDelete.comment"><strong>Comentário:</strong> {{ ratingToDelete.comment }}</p>
                </div>
            </div>

            <template #footer>
                <div class="flex gap-3 justify-end">
                    <Button label="Cancelar" severity="secondary" @click="showDeleteDialog = false" />
                    <Button label="Remover" severity="danger" @click="confirmDeleteRating" />
                </div>
            </template>
        </Dialog>
    </div>
</template>
