<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { useBookingParticipantStore } from '@/stores/athlete/booking/bookingParticipantStore';
import type { BookingParticipant, BookingParticipantPayload } from '@/types/athlete/booking/bookingParticipant';
import type { Booking } from '@/types/athlete/booking/bookingAthlete';
import { useToast } from 'primevue/usetoast';
import { useFormat } from '@/utils/useFormat';
import TeamDraw from '@/components/athlete/booking/TeamDraw.vue';

interface Props {
    booking: Booking;
}

const props = defineProps<Props>();
const participantStore = useBookingParticipantStore();
const toast = useToast();
const { formatCurrency } = useFormat();

const showDialog = ref(false);
const isEditMode = ref(false);
const showTeamDraw = ref(false);
const showDeleteDialog = ref(false);
const editingParticipant = ref<BookingParticipant | null>(null);
const participantToDelete = ref<BookingParticipant | null>(null);
const participantForm = ref<BookingParticipantPayload & { id?: number; participant_type?: string; user_phone?: string }>({
    name: '',
    phone: '',
    amount_paid: undefined,
    status: 'pendente',
    participant_type: 'guest',
    user_phone: ''
});

const canManageParticipants = computed(() => {
    return !['cancelado', 'pendente'].includes(props.booking.bookingStatus);
});

const confirmedParticipants = computed(() => {
    return participantStore.participants.filter((p) => p.status === 'confirmado');
});

onMounted(() => {
    loadParticipants();
});

const loadParticipants = async (): Promise<void> => {
    try {
        await participantStore.getParticipants(props.booking.id, false);
    } catch {
        if (participantStore.error) {
            toast.add({
                severity: 'error',
                summary: 'Erro',
                detail: participantStore.error,
                life: 5000
            });
        }
    }
};

const saveParticipant = async (): Promise<void> => {
    try {
        if (isEditMode.value && editingParticipant.value) {
            const payload = UpdateBookingParticipantPayload(participantForm.value);
            await participantStore.updateParticipant(props.booking.id, editingParticipant.value.id, payload);
            toast.add({
                severity: 'success',
                summary: 'Sucesso',
                detail: 'Participante atualizado com sucesso',
                life: 3000
            });
        } else {
            const payload = CreateBookingParticipantPayload(participantForm.value);
            await participantStore.createParticipant(props.booking.id, payload);
            toast.add({
                severity: 'success',
                summary: 'Sucesso',
                detail: 'Participante adicionado com sucesso',
                life: 3000
            });
        }
        showDialog.value = false;
    } catch {
        if (participantStore.error) {
            toast.add({
                severity: 'error',
                summary: 'Erro',
                detail: participantStore.error,
                life: 5000
            });
        }
    }
};

const confirmDeleteParticipant = async (): Promise<void> => {
    if (!participantToDelete.value) return;

    try {
        await participantStore.deleteParticipant(props.booking.id, participantToDelete.value.id);
        await participantStore.getParticipants(props.booking.id, true);

        toast.add({
            severity: 'success',
            summary: 'Sucesso',
            detail: 'Participante removido com sucesso',
            life: 5000
        });

        showDeleteDialog.value = false;
        participantToDelete.value = null;
    } catch {
        if (participantStore.error) {
            toast.add({
                severity: 'error',
                summary: 'Erro',
                detail: participantStore.error,
                life: 5000
            });
        }
    }
};

const CreateBookingParticipantPayload = (form: typeof participantForm.value): BookingParticipantPayload => {
    const payload: BookingParticipantPayload = {
        amount_paid: form.amount_paid,
        phone: form.phone,
        status: form.status
    };
    if (form.participant_type === 'guest') payload.name = form.name;
    if (form.participant_type === 'user') payload.user_phone = form.user_phone ? form.user_phone.replace(/\D/g, '') : undefined;
    return payload;
};

const UpdateBookingParticipantPayload = (form: typeof participantForm.value): BookingParticipantPayload => {
    const payload: BookingParticipantPayload = {
        phone: form.phone,
        amount_paid: form.amount_paid,
        status: form.status
    };
    if (form.participant_type === 'guest') payload.name = form.name;
    if (form.participant_type === 'user') payload.user_phone = form.user_phone ? form.user_phone.replace(/\D/g, '') : undefined;
    return payload;
};

