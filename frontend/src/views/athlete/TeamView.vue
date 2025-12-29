<script setup lang="ts">
import { ref, computed, watch, onMounted } from 'vue';
import { useToast } from 'primevue/usetoast';
import { useAuthStore } from '@/stores/auth/loginStore';
import { useTeamStore } from '@/stores/athlete/team/teamStore';
import { useTeamPlayerStore } from '@/stores/athlete/team/teamPlayerStore';
import { useSportStore } from '@/stores/athlete/select/sportStore';
import { useTeamTypeStore } from '@/stores/athlete/select/teamTypeStore';
import type { Team, TeamPayload } from '@/types/athlete/team/team';
import type { TeamPlayer, PayloadTeamPlayerData } from '@/types/athlete/team/teamPlayer';
import type { FileUploadSelectEvent } from 'primevue/fileupload';
import { Select } from 'primevue';

const toast = useToast();
const authStore = useAuthStore();
const teamStore = useTeamStore();
const teamPlayerStore = useTeamPlayerStore();
const sportStore = useSportStore();
const teamTypeStore = useTeamTypeStore();

const isLoading = ref(true);
const isLoadingPlayers = ref(false);
const isUploading = ref(false);
const showEditModal = ref(false);
const showPlayerModal = ref(false);
const showImageDialog = ref(false);
const showLeaveDialog = ref(false);
const showDeleteDialog = ref(false);
const showRemovePlayerDialog = ref(false);
const isCreating = ref(false);
const isCreatingPlayer = ref(false);
const isEditingPlayer = ref(false);
const submitted = ref(false);
const playerSubmitted = ref(false);
const team = ref<Team | null>(null);
const teamPlayers = ref<TeamPlayer[]>([]);
const selectedPlayer = ref<TeamPlayer | null>(null);
const selectedImage = ref('');
const playerToRemove = ref<TeamPlayer | null>(null);

const playerForm = ref<Partial<TeamPlayer> & { identifierType?: 'user_phone' | 'public_id'; identifierValue?: string }>({});
const editForm = ref({
    name: '',
    nickname: undefined as string | undefined,
    description: undefined as string | undefined,
    welcomeEmail: undefined as string | undefined,
    stadiumName: undefined as string | undefined,
    primaryColor: undefined as string | undefined,
    secondaryColor: undefined as string | undefined,
    website: undefined as string | undefined,
    foundedDate: '',
    maxMembers: 30,
    isPublic: false,
    sport: { id: 0, name: '' },
    teamType: { id: 0, name: '' }
});

const teamStats = computed(() => teamStore.teamStats);
const currentUserId = computed(() => authStore.user?.uuid);

const goalTerm = computed(() => {
    const sportName = team.value?.sport?.name;
    if (sportName === 'Futebol') return 'Gols';
    if (sportName === 'Basquete') return 'Pontos';
    return 'Sets';
});

const scoredSuffix = computed(() => (goalTerm.value === 'Sets' ? 'Ganhos' : 'Marcados'));
const concededSuffix = computed(() => (goalTerm.value === 'Sets' ? 'Perdidos' : 'Sofridos'));

const statsArray = computed(() => {
    if (!teamStats.value) return [];
    return [
        { name: 'Jogos', value: teamStats.value.matches, icon: 'pi pi-play', color: 'purple' },
        { name: 'Vitórias', value: teamStats.value.wins, icon: 'pi pi-check-circle', color: 'green' },
        { name: 'Derrotas', value: teamStats.value.losses, icon: 'pi pi-times-circle', color: 'red' },
        { name: 'Empates', value: teamStats.value.draws, icon: 'pi pi-circle', color: 'yellow' },
        { name: `${goalTerm.value} ${scoredSuffix.value}`, value: teamStats.value.goalScored, icon: 'pi pi-arrow-up', color: 'blue' },
        { name: `${goalTerm.value} ${concededSuffix.value}`, value: teamStats.value.goalConceded, icon: 'pi pi-arrow-down', color: 'orange' }
    ];
});

const roleOptions = [
    { label: 'Jogador(a)', value: 'jogador' },
    { label: 'Capitão(ã)', value: 'capitao' },
    { label: 'Treinador(a)', value: 'treinador' }
];

const formatDateToISO = (dateStr: string): string => {
    if (!dateStr) return '';
    const parts = dateStr.split('/');
    if (parts.length !== 3) return '';
    return `${parts[2]}-${parts[1].padStart(2, '0')}-${parts[0].padStart(2, '0')}`;
};

const loadMyTeam = async () => {
    try {
        isLoading.value = true;
        const teamData = await teamStore.fetchMyTeam();
        team.value = teamData;
        if (teamData?.id) {
            await loadTeamPlayers(teamData.id);
            await loadTeamStats(teamData.id);
            await loadTeamDepartures(teamData.id);
        }
    } catch {
        team.value = null;
        teamPlayers.value = [];
        if (teamStore.error && !teamStore.error.includes('não possui um time') && !teamStore.error.includes('Time não encontrado.')) {
            toast.add({ severity: 'error', summary: 'Erro', detail: teamStore.error, life: 3000 });
        }
    } finally {
        isLoading.value = false;
    }
};

const loadTeamPlayers = async (teamId: number) => {
    try {
        isLoadingPlayers.value = true;
        await teamPlayerStore.fetchTeamPlayers(teamId, {});
        teamPlayers.value = teamPlayerStore.teamPlayers;
    } catch {
        teamPlayers.value = [];
        if (teamPlayerStore.error) {
            toast.add({ severity: 'error', summary: 'Erro', detail: teamPlayerStore.error, life: 3000 });
        }
    } finally {
        isLoadingPlayers.value = false;
    }
};

const loadTeamStats = async (teamId: number) => {
    try {
        await teamStore.fetchTeamStats(teamId);
    } catch {
        if (teamStore.error) {
            toast.add({ severity: 'error', summary: 'Erro', detail: teamStore.error, life: 3000 });
        }
    }
};

const loadTeamDepartures = async (teamId: number) => {
    try {
        await teamStore.fetchTeamDepartures(teamId);
    } catch {
        if (teamStore.error) {
            toast.add({ severity: 'error', summary: 'Erro', detail: teamStore.error, life: 3000 });
        }
    }
};

