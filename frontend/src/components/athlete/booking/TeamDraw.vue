<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { useToast } from 'primevue/usetoast';
import type { BookingParticipant } from '@/types/athlete/booking/bookingParticipant';

interface Props {
    participants: BookingParticipant[];
    visible: boolean;
}

interface Emits {
    (e: 'update:visible', value: boolean): void;
    (e: 'teams-drawn', teams: BookingParticipant[][]): void;
}

const props = defineProps<Props>();
const emit = defineEmits<Emits>();
const toast = useToast();

const config = ref({
    teamsCount: 2,
    playersPerTeam: 6
});

const manualTeams = ref<{ [key: number]: BookingParticipant[] }>({});
const drawnTeams = ref<BookingParticipant[][]>([]);
const showResultModal = ref(false);
const showInstructions = ref(false);

const availableParticipants = computed(() => {
    return props.participants.filter((p) => p.status === 'confirmado');
});

const manuallyAllocatedPlayers = computed(() => {
    const players: BookingParticipant[] = [];
    Object.values(manualTeams.value).forEach((team) => {
        players.push(...team);
    });
    return players;
});

const remainingPlayers = computed(() => {
    return availableParticipants.value.filter((p) => !manuallyAllocatedPlayers.value.some((mp) => mp.id === p.id));
});

const validation = computed(() => {
    const totalParticipants = availableParticipants.value.length;
    const minRequiredPlayers = config.value.teamsCount;
    const remainingAfterFull = totalParticipants - config.value.teamsCount * config.value.playersPerTeam;

    if (config.value.teamsCount < 2) {
        return { valid: false, message: 'É necessário pelo menos 2 times.', type: 'error' };
    }

    if (config.value.playersPerTeam < 1) {
        return { valid: false, message: 'É necessário pelo menos 1 jogador por time.', type: 'error' };
    }

    if (totalParticipants < 4) {
        return { valid: false, message: 'É necessário pelo menos 4 participantes confirmados.', type: 'error' };
    }

    if (totalParticipants < minRequiredPlayers) {
        return { valid: false, message: `Participantes insuficientes. Necessário pelo menos: ${minRequiredPlayers}, Disponível: ${totalParticipants}`, type: 'error' };
    }

    if (remainingAfterFull > 0) {
        return { valid: true, message: `${remainingAfterFull} jogador(es) ficarão no "Time de Fora"`, type: 'info' };
    }

    return { valid: true, message: 'Distribuição perfeita para todos os times!', type: 'success' };
});

watch(
    () => props.visible,
    (newVisible) => {
        if (!newVisible) {
            resetDraw();
        }
    }
);

const resetDraw = (): void => {
    config.value = {
        teamsCount: 2,
        playersPerTeam: 6
    };
    manualTeams.value = {};
    drawnTeams.value = [];
    showResultModal.value = false;
    showInstructions.value = false;
};

const addPlayerToManualTeam = (player: BookingParticipant, teamIndex: number): void => {
    if (!manualTeams.value[teamIndex]) {
        manualTeams.value[teamIndex] = [];
    }

    Object.keys(manualTeams.value).forEach((key) => {
        const index = parseInt(key);
        manualTeams.value[index] = manualTeams.value[index].filter((p) => p.id !== player.id);
    });

    const currentTeam = manualTeams.value[teamIndex];

    if (currentTeam.length < config.value.playersPerTeam) {
        manualTeams.value[teamIndex].push(player);
    } else {
        toast.add({
            severity: 'warn',
            summary: 'Time Completo',
            detail: `Este time já possui ${config.value.playersPerTeam} jogadores. O jogador ficará no Time de Fora durante o sorteio.`,
            life: 4000
        });
    }
};

const removePlayerFromManualTeam = (player: BookingParticipant, teamIndex: number): void => {
    if (manualTeams.value[teamIndex]) {
        manualTeams.value[teamIndex] = manualTeams.value[teamIndex].filter((p) => p.id !== player.id);
    }
};

const clearTeam = (teamIndex: number): void => {
    if (manualTeams.value[teamIndex]) {
        manualTeams.value[teamIndex] = [];
        toast.add({
            severity: 'info',
            summary: 'Time Limpo',
            detail: `${getTeamName(teamIndex)} foi limpo com sucesso.`,
            life: 2000
        });
    }
};

