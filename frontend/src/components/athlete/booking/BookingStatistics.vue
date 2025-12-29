<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { useBookingStatisticStore } from '@/stores/athlete/booking/bookingStatisticStore';
import { useBookingParticipantStore } from '@/stores/athlete/booking/bookingParticipantStore';
import { BookingStatisticService } from '@/services/athlete/booking/bookingStatisticService';
import { useToast } from 'primevue/usetoast';
import type { BookingStatistic, BookingStatisticPayload, StatisticOption } from '@/types/athlete/booking/bookingStatistic';

interface Props {
    bookingId: number;
    bookingStatus: string;
}

const props = defineProps<Props>();
const statisticStore = useBookingStatisticStore();
const participantStore = useBookingParticipantStore();
const toast = useToast();

const showDialog = ref(false);
const isEditMode = ref(false);
const editingStatistic = ref<BookingStatistic | null>(null);
const showDeleteDialog = ref(false);
const statisticToDelete = ref<BookingStatistic | null>(null);

const availableStatistics = ref<StatisticOption[]>([]);
const loadingStatistics = ref(false);
const submitted = ref(false);
const searchTerm = ref('');

const statisticForm = ref<BookingStatisticPayload & { id?: number }>({
    booking_participant_id: 0,
    statistic_id: 0,
    count: 1
});

const canManageStatistics = computed(() => props.bookingStatus === 'concluido');

const participantOptions = computed(() =>
    participantStore.participants.map((participant) => ({
        label: participant.user?.name || participant.name,
        value: participant.user?.id || participant.id,
        participant: participant
    }))
);

onMounted(async () => {
    await Promise.all([loadStatistics(), loadAvailableStatistics(), participantStore.getParticipants(props.bookingId)]);
});

const loadStatistics = async () => {
    try {
        await statisticStore.getStatistics(props.bookingId, false);
    } catch {
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: statisticStore.error,
            life: 5000
        });
    }
};

const loadAvailableStatistics = async () => {
    try {
        loadingStatistics.value = true;
        availableStatistics.value = await BookingStatisticService.getAvailableStatistics();
    } catch {
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: statisticStore.error,
            life: 5000
        });
    } finally {
        loadingStatistics.value = false;
    }
};

const saveStatistic = async (): Promise<void> => {
    submitted.value = true;

    if (!isEditMode.value && (!statisticForm.value.booking_participant_id || !statisticForm.value.statistic_id || !statisticForm.value.count)) {
        toast.add({
            severity: 'warn',
            summary: 'Atenção',
            detail: 'Preencha todos os campos obrigatórios',
            life: 5000
        });
        return;
    }

    try {
        if (isEditMode.value && editingStatistic.value) {
            const updatePayload: BookingStatisticPayload = {
                count: statisticForm.value.count
            };
            await statisticStore.updateStatistic(props.bookingId, editingStatistic.value.id, updatePayload);
            toast.add({
                severity: 'success',
                summary: 'Sucesso',
                detail: 'Estatística atualizada com sucesso',
                life: 5000
            });
            await loadStatistics();
        } else {
            const createPayload: BookingStatisticPayload = {
                booking_participant_id: statisticForm.value.booking_participant_id,
                statistic_id: statisticForm.value.statistic_id,
                count: statisticForm.value.count
            };
            await statisticStore.createStatistic(props.bookingId, createPayload);
            toast.add({
                severity: 'success',
                summary: 'Sucesso',
                detail: 'Estatística adicionada com sucesso',
                life: 5000
            });
            await loadStatistics();
        }
        showDialog.value = false;
    } catch {
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: statisticStore.error,
            life: 5000
        });
    }
};

const confirmDeleteStatistic = async (): Promise<void> => {
    if (!statisticToDelete.value) return;

    try {
        await statisticStore.deleteStatistic(props.bookingId, statisticToDelete.value.id);
        toast.add({
            severity: 'success',
            summary: 'Sucesso',
            detail: 'Estatística removida com sucesso',
            life: 5000
        });
        await loadStatistics();
        showDeleteDialog.value = false;
        statisticToDelete.value = null;
    } catch {
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: statisticStore.error,
            life: 5000
        });
    }
};