const openCreateBookingParticipantDialog = (): void => {
    isEditMode.value = false;
    editingParticipant.value = null;
    participantForm.value = {
        name: '',
        phone: '',
        amount_paid: undefined,
        status: 'pendente',
        participant_type: 'guest',
        user_phone: ''
    };
    showDialog.value = true;
};

const openEditBookingParticipantDialog = (participant: BookingParticipant): void => {
    isEditMode.value = true;
    editingParticipant.value = participant;
    participantForm.value = {
        id: participant.id,
        name: participant.name,
        phone: participant.phone || '',
        amount_paid: participant.amountPaid,
        status: participant.status,
        participant_type: 'guest',
        user_phone: ''
    };
    showDialog.value = true;
};

const openDeleteBookingParticipantDialog = (participant: BookingParticipant): void => {
    participantToDelete.value = participant;
    showDeleteDialog.value = true;
};

const openTeamDraw = (): void => {
    if (confirmedParticipants.value.length < 4) {
        toast.add({
            severity: 'warn',
            summary: 'Poucos Participantes',
            detail: 'É necessário pelo menos 4 participantes confirmados para sortear times.',
            life: 4000
        });
        return;
    }
    showTeamDraw.value = true;
};

const getStatusSeverity = (status: string): string => {
    switch (status) {
        case 'confirmado':
            return 'success';
        case 'pendente':
            return 'warn';
        case 'cancelado':
            return 'danger';
        default:
            return 'info';
    }
};

const getStatusLabel = (status: string): string => {
    const statusMap: Record<string, string> = {
        pendente: 'Pendente',
        confirmado: 'Confirmado',
        cancelado: 'Cancelado'
    };
    return statusMap[status] || status;
};

const sendToWhatsApp = (): void => {
    const baseUrl = window.location.origin;
    const rachasUrl = `${baseUrl}/atleta/rachas`;

    let message = `*📱 Confirme sua Reserva*\n\n`;
    message += `*N° da Reserva:* ${props.booking.bookingNumber}\n\n`;
    message += `*📋 Detalhes da Reserva*\n`;
    message += `📅 Data: ${props.booking.bookingDate}\n`;
    message += `🕐 Horário Início: ${props.booking.startTime}\n`;
    message += `🕑 Horário Fim: ${props.booking.endTime}\n`;
    message += `⏱️ Duração: ${props.booking.durationMinutes}\n`;
    message += `🏟️ Arena: ${props.booking.field.name}\n`;

    if (props.booking.company) {
        message += `🏢 Local: ${props.booking.company.name}\n`;
        if (props.booking.company.address) {
            message += `📍 Endereço: ${props.booking.company.address}\n`;
        }
    }

    message += `\n🔗 *Acesse o astleta*\n`;
    message += `Vá em "Meus Rachas" através do link: ${rachasUrl}\n`;
    message += `Clique em "Entrar no Racha" e informe o número da reserva acima.\n`;

    const url = `https://wa.me/?text=${encodeURIComponent(message)}`;
    window.open(url, '_blank');
};
</script>