const clearAllTeams = (): void => {
    manualTeams.value = {};
    toast.add({
        severity: 'info',
        summary: 'Todos os Times Limpos',
        detail: 'Configuração manual foi resetada.',
        life: 2000
    });
};

const drawTeams = (): void => {
    if (!validation.value.valid) {
        toast.add({
            severity: validation.value.type === 'error' ? 'error' : 'warn',
            summary: 'Configuração Inválida',
            detail: validation.value.message,
            life: 5000
        });
        return;
    }

    try {
        const teams: BookingParticipant[][] = [];
        const benchTeam: BookingParticipant[] = [];

        for (let i = 0; i < config.value.teamsCount; i++) {
            teams[i] = [...(manualTeams.value[i] || [])];
        }

        const remainingPlayers = availableParticipants.value.filter((p) => !manuallyAllocatedPlayers.value.some((mp) => mp.id === p.id));
        const shuffledPlayers = remainingPlayers.sort(() => Math.random() - 0.5);

        shuffledPlayers.forEach((player) => {
            let targetTeam = 0;

            for (let i = 1; i < teams.length; i++) {
                if (teams[i].length < teams[targetTeam].length && teams[i].length < config.value.playersPerTeam) {
                    targetTeam = i;
                } else if (teams[targetTeam].length >= config.value.playersPerTeam && teams[i].length < config.value.playersPerTeam) {
                    targetTeam = i;
                }
            }

            if (teams[targetTeam].length < config.value.playersPerTeam) {
                teams[targetTeam].push(player);
            } else {
                benchTeam.push(player);
            }
        });

        if (benchTeam.length > 0) {
            teams.push(benchTeam);
        }

        drawnTeams.value = teams;
        showResultModal.value = true;

        emit('teams-drawn', teams);
    } catch {
        toast.add({
            severity: 'error',
            summary: 'Erro no Sorteio',
            detail: 'Ocorreu um erro ao sortear os times',
            life: 5000
        });
    }
};

const getTeamName = (index: number): string => {
    if (index >= config.value.teamsCount) {
        return 'Time de Fora';
    }
    const teamNames = ['Time A', 'Time B', 'Time C', 'Time D', 'Time E', 'Time F'];
    return teamNames[index] || `Time ${index + 1}`;
};

const generateTeamsText = (): string => {
    let text = `*Times Sorteados*\n`;
    text += `${new Date().toLocaleDateString('pt-BR', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}\n\n`;

    drawnTeams.value.forEach((team, index) => {
        if (index < config.value.teamsCount) {
            text += `*${getTeamName(index)}*\n`;
            team.forEach((player) => {
                text += `• ${player.name}${player.positionName ? ` (${player.positionName})` : ''}\n`;
            });
            text += `\n`;
        }
    });

    if (drawnTeams.value.length > config.value.teamsCount && drawnTeams.value[config.value.teamsCount].length > 0) {
        text += `*⏳ Time de Fora*\n`;
        drawnTeams.value[config.value.teamsCount].forEach((player) => {
            text += `• ${player.name}${player.positionName ? ` (${player.positionName})` : ''}\n`;
        });
    }

    return text;
};

const sendToWhatsApp = (): void => {
    const message = generateTeamsText();
    const encodedMessage = encodeURIComponent(message);
    const whatsappUrl = `https://wa.me/?text=${encodedMessage}`;

    window.open(whatsappUrl, '_blank');

    toast.add({
        severity: 'success',
        summary: 'WhatsApp Aberto',
        detail: 'Selecione o contato ou grupo para enviar os times',
        life: 4000
    });
};

const closeDialog = (): void => {
    emit('update:visible', false);
};
</script>