const saveTeam = async () => {
    submitted.value = true;
    if (isCreating.value && (!editForm.value.name?.trim() || !editForm.value.sport?.id || !editForm.value.teamType?.id)) {
        toast.add({
            severity: 'warn',
            summary: 'Campos obrigatórios',
            detail: 'Por favor, preencha todos os campos obrigatórios.',
            life: 3000
        });
        return;
    }
    try {
        let foundedDateString = '';
        if (editForm.value.foundedDate) foundedDateString = formatDateToISO(editForm.value.foundedDate);
        const payload: TeamPayload = {
            name: editForm.value.name,
            nickname: editForm.value.nickname || undefined,
            description: editForm.value.description || '',
            welcome_email: editForm.value.welcomeEmail || '',
            stadium_name: editForm.value.stadiumName || undefined,
            primary_color: editForm.value.primaryColor || undefined,
            secondary_color: editForm.value.secondaryColor || undefined,
            website: editForm.value.website || undefined,
            founded_date: foundedDateString,
            max_members: editForm.value.maxMembers,
            is_public: editForm.value.isPublic,
            sport_id: editForm.value.sport?.id || 0,
            team_type_id: editForm.value.teamType?.id || 0
        };
        if (isCreating.value) {
            await teamStore.createTeam(payload);
            toast.add({ severity: 'success', summary: 'Sucesso', detail: 'Time criado com sucesso', life: 3000 });
        } else if (team.value) {
            await teamStore.updateTeam(team.value.id, payload);
            toast.add({ severity: 'success', summary: 'Sucesso', detail: 'Time atualizado com sucesso', life: 3000 });
        }
        showEditModal.value = false;
        await loadMyTeam();
    } catch {
        if (teamStore.error) toast.add({ severity: 'error', summary: 'Erro', detail: teamStore.error, life: 3000 });
    }
};

const savePlayer = async () => {
    playerSubmitted.value = true;
    if (isCreatingPlayer.value && !playerForm.value.identifierValue?.trim()) {
        toast.add({ severity: 'warn', summary: 'Campo obrigatório', detail: 'Informe o identificador do usuário.', life: 3000 });
        return;
    }
    try {
        if (!team.value) return;
        const baseData: PayloadTeamPlayerData = {
            number: playerForm.value.number,
            role: playerForm.value.role,
            joined_at: playerForm.value.joinedAt,
            left_at: playerForm.value.leftAt || undefined
        };
        if (isCreatingPlayer.value) {
            const formData = { ...baseData };
            if (playerForm.value.identifierType === 'user_phone') formData.user_phone = playerForm.value.identifierValue?.replace(/\D/g, '');
            else formData.public_id = playerForm.value.identifierValue;
            await teamPlayerStore.addPlayerToTeam(team.value.id, formData);
            toast.add({ severity: 'success', summary: 'Sucesso', detail: 'Jogador adicionado com sucesso', life: 3000 });
        } else if (selectedPlayer.value) {
            await teamPlayerStore.updateTeamPlayer(team.value.id, selectedPlayer.value.id, baseData);
            toast.add({ severity: 'success', summary: 'Sucesso', detail: 'Jogador atualizado com sucesso', life: 3000 });
        }
        showPlayerModal.value = false;
        await loadTeamPlayers(team.value.id);
    } catch {
        if (teamPlayerStore.error) toast.add({ severity: 'error', summary: 'Erro', detail: teamPlayerStore.error, life: 3000 });
    }
};

const onFileSelect = async (event: FileUploadSelectEvent) => {
    const file = Array.isArray(event.files) ? event.files[0] : event.files;
    if (file && team.value) {
        isUploading.value = true;
        try {
            await teamStore.updateTeamImage(team.value.id, file);
            toast.add({ severity: 'success', summary: 'Sucesso', detail: 'Imagem atualizada com sucesso', life: 3000 });
            await loadMyTeam();
        } catch {
            if (teamStore.error) toast.add({ severity: 'error', summary: 'Erro', detail: teamStore.error, life: 3000 });
        } finally {
            isUploading.value = false;
        }
    }
};

const leaveTeam = async () => {
    if (!team.value) return;
    try {
        await teamPlayerStore.leaveTeam(team.value.id);
        toast.add({ severity: 'success', summary: 'Sucesso', detail: 'Você saiu do time com sucesso', life: 3000 });
        await loadMyTeam();
    } catch {
        if (teamPlayerStore.error) toast.add({ severity: 'error', summary: 'Erro', detail: teamPlayerStore.error, life: 3000 });
    }
};

const confirmLeaveTeam = async () => {
    await leaveTeam();
    showLeaveDialog.value = false;
};

const confirmDeleteTeam = async () => {
    if (!team.value) return;
    try {
        await teamStore.deleteTeam(team.value.id);
        toast.add({ severity: 'success', summary: 'Sucesso', detail: 'Time excluído com sucesso', life: 3000 });
        team.value = null;
        showDeleteDialog.value = false;
    } catch {
        if (teamStore.error) toast.add({ severity: 'error', summary: 'Erro', detail: teamStore.error, life: 3000 });
    }
};

const removePlayerFromTeam = async () => {
    if (!team.value || !playerToRemove.value) return;
    try {
        await teamPlayerStore.removePlayerFromTeam(team.value.id, playerToRemove.value.id);
        toast.add({ severity: 'success', summary: 'Sucesso', detail: 'Jogador removido do time com sucesso', life: 3000 });
        showRemovePlayerDialog.value = false;
        playerToRemove.value = null;
        await loadTeamPlayers(team.value.id);
    } catch {
        if (teamPlayerStore.error) toast.add({ severity: 'error', summary: 'Erro', detail: teamPlayerStore.error, life: 3000 });
    }
};

const copyToClipboard = async (text: string) => {
    await navigator.clipboard.writeText(text);
    toast.add({ severity: 'success', summary: 'Copiado', detail: 'ID público copiado para a área de transferência', life: 2000 });
};

const openWebsite = (url: string) => window.open(url, '_blank');

const openImageDialog = (imageSrc: string) => {
    selectedImage.value = imageSrc;
    showImageDialog.value = true;
};

const openCreateModal = () => {
    isCreating.value = true;
    editForm.value = {
        name: '',
        nickname: undefined,
        description: undefined,
        welcomeEmail: undefined,
        stadiumName: undefined,
        primaryColor: undefined,
        secondaryColor: undefined,
        website: undefined,
        foundedDate: '',
        maxMembers: 30,
        isPublic: false,
        sport: { id: 0, name: '' },
        teamType: { id: 0, name: '' }
    };
    submitted.value = false;
    showEditModal.value = true;
};

