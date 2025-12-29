<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import type { TeamBookingPayload, TeamFormType } from '@/types/athlete/team/teamBooking';
import { useTeamBookingStore } from '@/stores/athlete/team/teamBookingStore';
import { useSportStore } from '@/stores/athlete/select/sportStore';
import { useToast } from 'primevue/usetoast';

interface Props {
    bookingId: number;
    bookingStatus: string;
    sportId?: number;
}

const props = defineProps<Props>();
const teamBookingStore = useTeamBookingStore();
const sportStore = useSportStore();
const toast = useToast();

const showDialog = ref(false);
const submitted = ref(false);
const goalsHome = ref<number | null>(null);
const goalsAway = ref<number | null>(null);
const showResultDialog = ref(false);
const showDeleteConfirmation = ref(false);

const teamForm = ref<TeamFormType>({
    home_team_uuid: '',
    away_team_uuid: '',
    sport_id: props.sportId || null
});

const canManageTeams = computed(() => {
    return !['cancelado', 'pendente'].includes(props.bookingStatus);
});

const canRegisterResult = computed(() => {
    return teamBookingStore.teamBooking && props.bookingStatus === 'concluido' && !teamBookingStore.teamBooking.result;
});

const hasResult = computed(() => {
    return teamBookingStore.teamBooking?.result && teamBookingStore.teamBooking?.goalsHome !== undefined && teamBookingStore.teamBooking?.goalsAway !== undefined;
});

type TeamSide = 'home' | 'away';

const hexToRGBA = (hex: string, alpha = 1): string => {
    if (!hex) {
        return `rgba(156, 163, 175, ${alpha})`;
    }

    let sanitized = hex.replace('#', '');
    if (sanitized.length === 3) {
        sanitized = sanitized
            .split('')
            .map((char) => char + char)
            .join('');
    }

    const bigint = parseInt(sanitized, 16);
    if (Number.isNaN(bigint)) {
        return `rgba(156, 163, 175, ${alpha})`;
    }

    const r = (bigint >> 16) & 255;
    const g = (bigint >> 8) & 255;
    const b = bigint & 255;
    return `rgba(${r}, ${g}, ${b}, ${alpha})`;
};

const primaryFallback: Record<TeamSide, string> = {
    home: '#2563EB',
    away: '#DC2626'
};

const getTeamPrimaryColor = (team: TeamSide): string => {
    const booking = teamBookingStore.teamBooking;
    if (!booking) {
        return primaryFallback[team];
    }

    return team === 'home' ? booking.homeTeam?.primaryColor || primaryFallback.home : booking.awayTeam?.primaryColor || primaryFallback.away;
};

const homeIsWinner = computed(() => {
    const booking = teamBookingStore.teamBooking;
    if (!booking?.winner?.uuid) {
        return false;
    }
    return booking.winner.uuid === booking.homeTeam?.uuid;
});

const awayIsWinner = computed(() => {
    const booking = teamBookingStore.teamBooking;
    if (!booking?.winner?.uuid) {
        return false;
    }
    return booking.winner.uuid === booking.awayTeam?.uuid;
});

const matchOutcomePill = computed(() => {
    const booking = teamBookingStore.teamBooking;

    if (homeIsWinner.value) {
        const color = getTeamPrimaryColor('home');
        return {
            text: `Vitória de ${booking?.homeTeam?.name || 'Time da Casa'}`,
            color,
            background: hexToRGBA(color, 0.15),
            icon: 'pi pi-trophy'
        };
    }

    if (awayIsWinner.value) {
        const color = getTeamPrimaryColor('away');
        return {
            text: `Vitória de ${booking?.awayTeam?.name || 'Time Visitante'}`,
            color,
            background: hexToRGBA(color, 0.15),
            icon: 'pi pi-trophy'
        };
    }

    return {
        text: 'Empate',
        color: '#374151',
        background: 'rgba(107, 114, 128, 0.15)',
        icon: 'pi pi-handshake'
    };
});

const scoreboardAccent = computed(() => {
    return matchOutcomePill.value.color;
});

onMounted(async () => {
    await sportStore.fetchSports();
});

const saveTeamBooking = async (): Promise<void> => {
    submitted.value = true;

    if (!isFormValid()) {
        toast.add({
            severity: 'warn',
            summary: 'Atenção',
            detail: 'Preencha todos os campos obrigatórios',
            life: 5000
        });
        return;
    }

    try {
        const createPayload: TeamBookingPayload = {
            home_team_uuid: teamForm.value.home_team_uuid,
            away_team_uuid: teamForm.value.away_team_uuid
        };

        if (teamForm.value.sport_id) {
            createPayload.sport_id = teamForm.value.sport_id;
        }
        await teamBookingStore.createTeamBooking(props.bookingId, createPayload);
        toast.add({
            severity: 'success',
            summary: 'Sucesso',
            detail: 'Times do jogo adicionados com sucesso',
            life: 5000
        });
        showDialog.value = false;
    } catch {
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: teamBookingStore.error,
            life: 5000
        });
    }
};