const getStatisticsByUser = computed(() => {
    const grouped: Record<string, { stats: BookingStatistic[]; imagePath?: string }> = {};
    statisticStore.statistics.forEach((stat) => {
        const participant = participantStore.participants.find((p) => p.id === stat.participant?.id);
        const userName = participant?.user?.name || participant?.name || 'Participante desconhecido';
        if (!grouped[userName]) {
            grouped[userName] = { stats: [], imagePath: participant?.imagePath };
        }
        grouped[userName].stats.push(stat);
    });

    if (!searchTerm.value.trim()) {
        return grouped;
    }

    const filtered: Record<string, { stats: BookingStatistic[]; imagePath?: string }> = {};
    Object.entries(grouped).forEach(([userName, data]) => {
        if (userName.toLowerCase().includes(searchTerm.value.toLowerCase())) {
            filtered[userName] = data;
        }
    });
    return filtered;
});

const openCreateStatisticDialog = () => {
    isEditMode.value = false;
    editingStatistic.value = null;
    submitted.value = false;
    statisticForm.value = {
        booking_participant_id: 0,
        statistic_id: 0,
        count: 1
    };
    showDialog.value = true;
};

const openUpdateStatisticDialog = (statistic: BookingStatistic) => {
    isEditMode.value = true;
    editingStatistic.value = statistic;
    statisticForm.value = {
        id: statistic.id,
        booking_participant_id: statistic.participant?.id || 0,
        statistic_id: statistic.statistic?.id || 0,
        count: statistic.count
    };
    showDialog.value = true;
};

const openDeleteStatisticDialog = (statistic: BookingStatistic) => {
    statisticToDelete.value = statistic;
    showDeleteDialog.value = true;
};
</script>