const openEditModal = () => {
    isCreating.value = false;
    editForm.value = {
        name: team.value?.name || '',
        nickname: team.value?.nickname || undefined,
        description: team.value?.description || undefined,
        welcomeEmail: team.value?.welcomeEmail || undefined,
        stadiumName: team.value?.stadiumName || undefined,
        primaryColor: team.value?.primaryColor || undefined,
        secondaryColor: team.value?.secondaryColor || undefined,
        website: team.value?.website || undefined,
        foundedDate: team.value?.foundedDate || '',
        maxMembers: team.value?.maxMembers || 30,
        isPublic: team.value?.isPublic ?? false,
        sport: team.value?.sport || { id: 0, name: '' },
        teamType: team.value?.teamType || { id: 0, name: '' }
    };
    showEditModal.value = true;
};

const openAddPlayerModal = () => {
    isCreatingPlayer.value = true;
    isEditingPlayer.value = false;
    selectedPlayer.value = null;
    playerForm.value = { identifierType: 'user_phone', identifierValue: '', number: undefined, role: 'jogador' };
    playerSubmitted.value = false;
    showPlayerModal.value = true;
};

const openEditPlayerModal = (player: TeamPlayer) => {
    isCreatingPlayer.value = false;
    isEditingPlayer.value = true;
    selectedPlayer.value = player;
    playerForm.value = { number: player.number, role: player.role };
    playerSubmitted.value = false;
    showPlayerModal.value = true;
};

const deleteTeam = () => (showDeleteDialog.value = true);

const getResultClass = (result: string): string => {
    const baseClass = 'px-3 py-1 rounded-full text-xs font-semibold';
    switch (result) {
        case 'vitória':
            return `${baseClass} bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300`;
        case 'derrota':
            return `${baseClass} bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300`;
        case 'empate':
            return `${baseClass} bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300`;
        default:
            return `${baseClass} bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300`;
    }
};

const getResultLabel = (result: string): string => {
    switch (result) {
        case 'vitória':
            return 'Vitória';
        case 'derrota':
            return 'Derrota';
        case 'empate':
            return 'Empate';
        default:
            return 'Pendente';
    }
};

const getRoleLabel = (role: string): string => {
    switch (role) {
        case 'jogador':
            return 'Jogador(a)';
        case 'capitao':
            return 'Capitão(ã)';
        case 'treinador':
            return 'Treinador(a)';
        default:
            return 'Jogador(a)';
    }
};

const openRemovePlayerDialog = (player: TeamPlayer) => {
    playerToRemove.value = player;
    showRemovePlayerDialog.value = true;
};

watch(
    () => editForm.value.primaryColor,
    (val) => {
        if (val && !val.startsWith('#')) editForm.value.primaryColor = '#' + val;
    }
);

watch(
    () => editForm.value.secondaryColor,
    (val) => {
        if (val && !val.startsWith('#')) editForm.value.secondaryColor = '#' + val;
    }
);

onMounted(async () => {
    await Promise.all([loadMyTeam(), sportStore.fetchSports(), teamTypeStore.fetchTeamTypes()]);
});
</script>