const registerResult = async (): Promise<void> => {
    if (!teamBookingStore.teamBooking || goalsHome.value === null || goalsAway.value === null) return;

    try {
        const payload: TeamBookingPayload = {
            goals_home: goalsHome.value,
            goals_away: goalsAway.value
        };

        await teamBookingStore.updateTeamBooking(props.bookingId, payload);

        toast.add({
            severity: 'success',
            summary: 'Sucesso',
            detail: 'Resultado registrado com sucesso',
            life: 5000
        });

        goalsHome.value = null;
        goalsAway.value = null;
        showResultDialog.value = false;
    } catch {
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: teamBookingStore.error,
            life: 5000
        });
    }
};

const deleteTeamBooking = async (): Promise<void> => {
    try {
        await teamBookingStore.deleteTeamBooking(props.bookingId);
        toast.add({
            severity: 'success',
            summary: 'Sucesso',
            detail: 'Times e estatísticas removidos com sucesso',
            life: 5000
        });
        showDeleteConfirmation.value = false;
    } catch {
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: teamBookingStore.error,
            life: 5000
        });
    }
};

const openCreateTeamDialog = () => {
    if (teamBookingStore.teamBooking) {
        return;
    }

    submitted.value = false;
    teamForm.value = {
        home_team_uuid: '',
        away_team_uuid: '',
        sport_id: props.sportId || null
    };
    showDialog.value = true;
};

const openResultDialog = () => {
    showResultDialog.value = true;
};

const isFormValid = (): boolean => {
    return !!(teamForm.value.home_team_uuid?.trim() && teamForm.value.away_team_uuid?.trim());
};
</script>