<template>
    <div class="space-y-6">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
            <div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Participantes do Racha</h3>
                <p class="text-gray-600 dark:text-gray-400 mt-1">
                    {{ participantStore.participants.length }} participante(s) | {{ participantStore.confirmedParticipants.length }} confirmado(s) | {{ participantStore.paidParticipants.length }} pago(s)
                </p>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Total pago: {{ formatCurrency(participantStore.totalPaid) }} | Valor da reserva: {{ formatCurrency(props.booking.totalAmount) }}</p>
                <p class="text-gray-600 dark:text-gray-400 mt-1">{{ participantStore.totalPaid > props.booking.totalAmount ? 'Saldo' : 'Quanto falta' }}: {{ formatCurrency(Math.abs(props.booking.totalAmount - participantStore.totalPaid)) }}</p>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Sugestão por participante: {{ participantStore.participants.length > 0 ? formatCurrency(props.booking.totalAmount / participantStore.participants.length) : '0' }}</p>
            </div>

            <div v-if="canManageParticipants" class="flex flex-col md:flex-row gap-2 w-full md:w-auto">
                <Button label="Adicionar Participante" icon="pi pi-plus" @click="openCreateBookingParticipantDialog" class="w-full md:w-auto" />
                <Button label="Compartilhar Reserva" icon="pi pi-whatsapp" @click="sendToWhatsApp" class="w-full md:w-auto" />
                <Button label="Sortear Times" icon="pi pi-users" @click="openTeamDraw" class="w-full md:w-auto" />
            </div>
        </div>

        <div v-if="participantStore.loading" class="text-center py-8">
            <ProgressSpinner />
            <p class="mt-3 text-gray-600 dark:text-gray-400">Carregando participantes...</p>
        </div>

        <div v-else-if="participantStore.hasParticipants" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div v-for="participant in participantStore.participants" :key="participant.id" class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition-shadow duration-200">
                <div class="flex flex-col space-y-3">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-primary/10 border border-primary/20 flex items-center justify-center overflow-hidden">
                                <img v-if="participant.imagePath" :src="participant.imagePath" :alt="participant.name" class="w-full h-full object-cover" />
                                <i v-else class="pi pi-user text-primary"></i>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 truncate">{{ participant.name }}</h4>
                        </div>
                        <div class="flex flex-col gap-1 ml-2 flex-shrink-0">
                            <Tag :value="getStatusLabel(participant.status)" :severity="getStatusSeverity(participant.status)" />
                            <Tag v-if="participant.isPaid" value="Pago" severity="success" icon="pi pi-check" />
                        </div>
                    </div>

                    <div class="space-y-2">
                        <div v-if="participant.phone" class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400 flex items-center gap-2">
                                <i class="pi pi-phone text-xs text-primary"></i>
                                Telefone:
                            </span>
                            <span class="text-sm text-gray-900 dark:text-gray-100 truncate ml-2">{{ participant.phone }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400 flex items-center gap-2">
                                <i class="pi pi-money-bill text-xs text-primary"></i>
                                Valor pago:
                            </span>
                            <span class="font-semibold text-green-600 dark:text-green-400">{{ formatCurrency(participant.amountPaid) }}</span>
                        </div>
                        <div v-if="participant.positionName" class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400 flex items-center gap-2">
                                <i class="pi pi-user text-xs text-primary"></i>
                                Posição:
                            </span>
                            <span class="text-sm text-gray-900 dark:text-gray-100">{{ participant.positionName }}</span>
                        </div>
                        <div v-if="participant.featureName" class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400 flex items-center gap-2">
                                <i class="pi pi-star text-xs text-primary"></i>
                                Especialidade:
                            </span>
                            <span class="text-sm text-gray-900 dark:text-gray-100">{{ participant.featureName }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400 flex items-center gap-2">
                                <i class="pi pi-calendar text-xs text-primary"></i>
                                Adicionado:
                            </span>
                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ participant.createdAt }}</span>
                        </div>
                    </div>

                    <div v-if="canManageParticipants" class="flex justify-center gap-2 pt-2 border-t border-gray-200 dark:border-gray-700 rounded-b-lg">
                        <Tag value="Editar" icon="pi pi-pencil" severity="info" class="cursor-pointer px-4 py-2 text-base rounded-lg" @click="openEditBookingParticipantDialog(participant)" style="user-select: none" />
                        <Tag value="Remover" icon="pi pi-trash" severity="danger" class="cursor-pointer px-4 py-2 text-base rounded-lg" @click="openDeleteBookingParticipantDialog(participant)" style="user-select: none" />
                    </div>
                </div>
            </div>
        </div>

        <div v-else class="text-center py-12 px-4">
            <div class="bg-gray-100 dark:bg-gray-800 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                <i class="pi pi-users text-2xl text-gray-400 dark:text-gray-500"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Nenhum participante adicionado</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Adicione participantes para organizar seu racha</p>
        </div>

        <Dialog v-model:visible="showDialog" :header="isEditMode ? 'Editar Participante' : 'Adicionar Participante'" modal :style="{ width: '50vw' }" :breakpoints="{ '960px': '75vw', '640px': '100vw' }" :maximizable="true">
            <div class="space-y-4 pt-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tipo de Participante <span class="text-red-500">*</span></label>
                    <Select
                        v-model="participantForm.participant_type"
                        :options="[
                            { label: 'Convidado (sem cadastro)', value: 'guest' },
                            { label: 'Usuário do sistema', value: 'user' }
                        ]"
                        optionLabel="label"
                        optionValue="value"
                        placeholder="Selecione o tipo"
                        class="w-full"
                    />
                </div>

                <div v-if="participantForm.participant_type === 'guest'">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nome <span class="text-red-500">*</span></label>
                    <InputText v-model="participantForm.name" placeholder="Digite o nome do participante" class="w-full" required />
                </div>

                <div v-if="participantForm.participant_type === 'user'">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Telefone do Usuário <span class="text-red-500">*</span></label>
                    <InputMask id="user_phone" v-model="participantForm.user_phone" mask="99 99999-9999" placeholder="00 00000-0000" class="w-full" required type="tel" />
                </div>

                <div v-if="participantForm.participant_type === 'guest'">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Telefone</label>
                    <InputMask id="phone" v-model="participantForm.phone" mask="99 99999-9999" placeholder="00 00000-0000" class="w-full" type="tel" :alwaysShowMask="true" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Valor Pago</label>
                    <InputNumber id="amountPaid" v-model="participantForm.amount_paid" mode="currency" currency="BRL" locale="pt-BR" :minFractionDigits="2" placeholder="0,00" class="w-full" showButtons buttonLayout="horizontal" :step="0.5">
                        <template #incrementicon>
                            <span class="pi pi-plus" />
                        </template>
                        <template #decrementicon>
                            <span class="pi pi-minus" />
                        </template>
                    </InputNumber>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                    <Select
                        v-model="participantForm.status"
                        :options="[
                            { label: 'Pendente', value: 'pendente' },
                            { label: 'Confirmado', value: 'confirmado' },
                            { label: 'Cancelado', value: 'cancelado' }
                        ]"
                        optionLabel="label"
                        optionValue="value"
                        placeholder="Selecione o status"
                        class="w-full"
                    />
                </div>
            </div>

            <template #footer>
                <Button label="Cancelar" icon="pi pi-times" text @click="showDialog = false" />
                <Button
                    :label="isEditMode ? 'Atualizar' : 'Adicionar'"
                    :icon="isEditMode ? 'pi pi-check' : 'pi pi-plus'"
                    @click="saveParticipant"
                    :disabled="
                        !participantForm.participant_type ||
                        (participantForm.participant_type === 'guest' && (!participantForm.name || !participantForm.name.trim())) ||
                        (participantForm.participant_type === 'user' && (!participantForm.user_phone || !participantForm.user_phone.trim()))
                    "
                />
            </template>
        </Dialog>

        <Dialog v-model:visible="showDeleteDialog" header="Confirmar Remoção" modal :style="{ width: '90vw', maxWidth: '400px' }">
            <div class="text-center py-4">
                <i class="pi pi-exclamation-triangle text-4xl text-red-500 mb-4"></i>
                <p class="text-lg text-gray-900 dark:text-gray-100 mb-2">Tem certeza que deseja remover este participante?</p>
                <p v-if="participantToDelete" class="text-gray-600 dark:text-gray-400">
                    Participante: <strong>{{ participantToDelete.name }}</strong>
                </p>
                <p class="text-sm text-gray-500 dark:text-gray-500 mt-4">Esta ação não pode ser desfeita.</p>
            </div>

            <template #footer>
                <Button label="Cancelar" icon="pi pi-times" text @click="showDeleteDialog = false" />
                <Button label="Remover" icon="pi pi-trash" severity="danger" @click="confirmDeleteParticipant" />
            </template>
        </Dialog>

        <TeamDraw :visible="showTeamDraw" @update:visible="showTeamDraw = false" :participants="participantStore.participants" />
    </div>
</template>