<template>
    <div class="space-y-6">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
            <div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Estatísticas da Partida</h3>
                <div class="mt-3 p-3 border border-blue-200 dark:border-blue-700 rounded-lg bg-blue-50 dark:bg-blue-900/20">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="pi pi-info-circle text-blue-600 dark:text-blue-400"></i>
                        <span class="text-sm font-medium text-blue-800 dark:text-blue-200">Importante</span>
                    </div>
                    <p class="text-sm text-blue-700 dark:text-blue-300">As estatísticas devem ser inseridas em conjunto pelos integrantes da equipe, colocando os dados em consenso com todos para garantir precisão e justiça.</p>
                </div>
            </div>

            <Button v-if="canManageStatistics" label="Adicionar Estatística" icon="pi pi-plus" @click="openCreateStatisticDialog" class="px-4 py-2" />

            <div v-else class="text-sm text-gray-500 dark:text-gray-400">Estatísticas só podem ser gerenciadas em reservas concluídas</div>
        </div>

        <div v-if="statisticStore.loading" class="text-center py-8">
            <ProgressSpinner />
            <p class="mt-4 text-gray-600 dark:text-gray-400">Carregando estatísticas...</p>
        </div>

        <div v-else-if="statisticStore.hasStatistics" class="space-y-6">
            <div class="flex justify-center">
                <IconField iconPosition="left">
                    <InputIcon class="pi pi-search" />
                    <InputText v-model="searchTerm" placeholder="Buscar por atleta..." class="w-full max-w-md" />
                </IconField>
            </div>
            <div v-for="[userName, userData] in Object.entries(getStatisticsByUser)" :key="userName" class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-primary/10 border border-primary/20 flex items-center justify-center overflow-hidden">
                        <img v-if="userData.imagePath" :src="userData.imagePath" :alt="userName" class="w-full h-full object-cover" />
                        <i v-else class="pi pi-user text-primary"></i>
                    </div>
                    {{ userName }}
                </h4>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    <div v-for="stat in userData.stats" :key="stat.id" class="border border-gray-200 dark:border-gray-700 rounded-lg p-3 hover:shadow-sm transition-shadow duration-200">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ stat.statistic?.name }}</span>
                            <span class="text-xl font-bold text-primary">{{ stat.count }}</span>
                        </div>

                        <div v-if="canManageStatistics" class="flex justify-center gap-2 pt-2 border-t border-gray-200 dark:border-gray-700 rounded-b-lg">
                            <Tag value="Editar" icon="pi pi-pencil" severity="info" class="cursor-pointer px-4 py-2 text-base rounded-lg" @click="openUpdateStatisticDialog(stat)" style="user-select: none" />
                            <Tag value="Remover" icon="pi pi-trash" severity="danger" class="cursor-pointer px-4 py-2 text-base rounded-lg" @click="openDeleteStatisticDialog(stat)" style="user-select: none" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-else class="text-center py-12 px-4">
            <div class="bg-gray-100 dark:bg-gray-800 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                <i class="pi pi-chart-bar text-2xl text-gray-400 dark:text-gray-500"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Nenhuma estatística registrada</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                <span v-if="canManageStatistics">Clique em "Adicionar Estatística" para começar a registrar dados da partida.</span>
                <span v-else>Estatísticas serão exibidas quando disponíveis.</span>
            </p>
        </div>

        <Dialog v-model:visible="showDialog" :header="isEditMode ? 'Editar Estatística' : 'Adicionar Estatística'" modal :style="{ width: '50vw' }" :breakpoints="{ '960px': '75vw', '640px': '100vw' }" :maximizable="true">
            <div class="space-y-4">
                <div v-if="!isEditMode">
                    <label for="user" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"> Jogador <span class="text-red-500">*</span> </label>
                    <Select
                        v-model="statisticForm.booking_participant_id"
                        :options="participantOptions"
                        optionLabel="label"
                        optionValue="value"
                        placeholder="Selecione o jogador"
                        :filter="true"
                        class="w-full"
                        :class="{ 'p-invalid': submitted && !statisticForm.booking_participant_id }"
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

                <div v-if="!isEditMode">
                    <label for="statistic" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"> Estatística <span class="text-red-500">*</span> </label>
                    <Select
                        v-model="statisticForm.statistic_id"
                        :options="availableStatistics"
                        optionLabel="name"
                        optionValue="id"
                        placeholder="Selecione a estatística"
                        class="w-full"
                        :loading="loadingStatistics"
                        :class="{ 'p-invalid': submitted && !statisticForm.statistic_id }"
                    />
                </div>

                <div>
                    <label for="count" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"> Quantidade <span class="text-red-500">*</span> </label>
                    <InputNumber
                        v-model="statisticForm.count"
                        inputId="count-buttons"
                        class="w-full"
                        showButtons
                        buttonLayout="horizontal"
                        mode="decimal"
                        locale="pt-BR"
                        placeholder="Quantidade"
                        fluid
                        :step="1"
                        :min="0"
                        :max="999"
                        :invalid="submitted && (!statisticForm.count || statisticForm.count < 0)"
                    >
                        <template #incrementicon>
                            <span class="pi pi-plus" />
                        </template>
                        <template #decrementicon>
                            <span class="pi pi-minus" />
                        </template>
                    </InputNumber>
                </div>
            </div>

            <template #footer>
                <div class="flex gap-3 justify-end">
                    <Button label="Cancelar" severity="secondary" @click="showDialog = false" />
                    <Button :label="isEditMode ? 'Atualizar' : 'Adicionar'" @click="saveStatistic" :disabled="!statisticForm.count || (!isEditMode && (!statisticForm.booking_participant_id || !statisticForm.statistic_id))" />
                </div>
            </template>
        </Dialog>

        <Dialog v-model:visible="showDeleteDialog" header="Confirmar Remoção" modal :style="{ width: '90vw', maxWidth: '400px' }">
            <div class="space-y-4">
                <p class="text-gray-700 dark:text-gray-300">Tem certeza que deseja remover esta estatística?</p>
                <div v-if="statisticToDelete" class="bg-gray-50 dark:bg-gray-700 p-3 rounded">
                    <p><strong>Jogador:</strong> {{ statisticToDelete.participant?.name }}</p>
                    <p><strong>Estatística:</strong> {{ statisticToDelete.statistic?.name }}</p>
                    <p><strong>Quantidade:</strong> {{ statisticToDelete.count }}</p>
                </div>
            </div>

            <template #footer>
                <div class="flex gap-3 justify-end">
                    <Button label="Cancelar" severity="secondary" @click="showDeleteDialog = false" />
                    <Button label="Remover" severity="danger" @click="confirmDeleteStatistic" />
                </div>
            </template>
        </Dialog>
    </div>
</template>