<template>
    <div class="space-y-6">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
            <div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Times do Jogo</h3>
                <div class="mt-3 p-3 border border-blue-200 dark:border-blue-700 rounded-lg bg-blue-50 dark:bg-blue-900/20">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="pi pi-info-circle text-blue-600 dark:text-blue-400"></i>
                        <span class="text-sm font-medium text-blue-800 dark:text-blue-200">Importante</span>
                    </div>
                    <p class="text-sm text-blue-700 dark:text-blue-300">Defina os times que participarão do jogo. Após adicionar os times, você não poderá mais editá-los, apenas registrar o resultado do jogo ou deletar o jogo completamente.</p>
                </div>
            </div>

            <div v-if="canManageTeams || canRegisterResult" class="flex gap-3">
                <Button v-if="canManageTeams && !teamBookingStore.teamBooking" label="Adicionar Times" icon="pi pi-plus" @click="openCreateTeamDialog" class="px-4 py-2" />
                <Button v-if="canRegisterResult" label="Registrar Resultado" icon="pi pi-save" @click="openResultDialog" class="px-4 py-2" />
                <Button v-if="teamBookingStore.teamBooking && canManageTeams" label="Deletar Jogo" icon="pi pi-trash" severity="danger" @click="showDeleteConfirmation = true" class="px-4 py-2" />
            </div>

            <div v-else class="text-sm text-gray-500 dark:text-gray-400">Times só podem ser gerenciados em reservas confirmadas ou concluídas</div>
        </div>

        <div v-if="teamBookingStore.teamBooking" class="space-y-6">
            <div v-if="teamBookingStore.teamBooking.sport" class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-white dark:bg-gray-800">
                <div class="flex items-center gap-2">
                    <i class="pi pi-trophy text-primary"></i>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Esporte:</span>
                    <span class="text-sm text-gray-900 dark:text-gray-100">{{ teamBookingStore.teamBooking.sport.name }}</span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-stretch">
                <div class="relative rounded-xl p-6 bg-white dark:bg-gray-800 shadow-sm border-l-8 transition" :style="{ borderLeftColor: getTeamPrimaryColor('home') }">
                    <span class="absolute top-4 right-4 text-xs uppercase tracking-wide text-gray-400"> Casa </span>

                    <div class="flex items-center gap-4">
                        <img :src="teamBookingStore.teamBooking.homeTeam?.shieldPath || '/image/logo.png'" alt="Escudo" class="w-14 h-14 object-contain" />

                        <div>
                            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 leading-tight">
                                {{ teamBookingStore.teamBooking.homeTeam?.name || '—' }}
                            </p>

                            <p v-if="teamBookingStore.teamBooking.homeTeam?.nickname" class="text-sm text-gray-500 dark:text-gray-400">
                                {{ teamBookingStore.teamBooking.homeTeam.nickname }}
                            </p>
                        </div>
                    </div>

                    <p v-if="teamBookingStore.teamBooking.homeTeam?.stadiumName" class="mt-4 text-sm text-gray-500 dark:text-gray-400">🏟 {{ teamBookingStore.teamBooking.homeTeam.stadiumName }}</p>

                    <div v-if="hasResult" class="mt-6 flex items-baseline gap-2">
                        <span class="text-4xl font-black text-gray-900 dark:text-gray-100">{{ teamBookingStore.teamBooking?.goalsHome }}</span>
                        <span class="text-xs uppercase tracking-widest text-gray-400">gols</span>
                    </div>
                </div>

                <div class="flex flex-col items-center justify-center gap-3">
                    <div class="w-24 h-24 rounded-full flex flex-col items-center justify-center text-center shadow-inner" :style="{ backgroundColor: hexToRGBA(scoreboardAccent, hasResult ? 0.1 : 0.05) }">
                        <template v-if="hasResult">
                            <span class="text-xs uppercase tracking-[0.35em] text-gray-400">Final</span>
                            <span class="text-3xl font-black" :style="{ color: scoreboardAccent }">{{ teamBookingStore.teamBooking?.goalsHome }}-{{ teamBookingStore.teamBooking?.goalsAway }}</span>
                        </template>
                        <template v-else>
                            <span class="text-3xl font-extrabold text-gray-500 dark:text-gray-300">VS</span>
                        </template>
                    </div>
                    <div v-if="hasResult" class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold tracking-wide" :style="{ backgroundColor: matchOutcomePill.background, color: matchOutcomePill.color }">
                        <i :class="matchOutcomePill.icon"></i>
                        <span>{{ matchOutcomePill.text }}</span>
                    </div>
                    <p v-else class="text-xs uppercase text-gray-400 tracking-widest">vs</p>
                </div>

                <div class="relative rounded-xl p-6 bg-white dark:bg-gray-800 shadow-sm border-r-8 transition text-right" :style="{ borderRightColor: getTeamPrimaryColor('away') }">
                    <span class="absolute top-4 left-4 text-xs uppercase tracking-wide text-gray-400"> Visitante </span>

                    <div class="flex items-center justify-end gap-4">
                        <div>
                            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 leading-tight">
                                {{ teamBookingStore.teamBooking.awayTeam?.name || '—' }}
                            </p>

                            <p v-if="teamBookingStore.teamBooking.awayTeam?.nickname" class="text-sm text-gray-500 dark:text-gray-400">
                                {{ teamBookingStore.teamBooking.awayTeam.nickname }}
                            </p>
                        </div>

                        <img :src="teamBookingStore.teamBooking.awayTeam?.shieldPath || '/image/logo.png'" alt="Escudo" class="w-14 h-14 object-contain" />
                    </div>

                    <p v-if="teamBookingStore.teamBooking.awayTeam?.stadiumName" class="mt-4 text-sm text-gray-500 dark:text-gray-400">🏟 {{ teamBookingStore.teamBooking.awayTeam.stadiumName }}</p>

                    <div v-if="hasResult" class="mt-6 flex items-baseline justify-end gap-2">
                        <span class="text-4xl font-black text-gray-900 dark:text-gray-100">{{ teamBookingStore.teamBooking?.goalsAway }}</span>
                        <span class="text-xs uppercase tracking-widest text-gray-400">gols</span>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="!teamBookingStore.teamBooking" class="text-center py-12 px-4">
            <div class="bg-gray-100 dark:bg-gray-800 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                <i class="pi pi-shield text-2xl text-gray-400 dark:text-gray-500"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Nenhum time definido</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                <span v-if="canManageTeams">Clique em "Adicionar Times" para definir os times que participarão do jogo.</span>
                <span v-else>Os times serão exibidos quando a reserva for confirmada.</span>
            </p>
        </div>

        <Dialog v-model:visible="showResultDialog" header="Registrar Resultado" modal :style="{ width: '50vw' }" :breakpoints="{ '960px': '75vw', '640px': '100vw' }" :maximizable="true">
            <div class="space-y-4 pt-4">
                <div class="text-center mb-6">
                    <div class="grid grid-cols-3 gap-4 items-center">
                        <div class="text-center">
                            <img :src="teamBookingStore.teamBooking?.homeTeam?.shieldPath || '/image/logo.png'" alt="Escudo Casa" class="w-12 h-12 object-contain mx-auto mb-2" />
                            <p class="font-semibold text-lg">{{ teamBookingStore.teamBooking?.homeTeam?.name || 'Time da Casa' }}</p>
                            <p v-if="teamBookingStore.teamBooking?.homeTeam?.nickname" class="text-sm text-gray-600 dark:text-gray-400">{{ teamBookingStore.teamBooking.homeTeam.nickname }}</p>
                        </div>
                        <div class="text-center">
                            <p class="text-gray-500 dark:text-gray-400">vs</p>
                        </div>
                        <div class="text-center">
                            <img :src="teamBookingStore.teamBooking?.awayTeam?.shieldPath || '/image/logo.png'" alt="Escudo Visitante" class="w-12 h-12 object-contain mx-auto mb-2" />
                            <p class="font-semibold text-lg">{{ teamBookingStore.teamBooking?.awayTeam?.name || 'Time Visitante' }}</p>
                            <p v-if="teamBookingStore.teamBooking?.awayTeam?.nickname" class="text-sm text-gray-600 dark:text-gray-400">{{ teamBookingStore.teamBooking.awayTeam.nickname }}</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Placar {{ teamBookingStore.teamBooking?.homeTeam?.name || 'Time da Casa' }}</label>
                        <InputNumber v-model="goalsHome" :min="0" placeholder="0" class="w-full" showButtons buttonLayout="horizontal" :step="1">
                            <template #incrementicon>
                                <span class="pi pi-plus" />
                            </template>
                            <template #decrementicon>
                                <span class="pi pi-minus" />
                            </template>
                        </InputNumber>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Placar {{ teamBookingStore.teamBooking?.awayTeam?.name || 'Time Visitante' }}</label>
                        <InputNumber v-model="goalsAway" :min="0" placeholder="0" class="w-full" showButtons buttonLayout="horizontal" :step="1">
                            <template #incrementicon>
                                <span class="pi pi-plus" />
                            </template>
                            <template #decrementicon>
                                <span class="pi pi-minus" />
                            </template>
                        </InputNumber>
                    </div>
                </div>
            </div>

            <template #footer>
                <div class="flex gap-3 justify-end">
                    <Button label="Cancelar" severity="secondary" @click="showResultDialog = false" />
                    <Button label="Registrar" @click="registerResult" :disabled="goalsHome === null || goalsAway === null" />
                </div>
            </template>
        </Dialog>

        <Dialog v-model:visible="showDialog" header="Adicionar Times do Jogo" modal :style="{ width: '50vw' }" :breakpoints="{ '960px': '75vw', '640px': '100vw' }" :maximizable="true">
            <div class="space-y-4 pt-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"> Esporte </label>
                    <Select v-model="teamForm.sport_id" :options="sportStore.sportOptions" optionLabel="label" optionValue="value" placeholder="Selecione o esporte" class="w-full" :disabled="!!props.sportId" />
                    <small v-if="props.sportId" class="text-gray-500 dark:text-gray-400"> Esporte pré-definido pela reserva </small>
                </div>

                <div class="space-y-4">
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Time da Casa</h4>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"> ID Público do Time da Casa <span class="text-red-500">*</span> </label>
                        <InputText v-model="teamForm.home_team_uuid" placeholder="Digite id público do time" class="w-full" :class="{ 'p-invalid': submitted && !teamForm.home_team_uuid }" />
                    </div>

                    <div>
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Time Visitante</h4>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"> ID Público do Time Visitante <span class="text-red-500">*</span> </label>
                        <InputText v-model="teamForm.away_team_uuid" placeholder="Digite id público do time" class="w-full" :class="{ 'p-invalid': submitted && !teamForm.away_team_uuid }" />
                    </div>
                </div>
            </div>

            <template #footer>
                <div class="flex gap-3 justify-end">
                    <Button label="Cancelar" severity="secondary" @click="showDialog = false" />
                    <Button label="Adicionar" @click="saveTeamBooking" :disabled="!isFormValid()" />
                </div>
            </template>
        </Dialog>

        <Dialog v-model:visible="showDeleteConfirmation" header="Confirmar Exclusão" modal :style="{ width: '90vw', maxWidth: '400px' }">
            <div class="pt-4">
                <div class="flex items-center gap-3 mb-4">
                    <i class="pi pi-exclamation-triangle text-3xl text-orange-500"></i>
                    <div>
                        <p class="font-medium text-gray-900 dark:text-gray-100">Tem certeza que deseja deletar este jogo?</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Esta ação removerá os times e todas as estatísticas relacionadas. Esta ação não pode ser desfeita.</p>
                    </div>
                </div>
            </div>

            <template #footer>
                <div class="flex gap-3 justify-end">
                    <Button label="Cancelar" severity="secondary" @click="showDeleteConfirmation = false" />
                    <Button label="Confirmar Exclusão" severity="danger" @click="deleteTeamBooking" />
                </div>
            </template>
        </Dialog>
    </div>
</template>