<template>
    <Dialog :visible="visible" @update:visible="closeDialog" header="Sorteio de Times" modal :style="{ width: '50vw' }" :breakpoints="{ '960px': '75vw', '640px': '100vw' }" :maximizable="true">
        <div class="space-y-6 pt-4">
            <div v-if="showInstructions" class="border border-primary-200 dark:border-primary-800 rounded-lg p-4">
                <h5 class="font-medium text-primary-800 dark:text-primary-200 mb-3">Como funciona o sorteio:</h5>
                <div class="space-y-2 text-sm text-primary-700 dark:text-primary-300">
                    <p><strong>1. Configuração:</strong> Defina quantos times e quantos jogadores por time.</p>
                    <p><strong>2. Distribuição Manual:</strong> Escolha jogadores específicos para cada time (opcional).</p>
                    <p><strong>3. Sorteio Automático:</strong> Jogadores restantes são distribuídos aleatoriamente.</p>
                    <p><strong>4. Balanceamento:</strong> Sistema equilibra jogadores por time automaticamente.</p>
                    <p><strong>5. Time de Fora:</strong> Jogadores extras ficam no "Time de Fora" para próxima rodada.</p>
                </div>
                <div class="mt-3 p-3 rounded border border-yellow-200 dark:border-yellow-800">
                    <p class="text-yellow-800 dark:text-yellow-200 text-xs"><strong>Dica:</strong> Deixe alguns jogadores sem alocar manualmente para que sejam sorteados automaticamente, garantindo maior aleatoriedade.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Número de Times *</label>
                    <InputNumber id="teamsCount" v-model="config.teamsCount" :min="2" :max="10" showButtons buttonLayout="horizontal" :step="1" fluid>
                        <template #incrementicon>
                            <span class="pi pi-plus" />
                        </template>
                        <template #decrementicon>
                            <span class="pi pi-minus" />
                        </template>
                    </InputNumber>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Jogadores por Time *</label>
                    <InputNumber id="playersPerTeam" v-model="config.playersPerTeam" :min="1" :max="15" showButtons buttonLayout="horizontal" :step="1" fluid>
                        <template #incrementicon>
                            <span class="pi pi-plus" />
                        </template>
                        <template #decrementicon>
                            <span class="pi pi-minus" />
                        </template>
                    </InputNumber>
                </div>
            </div>

            <div class="p-4 rounded-lg">
                <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-3">Status dos Participantes</h4>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                    <div class="flex items-center gap-2">
                        <span class="text-gray-600 dark:text-gray-400">Total:</span>
                        <span class="font-semibold">{{ availableParticipants.length }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-gray-600 dark:text-gray-400">Necessário:</span>
                        <span class="font-semibold">{{ config.teamsCount * config.playersPerTeam }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-gray-600 dark:text-gray-400">Sobram:</span>
                        <span class="font-semibold">{{ Math.max(0, availableParticipants.length - config.teamsCount * config.playersPerTeam) }}</span>
                    </div>
                </div>

                <div
                    v-if="validation.message"
                    class="mt-3 p-3 rounded"
                    :class="{
                        'border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/10': validation.type === 'error',
                        'border border-yellow-200 dark:border-yellow-800 bg-yellow-50 dark:bg-yellow-900/10': validation.type === 'warn',
                        'border border-blue-200 dark:border-blue-800 bg-blue-50 dark:bg-blue-900/10': validation.type === 'info',
                        'border border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-900/10': validation.type === 'success'
                    }"
                >
                    <div
                        class="flex items-center gap-2"
                        :class="{
                            'text-red-800 dark:text-red-200': validation.type === 'error',
                            'text-yellow-800 dark:text-yellow-200': validation.type === 'warn',
                            'text-blue-800 dark:text-blue-200': validation.type === 'info',
                            'text-green-800 dark:text-green-200': validation.type === 'success'
                        }"
                    >
                        <i
                            class="pi"
                            :class="{
                                'pi-exclamation-triangle': validation.type === 'error' || validation.type === 'warn',
                                'pi-info-circle': validation.type === 'info',
                                'pi-check-circle': validation.type === 'success'
                            }"
                        ></i>
                        <span class="text-sm font-medium">{{ validation.message }}</span>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h4 class="font-medium text-gray-900 dark:text-gray-100">Configuração Manual</h4>
                    <Button
                        v-if="Object.values(manualTeams).some((team: BookingParticipant[]) => team && team.length > 0)"
                        label="Limpar Todos"
                        icon="pi pi-refresh"
                        severity="secondary"
                        size="small"
                        @click="clearAllTeams"
                        v-tooltip.top="'Limpar todos os times'"
                        class="text-xs"
                    />
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-3 h-fit">
                        <h5 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Jogadores Disponíveis</h5>
                        <div class="max-h-[calc(100vh-29rem)] overflow-y-auto space-y-1">
                            <div v-if="remainingPlayers.length === 0" class="text-gray-400 text-sm italic py-4 text-center">Todos os jogadores foram alocados</div>
                            <div v-else class="space-y-1">
                                <div v-for="player in remainingPlayers" :key="player.id" class="flex items-center justify-between px-2 py-2 rounded text-sm transition-colors">
                                    <div class="flex items-center gap-2">
                                        <div>
                                            <span class="font-medium">{{ player.name }}</span>
                                            <span v-if="player.positionName" class="text-gray-500 ml-1 text-xs">({{ player.positionName }})</span>
                                        </div>
                                    </div>
                                    <Select
                                        :options="Array.from({ length: config.teamsCount }, (_, i) => ({ label: getTeamName(i), value: i }))"
                                        optionLabel="label"
                                        optionValue="value"
                                        placeholder="Time"
                                        @change="(event) => addPlayerToManualTeam(player, event.value)"
                                        class="text-xs w-24"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3 sticky top-4 h-fit max-h-[calc(100vh-8rem)] overflow-y-auto">
                        <h5 class="text-sm font-medium text-gray-700 dark:text-gray-300">Times Configurados</h5>
                        <div v-for="teamIndex in config.teamsCount" :key="teamIndex" class="border border-gray-200 dark:border-gray-700 rounded-lg p-3">
                            <div class="flex items-center justify-between mb-2">
                                <h6 class="font-medium text-gray-900 dark:text-gray-100">{{ getTeamName(teamIndex - 1) }}</h6>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-gray-500">{{ manualTeams[teamIndex - 1]?.length || 0 }}/{{ config.playersPerTeam }}</span>
                                    <Button
                                        v-if="manualTeams[teamIndex - 1]?.length > 0"
                                        icon="pi pi-trash"
                                        severity="secondary"
                                        text
                                        size="small"
                                        @click="clearTeam(teamIndex - 1)"
                                        v-tooltip.top="'Limpar time'"
                                        class="p-1 w-5 h-5 text-gray-400 hover:text-red-500"
                                    />
                                </div>
                            </div>
                            <div class="min-h-[60px] space-y-1">
                                <div v-if="!manualTeams[teamIndex - 1] || manualTeams[teamIndex - 1].length === 0" class="text-gray-400 text-sm italic py-2 text-center border-2 border-dashed border-gray-200 dark:border-gray-700 rounded">
                                    <div class="space-y-1">
                                        <div class="text-xs font-medium text-gray-500 dark:text-gray-400">Cabeças de Time (Opcional)</div>
                                        <div class="text-xs">Selecione jogadores específicos para liderar cada time</div>
                                        <div class="text-xs text-primary-600 dark:text-primary-400 mt-1">Os demais poderão ser sorteados automaticamente</div>
                                    </div>
                                </div>
                                <div v-else class="space-y-1">
                                    <div
                                        v-for="player in manualTeams[teamIndex - 1]"
                                        :key="player.id"
                                        class="flex items-center justify-between text-primary-700 dark:text-primary-300 px-2 py-1 rounded text-sm bg-primary-50 dark:bg-primary-900/20 hover:bg-primary-100 dark:hover:bg-primary-900/30 transition-colors"
                                    >
                                        <div class="flex items-center gap-2">
                                            <span class="font-medium">{{ player.name }}</span>
                                            <span v-if="player.positionName" class="text-xs opacity-75">({{ player.positionName }})</span>
                                        </div>
                                        <Button
                                            icon="pi pi-times"
                                            severity="danger"
                                            text
                                            size="small"
                                            @click="removePlayerFromManualTeam(player, teamIndex - 1)"
                                            v-tooltip.top="'Remover do time'"
                                            class="p-1 w-6 h-6 hover:bg-red-100 dark:hover:bg-red-900/30"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <template #footer>
            <Button label="Cancelar" text @click="closeDialog" />
            <Button label="Sortear Times" severity="primary" @click="drawTeams" />
        </template>
    </Dialog>

    <Dialog :visible="showResultModal" @update:visible="showResultModal = false" header="Times Sorteados" modal :style="{ width: '95vw', maxWidth: '1000px' }" :maximizable="true">
        <div class="space-y-4">
            <div class="bg-primary-50 dark:bg-primary-900/20 border border-primary-200 dark:border-primary-800 rounded-lg p-4">
                <div class="flex items-center gap-3">
                    <i class="pi pi-camera text-primary-600 dark:text-primary-400 text-lg"></i>
                    <div class="flex-1">
                        <h5 class="font-medium text-primary-800 dark:text-primary-200 mb-1">Capture os Times</h5>
                        <p class="text-sm text-primary-700 dark:text-primary-300">Tire um print desta tela para salvar os times sorteados.</p>
                    </div>
                </div>
            </div>

            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <i class="pi pi-send text-green-600 dark:text-green-400 text-lg"></i>
                        <div class="flex-1">
                            <h5 class="font-medium text-green-800 dark:text-green-200 mb-1">Compartilhar no WhatsApp</h5>
                            <p class="text-sm text-green-700 dark:text-green-300">Envie os times para um contato ou grupo do WhatsApp.</p>
                        </div>
                    </div>
                    <Button icon="pi pi-whatsapp" label="Enviar" severity="success" size="small" @click="sendToWhatsApp" class="shrink-0" />
                </div>
            </div>

            <div class="bg-white dark:bg-gray-900 p-6 rounded-lg">
                <div class="text-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">Times Sorteados</h2>
                    <p class="text-gray-600 dark:text-gray-400">{{ new Date().toLocaleDateString('pt-BR') }}</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div v-for="(team, index) in drawnTeams.slice(0, config.teamsCount)" :key="index" class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-white dark:bg-gray-800">
                        <div class="flex items-center justify-center gap-2 mb-3">
                            <i class="pi pi-users text-primary-600 dark:text-primary-400 text-lg"></i>
                            <h5 class="font-semibold text-gray-900 dark:text-gray-100 text-center text-lg border-b border-gray-200 dark:border-gray-700 pb-2">
                                {{ getTeamName(index) }}
                            </h5>
                        </div>
                        <div class="space-y-2">
                            <div v-for="player in team" :key="player.id" class="flex items-center justify-between text-sm p-2 bg-white dark:bg-gray-800 rounded shadow-sm">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-primary/10 border border-primary/20 flex items-center justify-center overflow-hidden flex-shrink-0">
                                        <img v-if="player.imagePath" :src="player.imagePath" :alt="player.name" class="w-full h-full object-cover" />
                                        <i v-else class="pi pi-user text-primary text-xs"></i>
                                    </div>
                                    <span class="font-medium">{{ player.name }}</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <span v-if="player.positionName" class="text-gray-500 text-xs">{{ player.positionName }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 text-center text-xs text-gray-500 border-t border-gray-200 dark:border-gray-700 pt-2">Total: {{ team.length }} jogadores</div>
                    </div>
                </div>

                <div v-if="drawnTeams.length > config.teamsCount && drawnTeams[config.teamsCount].length > 0" class="mt-6">
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-white dark:bg-gray-800">
                        <div class="flex items-center justify-center gap-2 mb-3">
                            <i class="pi pi-users text-primary"></i>
                            <h5 class="font-semibold text-gray-900 dark:text-gray-100 text-lg">Time de Fora</h5>
                        </div>
                        <p class="text-center text-gray-700 dark:text-gray-300 text-sm mb-4">Jogadores que ficarão de fora nesta rodada ({{ drawnTeams[config.teamsCount].length }} pessoa{{ drawnTeams[config.teamsCount].length > 1 ? 's' : '' }})</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2">
                            <div v-for="player in drawnTeams[config.teamsCount]" :key="player.id" class="flex items-center justify-between text-sm p-3 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-primary/10 border border-primary/20 flex items-center justify-center overflow-hidden flex-shrink-0">
                                        <img v-if="player.imagePath" :src="player.imagePath" :alt="player.name" class="w-full h-full object-cover" />
                                        <i v-else class="pi pi-user text-primary text-xs"></i>
                                    </div>
                                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ player.name }}</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <span v-if="player.positionName" class="text-gray-500 text-xs">{{ player.positionName }}</span>
                                    <i class="pi pi-clock text-primary text-xs" title="Próxima rodada"></i>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 text-center">
                            <div class="inline-flex items-center gap-2 text-xs text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-900/30 px-3 py-1 rounded-full">
                                <i class="pi pi-lightbulb text-primary"></i>
                                <span>Sugestão: Estes jogadores entram na próxima rodada</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <template #footer>
            <Button label="Fechar" @click="showResultModal = false" />
        </template>
    </Dialog>
</template>