<template>
    <div v-if="isLoading" class="flex items-center justify-center min-h-[400px]">
        <ProgressSpinner strokeWidth="4" class="w-12 h-12" />
    </div>

    <div v-else-if="team" :style="{ '--team-primary': team.primaryColor || '#3B82F6', '--team-secondary': team.secondaryColor || '#1E40AF' }">
        <div
            class="relative overflow-hidden rounded-2xl shadow-xl mb-8"
            :style="{
                background:
                    team.primaryColor && team.secondaryColor
                        ? `linear-gradient(135deg, ${team.primaryColor} 0%, ${team.secondaryColor} 100%)`
                        : team.primaryColor
                          ? `linear-gradient(135deg, ${team.primaryColor} 0%, ${team.primaryColor}80 100%)`
                          : 'linear-gradient(135deg, #3B82F6 0%, #1E40AF 100%)'
            }"
        >
            <Button
                v-if="currentUserId === team.creator.uuid"
                label="Excluir Time"
                icon="pi pi-trash"
                class="absolute top-4 left-4 z-10 bg-red-500/20 hover:bg-red-500/30 text-white border-red-500/30 hover:border-red-500/40 backdrop-blur-sm transition-all duration-200"
                @click="deleteTeam"
            />
            <div class="absolute inset-0 bg-black bg-opacity-10">
                <svg class="absolute inset-0 w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                    <pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse">
                        <path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="0.5" />
                    </pattern>
                    <rect width="100" height="100" fill="url(#grid)" />
                </svg>
            </div>

            <div class="relative px-6 py-12 lg:px-8">
                <div class="max-w-7xl mx-auto">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-center">
                        <div class="flex justify-center lg:justify-start">
                            <div class="relative group">
                                <div v-if="team.shieldPath" class="relative">
                                    <img
                                        :src="team.shieldPath"
                                        :alt="team.name"
                                        class="w-32 h-32 lg:w-40 lg:h-40 rounded-3xl shadow-2xl ring-4 ring-white/30 group-hover:ring-white/50 transition-all duration-300 transform group-hover:scale-105 object-cover"
                                    />
                                    <div class="absolute inset-0 rounded-3xl bg-gradient-to-t from-black/20 to-transparent"></div>
                                </div>
                                <div
                                    v-else
                                    class="w-32 h-32 lg:w-40 lg:h-40 rounded-3xl bg-white/20 backdrop-blur-sm flex items-center justify-center shadow-2xl ring-4 ring-white/30 group-hover:ring-white/50 transition-all duration-300 transform group-hover:scale-105"
                                >
                                    <i class="pi pi-shield text-5xl lg:text-6xl text-white/80"></i>
                                </div>
                            </div>
                        </div>

                        <div class="lg:col-span-2 text-center lg:text-left">
                            <div class="mb-4">
                                <h1 class="text-3xl lg:text-5xl font-bold text-white mb-2 leading-tight">
                                    {{ team.name }}
                                </h1>
                                <p v-if="team.nickname" class="text-xl lg:text-2xl text-white/90 font-medium">"{{ team.nickname }}"</p>
                                <div class="flex items-center justify-center lg:justify-start gap-4 mt-4">
                                    <div class="flex items-center gap-2 bg-white/20 backdrop-blur-sm rounded-full px-4 py-2">
                                        <i class="pi pi-trophy text-white text-sm"></i>
                                        <span class="text-white font-medium text-sm">{{ team.sport?.name }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 bg-white/20 backdrop-blur-sm rounded-full px-4 py-2">
                                        <i class="pi pi-users text-white text-sm"></i>
                                        <span class="text-white font-medium text-sm">{{ teamPlayers.length }}/{{ team.maxMembers || 30 }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-center lg:justify-start gap-3 flex-wrap">
                                <div class="flex flex-col gap-2 md:flex-row md:gap-3">
                                    <Button
                                        v-if="currentUserId === team.creator.uuid"
                                        label="Editar Time"
                                        icon="pi pi-pencil"
                                        class="w-full md:w-auto min-w-0 bg-white/20 hover:bg-white/30 text-white border-white/30 hover:border-white/40 backdrop-blur-sm transition-all duration-200"
                                        size="small"
                                        @click="openEditModal"
                                    />
                                    <FileUpload
                                        v-if="currentUserId === team.creator.uuid"
                                        ref="fileUploadRef"
                                        mode="basic"
                                        name="demo[]"
                                        accept="image/*"
                                        :auto="false"
                                        @select="onFileSelect"
                                        :disabled="isUploading"
                                        choose-label="Alterar Escudo"
                                        class="w-full md:w-auto min-w-0 [&_.p-button]:bg-white/20 [&_.p-button]:hover:bg-white/30 [&_.p-button]:text-white [&_.p-button]:border-white/30 [&_.p-button]:hover:border-white/40 [&_.p-button]:backdrop-blur-sm"
                                    />
                                </div>
                                <Button v-if="team.website" icon="pi pi-external-link" class="p-button-rounded bg-white/20 hover:bg-white/30 text-white border-white/30 hover:border-white/40 backdrop-blur-sm" @click="openWebsite(team.website)" />
                                <Button
                                    v-if="team.shieldPath"
                                    icon="pi pi-search-plus"
                                    class="p-button-rounded bg-white/20 hover:bg-white/30 text-white border-white/30 hover:border-white/40 backdrop-blur-sm"
                                    @click="openImageDialog(team.shieldPath)"
                                />
                                <ProgressSpinner v-if="isUploading" strokeWidth="3" class="w-6 h-6 text-white" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6 lg:space-y-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <div class="lg:col-span-12 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:gap-6 items-stretch -mx-4 sm:mx-0">
                        <Card class="w-full h-full flex flex-col rounded-none sm:rounded-xl">
                            <template #header>
                                <div class="p-4 lg:p-6 pb-0">
                                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                                        <i class="pi pi-home text-primary"></i>
                                        Informações do Time
                                    </h3>
                                </div>
                            </template>
                            <template #content>
                                <div class="px-4 lg:px-6 pb-4 lg:pb-6 space-y-4 flex-grow">
                                    <div v-if="team.name" class="flex justify-between items-center gap-2 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors" @click="copyToClipboard(team.uuid)">
                                        <span class="text-sm text-gray-600 dark:text-gray-400 flex items-center gap-2">
                                            <span class="relative inline-flex items-center justify-center w-6 h-6 rounded-full bg-white/10 shadow-sm">
                                                <i class="pi pi-hashtag text-xs text-primary"></i>
                                            </span>
                                            Id público:
                                        </span>
                                        <span class="font-semibold text-gray-800 dark:text-gray-200 text-right">{{ team.uuid }}</span>
                                    </div>
                                    <div v-if="team.name" class="flex justify-between items-center gap-2">
                                        <span class="text-sm text-gray-600 dark:text-gray-400 flex items-center gap-2">
                                            <i class="pi pi-tag text-xs text-primary"></i>
                                            Nome:
                                        </span>
                                        <span class="font-semibold text-gray-800 dark:text-gray-200 text-right">{{ team.name }}</span>
                                    </div>
                                    <div v-if="team.stadiumName" class="flex justify-between items-center gap-2">
                                        <span class="text-sm text-gray-600 dark:text-gray-400 flex items-center gap-2">
                                            <i class="pi pi-map-marker text-xs text-primary"></i>
                                            Estádio:
                                        </span>
                                        <span class="font-semibold text-gray-800 dark:text-gray-200 text-right">{{ team.stadiumName }}</span>
                                    </div>
                                    <div v-if="team.foundedDate" class="flex justify-between items-center gap-2">
                                        <span class="text-sm text-gray-600 dark:text-gray-400 flex items-center gap-2">
                                            <i class="pi pi-calendar text-xs text-primary"></i>
                                            Fundação:
                                        </span>
                                        <span class="font-semibold text-gray-800 dark:text-gray-200 text-right">{{ team.foundedDate }}</span>
                                    </div>
                                    <div v-if="team.website" class="flex justify-between items-center gap-2">
                                        <span class="text-sm text-gray-600 dark:text-gray-400 flex items-center gap-2">
                                            <i class="pi pi-globe text-xs text-primary"></i>
                                            Website:
                                        </span>
                                        <Button label="Visitar" icon="pi pi-external-link" size="small" class="p-button-primary p-button-sm" @click="openWebsite(team.website)" />
                                    </div>
                                    <div class="flex justify-between items-center gap-2">
                                        <span class="text-sm text-gray-600 dark:text-gray-400 flex items-center gap-2">
                                            <i class="pi pi-eye text-xs text-primary"></i>
                                            Visibilidade:
                                        </span>
                                        <span class="font-semibold text-gray-800 dark:text-gray-200 text-right">{{ team.isPublic ? 'Público' : 'Privado' }}</span>
                                    </div>
                                    <div class="flex justify-between items-center gap-2">
                                        <span class="text-sm text-gray-600 dark:text-gray-400 flex items-center gap-2">
                                            <i class="pi pi-users text-xs text-primary"></i>
                                            Tipo do time:
                                        </span>
                                        <span class="font-semibold text-gray-800 dark:text-gray-200 text-right">{{ team.teamType.name }}</span>
                                    </div>
                                    <div class="flex justify-between items-center gap-2">
                                        <span class="text-sm text-gray-600 dark:text-gray-400 flex items-center gap-2">
                                            <i class="pi pi-user text-xs text-primary"></i>
                                            Fundado por:
                                        </span>
                                        <span class="font-semibold text-gray-800 dark:text-gray-200 text-right">{{ team.creator.name }}</span>
                                    </div>
                                    <div class="flex justify-between items-center gap-2">
                                        <span class="text-sm text-gray-600 dark:text-gray-400 flex items-center gap-2">
                                            <i class="pi pi-clock text-xs text-primary"></i>
                                            Membros:
                                        </span>
                                        <span class="font-semibold text-gray-800 dark:text-gray-200 text-right">{{ teamPlayers.length }}/{{ team.maxMembers || 30 }}</span>
                                    </div>
                                </div>
                            </template>
                        </Card>

                        <Card class="w-full h-full flex flex-col rounded-none sm:rounded-xl">
                            <template #header>
                                <div class="p-4 lg:p-6 pb-0">
                                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                                        <i class="pi pi-palette team-gradient-text"></i>
                                        Cores e Design
                                    </h3>
                                </div>
                            </template>
                            <template #content>
                                <div class="px-4 lg:px-6 pb-4 lg:pb-6 space-y-4 flex-grow">
                                    <div v-if="team.primaryColor" class="flex justify-between items-center gap-2">
                                        <span class="text-sm text-gray-600 dark:text-gray-400 flex items-center gap-2">
                                            <i class="pi pi-circle-fill text-xs team-gradient-text"></i>
                                            Cor Primária:
                                        </span>
                                        <div class="flex items-center gap-2">
                                            <span class="font-semibold text-gray-800 dark:text-gray-200">{{ team.primaryColor }}</span>
                                            <div :style="{ backgroundColor: team.primaryColor }" class="w-8 h-8 rounded-md border-2 border-gray-300 shadow-sm"></div>
                                        </div>
                                    </div>
                                    <div v-if="team.secondaryColor" class="flex justify-between items-center gap-2">
                                        <span class="text-sm text-gray-600 dark:text-gray-400 flex items-center gap-2">
                                            <i class="pi pi-circle text-xs team-gradient-text"></i>
                                            Cor Secundária:
                                        </span>
                                        <div class="flex items-center gap-2">
                                            <span class="font-semibold text-gray-800 dark:text-gray-200">{{ team.secondaryColor }}</span>
                                            <div :style="{ backgroundColor: team.secondaryColor }" class="w-8 h-8 rounded-md border-2 border-gray-300 shadow-sm"></div>
                                        </div>
                                    </div>
                                    <div v-if="team.primaryColor || team.secondaryColor" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div v-if="team.primaryColor" class="text-center">
                                            <div :style="{ backgroundColor: team.primaryColor }" class="w-full h-12 rounded-lg border-2 border-gray-300 shadow-sm flex items-center justify-center">
                                                <span class="text-white font-semibold text-xs">{{ team.primaryColor }}</span>
                                            </div>
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Prévia</p>
                                        </div>
                                        <div v-if="team.secondaryColor" class="text-center">
                                            <div :style="{ backgroundColor: team.secondaryColor }" class="w-full h-12 rounded-lg border-2 border-gray-300 shadow-sm flex items-center justify-center">
                                                <span class="text-white font-semibold text-xs">{{ team.secondaryColor }}</span>
                                            </div>
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Prévia</p>
                                        </div>
                                    </div>
                                    <div v-if="!team.primaryColor && !team.secondaryColor" class="text-center py-4">
                                        <p class="text-gray-500 dark:text-gray-400">Nenhuma cor definida ainda.</p>
                                    </div>
                                </div>
                            </template>
                        </Card>
                    </div>

                    <Card class="-mx-4 sm:mx-0 rounded-none sm:rounded-xl">
                        <template #header>
                            <div class="p-4 lg:p-4 pb-0">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                                    <i class="pi pi-book text-primary"></i>
                                    Sobre o Time
                                </h3>
                            </div>
                        </template>
                        <template #content>
                            <div class="px-4 lg:px-6 pb-4 lg:pb-6">
                                <p class="text-gray-600 dark:text-gray-400 leading-relaxed text-sm lg:text-base">
                                    {{ team.description || 'Nenhuma descrição disponível sobre o time.' }}
                                </p>
                            </div>
                        </template>
                    </Card>

                    <Card class="-mx-4 sm:mx-0 rounded-none sm:rounded-xl">
                        <template #header>
                            <div class="p-4 lg:p-6 pb-0">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                                    <i class="pi pi-chart-bar text-primary"></i>
                                    Estatísticas
                                </h3>
                            </div>
                        </template>
                        <template #content>
                            <div v-if="statsArray.length > 0" class="px-4 lg:px-6 pb-4 lg:pb-6">
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 lg:gap-4">
                                    <div v-for="stat in statsArray" :key="stat.name" class="flex flex-col items-center justify-center p-3 lg:p-4 bg-gray-50 dark:bg-gray-900 rounded-lg hover:shadow-md transition-all duration-200">
                                        <i :class="`${stat.icon} text-lg lg:text-xl text-${stat.color}-500 mb-1 lg:mb-2`"></i>
                                        <div class="text-xl lg:text-2xl font-bold text-gray-800 dark:text-gray-100">{{ stat.value }}</div>
                                        <div class="text-xs lg:text-sm text-gray-600 dark:text-gray-400 text-center">{{ stat.name }}</div>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="px-4 lg:px-6 pb-4 lg:pb-6 text-center py-8">
                                <i class="pi pi-chart-bar text-4xl text-gray-400 mb-3"></i>
                                <p class="text-gray-500 dark:text-gray-400">Nenhuma estatística disponível ainda.</p>
                                <p class="text-sm text-gray-400 dark:text-gray-500 mt-2">Participe de partidas para gerar estatísticas!</p>
                            </div>
                        </template>
                    </Card>

                    <Card class="-mx-4 sm:mx-0 rounded-none sm:rounded-xl">
                        <template #header>
                            <div class="p-4 lg:p-6 pb-0">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                                    <i class="pi pi-trophy text-primary"></i>
                                    Partidas
                                </h3>
                            </div>
                        </template>
                        <template #content>
                            <div v-if="teamStore.teamDepartures.length > 0" class="px-4 lg:px-6 pb-4 lg:pb-6 space-y-3">
                                <div v-for="departure in teamStore.teamDepartures" :key="departure.id" class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-900 rounded-lg hover:shadow-md transition-all duration-200">
                                    <div class="flex items-center gap-4 flex-1">
                                        <div class="text-center min-w-fit">
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ departure.date }}</p>
                                            <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ departure.isHome ? 'Casa' : 'Fora' }}</p>
                                        </div>
                                        <div class="flex-1 text-center">
                                            <p class="text-sm font-bold text-gray-800 dark:text-gray-100">{{ departure.opponent }}</p>
                                            <p class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ departure.goalsScored }} x {{ departure.goalsConceded }}</p>
                                        </div>
                                        <div :class="getResultClass(departure.result)">
                                            {{ getResultLabel(departure.result) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="px-4 lg:px-6 pb-4 lg:pb-6 text-center py-8">
                                <i class="pi pi-trophy text-4xl text-gray-400 mb-3"></i>
                                <p class="text-gray-500 dark:text-gray-400">Nenhuma partida registrada ainda.</p>
                                <p class="text-sm text-gray-400 dark:text-gray-500 mt-2">As partidas do seu time aparecerão aqui!</p>
                            </div>
                        </template>
                    </Card>
                </div>
            </div>
            <Card class="-mx-4 sm:mx-0 rounded-none sm:rounded-xl">
                <template #content>
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                                <i class="pi pi-users text-primary"></i>
                                Elenco
                            </h3>
                        </div>

                        <Button v-if="currentUserId === team.creator.uuid" label="Adicionar Jogador(a)" icon="pi pi-plus" size="small" class="p-button-primary" @click="openAddPlayerModal" />

                        <Button v-else-if="currentUserId !== team.creator.uuid" label="Sair do Time" icon="pi pi-sign-out" size="small" class="p-button-danger" @click="showLeaveDialog = true" />
                    </div>

                    <div v-if="isLoadingPlayers" class="flex justify-center py-8">
                        <ProgressSpinner strokeWidth="4" class="w-8 h-8" />
                    </div>

                    <div v-else-if="teamPlayers && teamPlayers.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div
                            v-for="teamPlayer in teamPlayers"
                            :key="teamPlayer.id"
                            class="relative overflow-hidden rounded-xl shadow-md hover:shadow-xl transition-all duration-300 border-2 hover:scale-[1.02] bg-white dark:bg-gray-800"
                            style="border-color: var(--primary-color)"
                        >
                            <div class="absolute top-3 left-3 z-10">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold shadow-sm text-primary"> Ativo </span>
                            </div>

                            <div class="p-5 pt-12">
                                <div class="flex flex-col items-center mb-4">
                                    <div class="flex flex-col items-center mb-3">
                                        <div class="relative w-20 h-20 flex items-center justify-center rounded-full shadow-lg bg-primary">
                                            <img v-if="teamPlayer.user.imagePath" :src="teamPlayer.user.imagePath" class="w-20 h-20 rounded-full object-cover" />
                                            <i v-else class="pi pi-user text-white text-5xl"></i>
                                        </div>
                                    </div>

                                    <h4 class="font-bold text-lg text-center text-gray-900 dark:text-gray-100 mb-1 line-clamp-2">
                                        {{ teamPlayer.user.name }}
                                    </h4>

                                    <p class="text-sm font-semibold text-gray-600 dark:text-gray-400 capitalize">
                                        {{ getRoleLabel(teamPlayer.role) }}
                                    </p>

                                    <div v-if="teamPlayer.number" class="mt-2">
                                        <span class="inline-block bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 text-sm font-semibold px-3 py-1 rounded-full"> Camisa {{ teamPlayer.number }} </span>
                                    </div>
                                </div>

                                <div class="space-y-2 mb-4">
                                    <div v-if="teamPlayer.user.athleteProfile" class="flex items-center justify-between text-sm bg-white/50 dark:bg-gray-900/50 rounded-lg p-2">
                                        <span class="text-gray-600 dark:text-gray-400 flex items-center gap-1">
                                            <i class="pi pi-map-marker text-xs text-primary"></i>
                                            Posição:
                                        </span>
                                        <span class="font-semibold text-gray-900 dark:text-gray-100 capitalize">
                                            {{ teamPlayer.user.athleteProfile.position ? teamPlayer.user.athleteProfile.position : '-' }}
                                        </span>
                                    </div>

                                    <div v-if="teamPlayer.user.athleteProfile" class="flex items-center justify-between text-sm bg-white/50 dark:bg-gray-900/50 rounded-lg p-2">
                                        <span class="text-gray-600 dark:text-gray-400 flex items-center gap-1">
                                            <i class="pi pi-directions text-xs text-primary"></i>
                                            Lado dominante:
                                        </span>
                                        <span class="font-semibold text-gray-900 dark:text-gray-100 capitalize">
                                            {{ teamPlayer.user.athleteProfile.dominantSide ? teamPlayer.user.athleteProfile.dominantSide : '-' }}
                                        </span>
                                    </div>

                                    <div v-if="teamPlayer.joinedAt" class="flex items-center justify-between text-sm bg-white/50 dark:bg-gray-900/50 rounded-lg p-2">
                                        <span class="text-gray-600 dark:text-gray-400 flex items-center gap-1">
                                            <i class="pi pi-calendar text-xs text-primary"></i>
                                            No time desde:
                                        </span>
                                        <span class="font-semibold text-gray-900 dark:text-gray-100">
                                            {{ teamPlayer.joinedAt }}
                                        </span>
                                    </div>
                                </div>

                                <div v-if="currentUserId === team.creator.uuid" class="flex gap-2 mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                    <Button icon="pi pi-pencil" label="Editar" size="small" class="flex-1 p-button-outlined" @click="openEditPlayerModal(teamPlayer)" />
                                    <Button icon="pi pi-trash" label="Tirar do time" size="small" class="flex-1 p-button-danger p-button-outlined" @click="openRemovePlayerDialog(teamPlayer)" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-12">
                        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-100 dark:bg-gray-800 mb-4">
                            <i class="pi pi-users text-4xl text-gray-400"></i>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">Nenhum jogador(a) no elenco</h4>
                        <p class="text-gray-500 dark:text-gray-400 mb-6">Comece adicionando jogador(a)s ao seu time</p>
                        <Button v-if="currentUserId === team.creator.uuid" label="Adicionar Primeiro Jogador(a)" icon="pi pi-plus" class="p-button-primary p-button-lg" @click="openAddPlayerModal" />
                    </div>
                </template>
            </Card>
        </div>
    </div>

    <div v-else class="flex items-center justify-center min-h-[400px] px-4">
        <div class="text-center max-w-md mx-auto">
            <div class="bg-gray-100 dark:bg-gray-800 rounded-full w-20 h-20 lg:w-24 lg:h-24 mx-auto mb-6 flex items-center justify-center">
                <i class="pi pi-shield text-gray-400" style="font-size: 5rem"></i>
            </div>
            <h3 class="text-xl lg:text-2xl font-semibold text-gray-800 dark:text-gray-200 mb-3">Time não encontrado</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-6 text-sm lg:text-base leading-relaxed">Você ainda não possui um time. Crie seu time para começar a jogar com seus amigos!</p>
            <Button label="Criar Time" icon="pi pi-plus" class="p-button-primary p-button-lg w-full sm:w-auto" @click="openCreateModal" />
        </div>
    </div>

    <Dialog v-model:visible="showEditModal" modal :header="isCreating ? 'Criar Time' : 'Editar Time'" :style="{ width: '50vw' }" :breakpoints="{ '960px': '75vw', '640px': '100vw' }" :maximizable="true">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 lg:gap-6">
            <div class="col-span-1 sm:col-span-2">
                <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-100 uppercase tracking-wide mb-3 border-b border-gray-200 dark:border-gray-700 pb-2">
                    <i class="pi pi-asterisk text-primary text-xs mr-2"></i>
                    Informações Básicas
                </h4>
            </div>

            <div class="col-span-1">
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"> Nome do Time <span class="text-red-500">*</span> </label>
                <InputText id="name" v-model="editForm.name" :invalid="submitted && isCreating && !editForm.name" class="w-full" placeholder="Ex: Real Maia CB" />
                <small v-if="submitted && isCreating && !editForm.name" class="text-red-500">Campo obrigatório</small>
            </div>

            <div class="col-span-1">
                <label for="nickname" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"> Apelido <span class="text-gray-400 text-xs">(opcional)</span> </label>
                <InputText id="nickname" v-model="editForm.nickname" placeholder="Ex: Los Blancos" class="w-full" />
            </div>

            <div class="col-span-1">
                <label for="sport_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"> Esporte <span class="text-red-500">*</span> </label>
                <Select
                    id="sport_id"
                    v-model="editForm.sport!.id"
                    :options="sportStore.sportOptions"
                    optionLabel="label"
                    optionValue="value"
                    placeholder="Selecione o esporte"
                    class="w-full"
                    :invalid="submitted && isCreating && !editForm.sport?.id"
                />
                <small v-if="submitted && isCreating && !editForm.sport?.id" class="text-red-500">Campo obrigatório</small>
            </div>

            <div class="col-span-1">
                <label for="team_type_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"> Tipo de Time <span class="text-red-500">*</span> </label>
                <Select
                    id="team_type_id"
                    v-model="editForm.teamType!.id"
                    :options="teamTypeStore.teamTypeOptions"
                    optionLabel="label"
                    optionValue="value"
                    placeholder="Selecione o tipo"
                    class="w-full"
                    :invalid="submitted && isCreating && !editForm.teamType?.id"
                />
                <small v-if="submitted && isCreating && !editForm.teamType?.id" class="text-red-500">Campo obrigatório</small>
            </div>

            <div class="col-span-1 sm:col-span-2">
                <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-100 uppercase tracking-wide mb-3 mt-4 border-b border-gray-200 dark:border-gray-700 pb-2">
                    <i class="pi pi-cog text-primary text-xs mr-2"></i>
                    Configurações
                </h4>
            </div>

            <div class="col-span-1">
                <label for="max_members" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"> Máximo de Jogador(a)s <span class="text-gray-400 text-xs">(opcional)</span> </label>
                <InputNumber id="max_members" v-model="editForm.maxMembers" :min="10" :max="50" placeholder="30" :invalid="submitted && (!editForm.maxMembers || editForm.maxMembers < 10)" showButtons buttonLayout="horizontal" :step="1" fluid>
                    <template #incrementicon>
                        <span class="pi pi-plus" />
                    </template>
                    <template #decrementicon>
                        <span class="pi pi-minus" />
                    </template>
                </InputNumber>
                <small class="text-gray-500">Entre 10 e 50 jogador(a)s</small>
            </div>

            <div class="col-span-1">
                <label for="is_public" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"> Visibilidade <span class="text-gray-400 text-xs">(opcional)</span> </label>
                <Select
                    id="is_public"
                    v-model="editForm.isPublic"
                    :options="[
                        { label: 'Público', value: true },
                        { label: 'Privado', value: false }
                    ]"
                    optionLabel="label"
                    optionValue="value"
                    placeholder="Selecione a visibilidade"
                    class="w-full"
                />
            </div>

            <div class="col-span-1 sm:col-span-2">
                <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-100 uppercase tracking-wide mb-3 mt-4 border-b border-gray-200 dark:border-gray-700 pb-2">
                    <i class="pi pi-info-circle text-primary text-xs mr-2"></i>
                    Detalhes
                </h4>
            </div>

            <div class="col-span-1">
                <label for="stadium_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"> Nome do Estádio <span class="text-gray-400 text-xs">(opcional)</span> </label>
                <InputText id="stadium_name" v-model="editForm.stadiumName" placeholder="Ex: Santiago Bernabéu" class="w-full" />
            </div>

            <div class="col-span-1">
                <label for="founded_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"> Data de Fundação <span class="text-gray-400 text-xs">(opcional)</span> </label>
                <InputMask id="founded_date" v-model="editForm.foundedDate" mask="99/99/9999" placeholder="dd/mm/aaaa" class="w-full" type="tel" />
            </div>

            <div class="col-span-1">
                <label for="website" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"> Website <span class="text-gray-400 text-xs">(opcional)</span> </label>
                <InputText id="website" v-model="editForm.website" placeholder="https://www.exemplo.com" class="w-full" />
                <small class="text-gray-500">Pode ser links do Instagram, Facebook, etc.</small>
            </div>

            <div class="col-span-1">
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label for="primary_color" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"> Cor Primária <span class="text-gray-400 text-xs">(opcional)</span> </label>
                        <div class="flex items-center gap-2">
                            <ColorPicker id="primary_color" v-model="editForm.primaryColor" class="w-12 h-10" />
                            <InputText v-model="editForm.primaryColor" placeholder="#FFFFFF" class="flex-1 text-sm" />
                        </div>
                    </div>
                    <div>
                        <label for="secondary_color" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"> Cor Secundária <span class="text-gray-400 text-xs">(opcional)</span> </label>
                        <div class="flex items-center gap-2">
                            <ColorPicker id="secondary_color" v-model="editForm.secondaryColor" class="w-12 h-10" />
                            <InputText v-model="editForm.secondaryColor" placeholder="#000000" class="flex-1 text-sm" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-span-1 sm:col-span-2">
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Descrição <span class="text-gray-400 text-xs">(opcional)</span>
                    <span v-if="editForm.description" class="text-gray-500 text-xs"> ({{ editForm.description.length }}/1000) </span>
                </label>
                <Textarea id="description" v-model="editForm.description" rows="4" :maxlength="1000" placeholder="Conte sobre a história, objetivos e características do time..." class="w-full" />
                <small class="text-gray-500">máximo de 1000 caracteres</small>
            </div>

            <div class="col-span-1 sm:col-span-2">
                <label for="welcome_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Mensagem de Boas Vindas <span class="text-gray-400 text-xs">(opcional)</span>
                    <span v-if="editForm.welcomeEmail" class="text-gray-500 text-xs"> ({{ editForm.welcomeEmail.length }}/1000) </span>
                </label>
                <Textarea id="welcome_email" v-model="editForm.welcomeEmail" rows="4" :maxlength="1000" placeholder="Digite uma mensagem de boas vindas para novos membros..." class="w-full" />
                <small class="text-gray-500">máximo de 1000 caracteres</small>
            </div>
        </div>
        <template #footer>
            <div class="flex flex-col sm:flex-row gap-2 w-full">
                <Button label="Cancelar" icon="pi pi-times" class="p-button-text w-full sm:w-auto" @click="showEditModal = false" />
                <Button :label="isCreating ? 'Criar' : 'Salvar'" :icon="isCreating ? 'pi pi-plus' : 'pi pi-check'" class="p-button-primary w-full sm:w-auto" @click="saveTeam" />
            </div>
        </template>
    </Dialog>

    <Dialog v-model:visible="showImageDialog" modal :header="team?.name || 'Imagem do Time'" :style="{ width: 'min(90vw, 600px)' }">
        <img :src="selectedImage" class="w-full h-auto rounded-lg" />
    </Dialog>

    <Dialog v-model:visible="showPlayerModal" modal :header="isCreatingPlayer ? 'Adicionar Jogador(a)' : 'Editar Jogador(a)'" :style="{ width: '50vw' }" :breakpoints="{ '960px': '75vw', '640px': '100vw' }" :maximizable="true">
        <div class="grid grid-cols-1 gap-4">
            <div v-if="isCreatingPlayer" class="col-span-1">
                <label for="identifier_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tipo de Identificador <span class="text-red-500">*</span></label>
                <Select
                    id="identifier_type"
                    v-model="playerForm.identifierType"
                    :options="[
                        { label: 'Telefone do Usuário', value: 'user_phone' },
                        { label: 'ID Público', value: 'public_id' }
                    ]"
                    optionLabel="label"
                    optionValue="value"
                    placeholder="Selecione o tipo"
                    class="w-full"
                />
            </div>

            <div v-if="isCreatingPlayer" class="col-span-1">
                <label for="identifier_value" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                    >{{ playerForm.identifierType === 'user_phone' ? 'Telefone do Usuário' : 'ID Público do Usuário' }} <span class="text-red-500">*</span></label
                >
                <InputMask
                    v-if="playerForm.identifierType === 'user_phone'"
                    id="identifier_value"
                    v-model="playerForm.identifierValue"
                    mask="99 99999-9999"
                    placeholder="00 00000-0000"
                    class="w-full"
                    type="tel"
                    :invalid="playerSubmitted && isCreatingPlayer && !playerForm.identifierValue?.trim()"
                />
                <InputText v-else id="identifier_value" v-model="playerForm.identifierValue" placeholder="Ex: user123" class="w-full" :invalid="playerSubmitted && isCreatingPlayer && !playerForm.identifierValue?.trim()" />
                <small v-if="playerSubmitted && isCreatingPlayer && !playerForm.identifierValue?.trim()" class="text-red-500">Campo obrigatório</small>
            </div>

            <div class="col-span-1">
                <label for="number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Número da Camisa</label>
                <InputNumber id="number" v-model="playerForm.number" placeholder="Ex: 10" showButtons buttonLayout="horizontal" :step="1" fluid>
                    <template #incrementicon>
                        <span class="pi pi-plus" />
                    </template>
                    <template #decrementicon>
                        <span class="pi pi-minus" />
                    </template>
                </InputNumber>
            </div>

            <div class="col-span-1">
                <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Função</label>
                <Select id="role" v-model="playerForm.role" :options="roleOptions" optionLabel="label" optionValue="value" placeholder="Selecione a função" class="w-full" />
            </div>
        </div>

        <template #footer>
            <div class="flex flex-col sm:flex-row gap-2 w-full">
                <Button label="Cancelar" icon="pi pi-times" class="p-button-text w-full sm:w-auto" @click="showPlayerModal = false" />
                <Button :label="isCreatingPlayer ? 'Adicionar' : 'Salvar'" :icon="isCreatingPlayer ? 'pi pi-plus' : 'pi pi-check'" class="p-button-primary w-full sm:w-auto" @click="savePlayer" />
            </div>
        </template>
    </Dialog>

    <Dialog v-model:visible="showLeaveDialog" modal header="Confirmar Saída" :style="{ width: 'min(25rem, 95vw)' }">
        <p>Tem certeza que deseja sair do time? Esta ação não pode ser desfeita.</p>
        <template #footer>
            <Button label="Cancelar" icon="pi pi-times" class="p-button-text" @click="showLeaveDialog = false" />
            <Button label="Confirmar" icon="pi pi-check" class="p-button-danger" @click="confirmLeaveTeam" />
        </template>
    </Dialog>

    <Dialog v-model:visible="showRemovePlayerDialog" modal header="Remover Jogador(a)" :style="{ width: 'min(25rem, 95vw)' }">
        <p v-if="playerToRemove">
            Tem certeza que deseja remover <strong>{{ playerToRemove.user.name }}</strong> do time? Esta ação não pode ser desfeita.
        </p>
        <template #footer>
            <Button label="Cancelar" icon="pi pi-times" class="p-button-text" @click="showRemovePlayerDialog = false" />
            <Button label="Remover" icon="pi pi-trash" class="p-button-danger" @click="removePlayerFromTeam" />
        </template>
    </Dialog>

    <Dialog v-model:visible="showDeleteDialog" modal header="Confirmar Exclusão" :style="{ width: 'min(25rem, 95vw)' }">
        <p>Tem certeza que deseja excluir este time? Esta ação não pode ser desfeita.</p>
        <template #footer>
            <Button label="Cancelar" icon="pi pi-times" class="p-button-text" @click="showDeleteDialog = false" />
            <Button label="Confirmar" icon="pi pi-check" class="p-button-danger" @click="confirmDeleteTeam" />
        </template>
    </Dialog>
</template>

<style scoped>
.team-gradient-text {
    background: linear-gradient(100deg, var(--team-primary) 50%, var(--team-secondary) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
</style>
