<script setup lang="ts">
import { onMounted, ref, watch, computed, reactive } from 'vue';
import { useTournamentStore } from '@/stores/company/tournamentStore';
import { useTournamentTeamStore } from '@/stores/company/tournamentTeamStore';
import type { Tournament } from '@/types/company/tournament';
import type { TournamentPayload } from '@/types/company/tournament';
import type { TournamentFilters } from '@/types/company/filters/tournamentFilter';
import type { TournamentTeamFilter } from '@/types/company/filters/tournamentTeamFilter';
import type { TournamentTeam, TournamentTeamPayload } from '@/types/company/tournamentTeam';
import type { DataTableSortEvent, DataTablePageEvent } from 'primevue/datatable';
import { useToast } from 'primevue/usetoast';
import { useKeyboardShortcuts } from '@/utils/useKeyboardShortcuts';

const tournamentStore = useTournamentStore();
const tournamentTeamStore = useTournamentTeamStore();

const searchTerm = ref('');
const currentPage = ref(1);
const rowsPerPage = ref(10);
const sortField = ref('id');
const sortOrder = ref<'asc' | 'desc'>('asc');

const tournamentDialog = ref(false);
const detailDialog = ref(false);
const deleteTournamentDialog = ref(false);
const deleteTournamentsDialog = ref(false);
const submitted = ref(false);
const showFiltersModal = ref(false);

const teamsPage = ref(1);
const teamsPerPage = ref(10);
const teamsSortField = ref('id');
const teamsSortOrder = ref<'asc' | 'desc'>('asc');

const teamDialog = ref(false);
const deleteTeamDialog = ref(false);
const teamSubmitted = ref(false);
const isEditingTeam = ref(false);
const loadingTeamData = ref(false);

const shieldDialog = ref(false);
const selectedShield = ref('');

const statusOptions = ref<{ label: string; value: string }[]>([
    { label: 'Aberto', value: 'aberto' },
    { label: 'Em andamento', value: 'em_andamento' },
    { label: 'Finalizado', value: 'finalizado' }
]);

const publicOptions = ref<{ label: string; value: boolean }[]>([
    { label: 'Público', value: true },
    { label: 'Privado', value: false }
]);

const statusFilterOptions = computed(() => {
    return [{ label: 'Todos', value: null }, ...statusOptions.value.map((opt) => ({ label: opt.label, value: opt.value }))];
});

const publicFilterOptions = computed(() => {
    return [{ label: 'Todos', value: null }, ...publicOptions.value.map((opt) => ({ label: opt.label, value: opt.value }))];
});

const filtersObj = reactive({
    status: null as string | null,
    isPublic: null as boolean | null
});

const toast = useToast();

useKeyboardShortcuts(openCreateTournament, saveTournament, tournamentDialog, exportCSV);

watch(
    () => tournamentStore.pagination?.currentPage,
    (newPage) => {
        if (newPage && newPage !== currentPage.value) {
            currentPage.value = newPage;
        }
    }
);

watch(
    () => filtersObj,
    async () => {
        currentPage.value = 1;
        await loadTournaments();
    },
    { deep: true }
);

onMounted(async () => {
    try {
        await loadTournaments();
    } catch {
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: 'Erro ao carregar dados da página',
            life: 5000
        });
    }
});

async function loadTournaments(): Promise<void> {
    const filters: TournamentFilters = {
        search: searchTerm.value || undefined,
        status: filtersObj.status || undefined,
        isPublic: filtersObj.isPublic !== null ? filtersObj.isPublic : undefined,
        sort: sortField.value,
        direction: sortOrder.value,
        perPage: rowsPerPage.value,
        page: currentPage.value
    };

    await tournamentStore.fetchTournaments(filters);
}

async function onSearch(): Promise<void> {
    currentPage.value = 1;
    await loadTournaments();
}

async function onPageChange(event: DataTablePageEvent): Promise<void> {
    currentPage.value = event.page + 1;
    rowsPerPage.value = event.rows;
    await loadTournaments();
}

async function onPaginatorPageChange(event: { page: number; first: number; rows: number }): Promise<void> {
    currentPage.value = event.page + 1;
    rowsPerPage.value = event.rows;
    await loadTournaments();
}

async function onSort(event: DataTableSortEvent): Promise<void> {
    if (typeof event.sortField === 'string') {
        sortField.value = event.sortField || 'name';
        sortOrder.value = event.sortOrder === 1 ? 'asc' : 'desc';
        await loadTournaments();
    }
}

async function saveTournament(): Promise<void> {
    submitted.value = true;

    if (tournamentStore.tournament.name?.trim() && tournamentStore.tournament.status && tournamentStore.tournament.startDate && tournamentStore.tournament.endDate && tournamentStore.tournament.maxParticipants > 0) {
        try {
            let startDateString = '';
            if (tournamentStore.tournament.startDate) {
                if (tournamentStore.tournament.startDate instanceof Date) {
                    startDateString = tournamentStore.tournament.startDate.toISOString().split('T')[0];
                } else {
                    startDateString = new Date(tournamentStore.tournament.startDate).toISOString().split('T')[0];
                }
            }

            let endDateString = '';
            if (tournamentStore.tournament.endDate) {
                if (tournamentStore.tournament.endDate instanceof Date) {
                    endDateString = tournamentStore.tournament.endDate.toISOString().split('T')[0];
                } else {
                    endDateString = new Date(tournamentStore.tournament.endDate).toISOString().split('T')[0];
                }
            }

            const payload: TournamentPayload = {
                name: tournamentStore.tournament.name,
                status: tournamentStore.tournament.status,
                description: tournamentStore.tournament.description || '',
                awards: tournamentStore.tournament.awards || '',
                welcome_email: tournamentStore.tournament.welcomeEmail || '',
                start_date: startDateString,
                end_date: endDateString,
                max_participants: tournamentStore.tournament.maxParticipants,
                is_public: tournamentStore.tournament.isPublic
            };

            if (tournamentStore.tournament.id) {
                await tournamentStore.updateTournament(tournamentStore.tournament.id, payload);
                toast.add({ severity: 'success', summary: 'Sucesso', detail: 'Torneio atualizado com sucesso', life: 3000 });
            } else {
                await tournamentStore.createTournament(payload);
                toast.add({ severity: 'success', summary: 'Sucesso', detail: 'Torneio criado com sucesso', life: 3000 });
            }

            tournamentDialog.value = false;
            tournamentStore.clearTournament();
            await loadTournaments();
        } catch {
            toast.add({ severity: 'error', summary: 'Erro', detail: tournamentStore.error, life: 3000 });
        }
    }
}

async function deleteTournament(): Promise<void> {
    if (tournamentStore.tournament.id) {
        try {
            await tournamentStore.deleteTournament(tournamentStore.tournament.id);
            deleteTournamentDialog.value = false;
            tournamentStore.clearTournament();
            toast.add({ severity: 'success', summary: 'Sucesso', detail: 'Torneio deletado com sucesso', life: 3000 });
            await loadTournaments();
        } catch {
            toast.add({ severity: 'error', summary: 'Erro', detail: tournamentStore.error, life: 3000 });
        }
    }
}

async function deleteSelectedTournaments(): Promise<void> {
    try {
        await tournamentStore.deleteSelectedTournaments();
        deleteTournamentsDialog.value = false;
        toast.add({ severity: 'success', summary: 'Sucesso', detail: 'Torneios deletados com sucesso', life: 3000 });
        await loadTournaments();
    } catch {
        toast.add({ severity: 'error', summary: 'Erro', detail: tournamentStore.error, life: 3000 });
    }
}

function openCreateTournament(): void {
    tournamentStore.clearTournament();
    submitted.value = false;
    tournamentDialog.value = true;
}

function openUpdateTournament(tournamentData: Tournament): void {
    const editedTournament = { ...tournamentData };
    if (editedTournament.startDate && typeof editedTournament.startDate === 'string') {
        const dateStr = editedTournament.startDate as string;
        const [day, month, year] = dateStr.split('/').map(Number);
        editedTournament.startDate = new Date(year, month - 1, day);
    }
    if (editedTournament.endDate && typeof editedTournament.endDate === 'string') {
        const dateStr = editedTournament.endDate as string;
        const [day, month, year] = dateStr.split('/').map(Number);
        editedTournament.endDate = new Date(year, month - 1, day);
    }
    tournamentStore.tournament = editedTournament;
    tournamentDialog.value = true;
}

function openDestroyTournament(tournamentData: Tournament): void {
    tournamentStore.tournament = tournamentData;
    deleteTournamentDialog.value = true;
}

function openDestroySelected(): void {
    deleteTournamentsDialog.value = true;
}

function hideDialog(): void {
    tournamentDialog.value = false;
    submitted.value = false;
}

async function openDetailTournament(tournamentData: Tournament): Promise<void> {
    tournamentStore.tournament = tournamentData;
    detailDialog.value = true;
    teamsPage.value = 1;
    await loadTournamentTeams();
}

async function loadTournamentTeams(): Promise<void> {
    if (!tournamentStore.tournament.id) return;

    const filters: TournamentTeamFilter = {
        tournamentId: tournamentStore.tournament.id,
        sort: teamsSortField.value,
        direction: teamsSortOrder.value,
        perPage: teamsPerPage.value,
        page: teamsPage.value
    };

    await tournamentTeamStore.fetchTournamentTeams(filters);
}

async function onTeamsPageChange(event: DataTablePageEvent): Promise<void> {
    teamsPage.value = event.page + 1;
    teamsPerPage.value = event.rows;
    await loadTournamentTeams();
}

async function onTeamsSort(event: DataTableSortEvent): Promise<void> {
    if (typeof event.sortField === 'string') {
        teamsSortField.value = event.sortField || 'id';
        teamsSortOrder.value = event.sortOrder === 1 ? 'asc' : 'desc';
        await loadTournamentTeams();
    }
}

function openCreateTeam(): void {
    tournamentTeamStore.clearTournamentTeam();
    tournamentTeamStore.tournamentTeam.tournamentId = tournamentStore.tournament.id;
    teamSubmitted.value = false;
    isEditingTeam.value = false;
    teamDialog.value = true;
}

function openUpdateTeam(teamData: TournamentTeam): void {
    tournamentTeamStore.tournamentTeam = { ...teamData };
    tournamentTeamStore.tournamentTeam.teamId = teamData.team.id;
    teamSubmitted.value = false;
    isEditingTeam.value = true;
    teamDialog.value = true;
}

function openDestroyTeam(teamData: TournamentTeam): void {
    tournamentTeamStore.tournamentTeam = teamData;
    deleteTeamDialog.value = true;
}

async function saveTeam(): Promise<void> {
    teamSubmitted.value = true;

    if (tournamentTeamStore.tournamentTeam.teamId && tournamentTeamStore.tournamentTeam.tournamentId) {
        try {
            loadingTeamData.value = true;
            let payload: TournamentTeamPayload | Partial<TournamentTeamPayload>;

            if (tournamentTeamStore.tournamentTeam.id) {
                payload = {
                    tournament_id: tournamentTeamStore.tournamentTeam.tournamentId,
                    team_id: tournamentTeamStore.tournamentTeam.teamId,
                    points: tournamentTeamStore.tournamentTeam.points || 0,
                    position: tournamentTeamStore.tournamentTeam.position || undefined,
                    wins: tournamentTeamStore.tournamentTeam.wins || 0,
                    draws: tournamentTeamStore.tournamentTeam.draws || 0,
                    losses: tournamentTeamStore.tournamentTeam.losses || 0
                };
                await tournamentTeamStore.updateTournamentTeam(tournamentTeamStore.tournamentTeam.id, payload);
                toast.add({ severity: 'success', summary: 'Sucesso', detail: 'Time atualizado com sucesso', life: 3000 });
            } else {
                payload = {
                    tournament_id: tournamentTeamStore.tournamentTeam.tournamentId,
                    team_id: tournamentTeamStore.tournamentTeam.teamId
                };
                await tournamentTeamStore.createTournamentTeam(payload as TournamentTeamPayload);
                toast.add({ severity: 'success', summary: 'Sucesso', detail: 'Time adicionado com sucesso', life: 3000 });
            }

            teamDialog.value = false;
            tournamentTeamStore.clearTournamentTeam();
            await loadTournamentTeams();
        } catch {
            toast.add({ severity: 'error', summary: 'Erro', detail: tournamentTeamStore.error, life: 3000 });
        } finally {
            loadingTeamData.value = false;
        }
    }
}

async function deleteTeam(): Promise<void> {
    if (tournamentTeamStore.tournamentTeam.id) {
        try {
            await tournamentTeamStore.deleteTournamentTeam(tournamentTeamStore.tournamentTeam.id);
            deleteTeamDialog.value = false;
            tournamentTeamStore.clearTournamentTeam();
            toast.add({ severity: 'success', summary: 'Sucesso', detail: 'Time removido com sucesso', life: 3000 });
            await loadTournamentTeams();
        } catch {
            toast.add({ severity: 'error', summary: 'Erro', detail: tournamentTeamStore.error, life: 3000 });
        }
    }
}

function hideTeamDialog(): void {
    teamDialog.value = false;
    teamSubmitted.value = false;
}

function openShieldModal(shieldPath: string): void {
    selectedShield.value = shieldPath;
    shieldDialog.value = true;
}

function exportCSV(): void {
    const data = tournamentStore.tournaments.map((tournament) => ({
        Nome: tournament.name,
        Status: getStatusText(tournament.status),
        'Data Início': tournament.startDate || '-',
        'Data Fim': tournament.endDate || '-',
        'Máx. Participantes': tournament.maxParticipants,
        Público: getPublicText(tournament.isPublic)
    }));

    const headers = Object.keys(data[0]);
    const csvContent = [headers.join(','), ...data.map((row) => headers.map((header) => `"${row[header as keyof typeof row]}"`).join(','))].join('\n');

    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', 'torneios.csv');
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function getStatusLabel(status: string): string {
    switch (status) {
        case 'aberto':
            return 'success';
        case 'em_andamento':
            return 'warning';
        case 'finalizado':
            return 'danger';
        default:
            return 'info';
    }
}

function getStatusText(status: string): string {
    const option = statusOptions.value.find((opt) => opt.value === status);
    return option ? option.label : status;
}

function getPublicLabel(isPublic: boolean): string {
    return isPublic ? 'info' : 'info';
}

function getPublicText(isPublic: boolean): string {
    return isPublic ? 'Público' : 'Privado';
}

function resetFilters(): void {
    filtersObj.status = null;
    filtersObj.isPublic = null;
    searchTerm.value = '';
}

function applyFilters(): void {
    showFiltersModal.value = false;
}
</script>

<template>
    <div class="space-y-6 lg:space-y-8">
        <div class="lg:grid-cols-12 gap-6">
            <div class="lg:col-span-4 -mx-4 sm:mx-0">
                <div class="card">
                    <Toolbar class="mb-6">
                        <template #start>
                            <div class="flex-wrap gap-2 items-center hidden md:flex">
                                <Button label="Adicionar Torneio (F1)" icon="pi pi-plus" severity="secondary" class="mr-2" @click="openCreateTournament" v-tooltip.top="'Cadastrar novo torneio'" />
                                <Select v-model="filtersObj.status" :options="statusFilterOptions" optionLabel="label" optionValue="value" placeholder="Status" class="mr-2" style="width: 140px" v-tooltip.top="'Filtrar por status do torneio'" />
                                <Select
                                    v-model="filtersObj.isPublic"
                                    :options="publicFilterOptions"
                                    optionLabel="label"
                                    optionValue="value"
                                    placeholder="Visibilidade"
                                    class="mr-2"
                                    style="width: 140px"
                                    v-tooltip.top="'Filtrar por visibilidade (Público/Privado)'"
                                />
                            </div>
                            <Button label="Resetar Filtros" icon="pi pi-refresh" severity="secondary" @click="resetFilters" class="mr-2" v-tooltip.top="'Limpar todos os filtros aplicados'" />
                            <Button
                                label="Deletar"
                                icon="pi pi-trash"
                                severity="secondary"
                                @click="openDestroySelected"
                                :disabled="!tournamentStore.selectedTournaments || !tournamentStore.selectedTournaments.length"
                                v-tooltip.top="'Excluir torneios selecionados'"
                            />
                        </template>

                        <template #end>
                            <Button label="Exportar (F4)" icon="pi pi-upload" severity="secondary" @click="exportCSV" v-tooltip.top="'Exportar Dados da Tabela'" />
                        </template>
                    </Toolbar>

                    <Dialog v-model:visible="showFiltersModal" modal header="Filtros" :style="{ width: '30vw' }" :breakpoints="{ '960px': '75vw', '640px': '100vw' }" :maximizable="true">
                        <div class="flex flex-col gap-4">
                            <div class="flex flex-col gap-2">
                                <label for="status">Status</label>
                                <Select v-model="filtersObj.status" :options="statusFilterOptions" optionLabel="label" optionValue="value" placeholder="Status" class="w-full" />
                            </div>
                            <div class="flex flex-col gap-2">
                                <label for="isPublic">Visibilidade</label>
                                <Select v-model="filtersObj.isPublic" :options="publicFilterOptions" optionLabel="label" optionValue="value" placeholder="Visibilidade" class="w-full" />
                            </div>
                        </div>
                        <template #footer>
                            <Button label="Cancelar" icon="pi pi-times" text @click="showFiltersModal = false" />
                            <Button label="Limpar Filtros" icon="pi pi-refresh" severity="secondary" @click="resetFilters" />
                            <Button label="Aplicar" icon="pi pi-check" @click="applyFilters" />
                        </template>
                    </Dialog>

                    <div class="block md:hidden mb-6">
                        <div class="grid grid-cols-1 gap-4">
                            <Button label="Adicionar Torneio" icon="pi pi-plus" severity="secondary" @click="openCreateTournament" class="w-full" />
                            <Button label="Filtros" icon="pi pi-filter" @click="showFiltersModal = true" class="w-full" />
                        </div>

                        <div class="mt-4 flex gap-2">
                            <IconField class="flex-1">
                                <InputIcon>
                                    <i class="pi pi-search" />
                                </InputIcon>
                                <InputText v-model="searchTerm" placeholder="Buscar torneio..." @keyup.enter="onSearch" class="w-full" />
                            </IconField>
                            <Button icon="pi pi-search" severity="secondary" @click="onSearch" :loading="tournamentStore.loading" />
                        </div>
                    </div>

                    <div class="hidden md:block">
                        <DataTable
                            ref="dt"
                            v-model:selection="tournamentStore.selectedTournaments"
                            :value="tournamentStore.tournaments"
                            dataKey="id"
                            :paginator="true"
                            :rows="rowsPerPage"
                            :totalRecords="tournamentStore.pagination.total"
                            :loading="tournamentStore.loading"
                            :first="(currentPage - 1) * rowsPerPage"
                            paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                            :rowsPerPageOptions="[5, 10, 25, 50]"
                            currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} torneios"
                            :lazy="true"
                            @page="onPageChange"
                            @sort="onSort"
                            sortMode="single"
                            :sortField="sortField"
                            :sortOrder="sortOrder === 'asc' ? 1 : -1"
                            class="hidden md:block"
                        >
                            <template #header>
                                <div class="flex flex-wrap gap-2 items-center justify-between">
                                    <h4 class="m-0 flex items-center gap-2">
                                        <span class="bg-gray-100 text-gray-600 rounded-full w-8 h-8 flex items-center justify-center">
                                            <i class="pi pi-fw pi-trophy"></i>
                                        </span>
                                        Torneios
                                    </h4>
                                    <div class="flex gap-2">
                                        <IconField>
                                            <InputIcon>
                                                <i class="pi pi-search" />
                                            </InputIcon>
                                            <InputText v-model="searchTerm" placeholder="Buscar torneio..." @keyup.enter="onSearch" />
                                        </IconField>
                                        <Button icon="pi pi-search" severity="secondary" @click="onSearch" :loading="tournamentStore.loading" v-tooltip.top="'Buscar'" />
                                    </div>
                                </div>
                            </template>

                            <Column field="name" header="Nome" sortable style="min-width: 16rem">
                                <template #body="slotProps">
                                    {{ slotProps.data.name }}
                                </template>
                            </Column>

                            <Column field="status" header="Status" sortable style="min-width: 8rem">
                                <template #body="slotProps">
                                    <Tag :value="getStatusText(slotProps.data.status)" :severity="getStatusLabel(slotProps.data.status)" />
                                </template>
                            </Column>

                            <Column field="startDate" header="Data Início" sortable style="min-width: 10rem">
                                <template #body="slotProps">
                                    {{ slotProps.data.startDate || '-' }}
                                </template>
                            </Column>

                            <Column field="endDate" header="Data Fim" sortable style="min-width: 10rem">
                                <template #body="slotProps">
                                    {{ slotProps.data.endDate || '-' }}
                                </template>
                            </Column>

                            <Column field="maxParticipants" header="Máx. Participantes" sortable style="min-width: 10rem">
                                <template #body="slotProps">
                                    {{ slotProps.data.maxParticipants }}
                                </template>
                            </Column>

                            <Column field="isPublic" header="Visibilidade" sortable style="min-width: 8rem">
                                <template #body="slotProps">
                                    <Tag :value="getPublicText(slotProps.data.isPublic)" :severity="getPublicLabel(slotProps.data.isPublic)" />
                                </template>
                            </Column>

                            <Column :exportable="false" style="min-width: 16rem">
                                <template #body="slotProps">
                                    <Button icon="pi pi-eye" outlined rounded class="mr-2" @click="openDetailTournament(slotProps.data)" v-tooltip.top="'Ver Detalhes'" />
                                    <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="openUpdateTournament(slotProps.data)" v-tooltip.top="'Editar Torneio'" />
                                    <Button icon="pi pi-trash" outlined rounded severity="danger" @click="openDestroyTournament(slotProps.data)" v-tooltip.top="'Excluir Torneio'" />
                                </template>
                            </Column>

                            <template #empty>
                                <div class="text-center py-8 text-gray-500">
                                    <i class="pi pi-fw pi-trophy text-4xl mb-2"></i>
                                    <p>Nenhum torneio encontrado</p>
                                </div>
                            </template>
                        </DataTable>
                    </div>

                    <div class="block md:hidden">
                        <div v-if="tournamentStore.loading" class="text-center py-8">
                            <ProgressSpinner />
                        </div>
                        <div v-else-if="tournamentStore.tournaments.length === 0" class="text-center py-8 text-gray-500">
                            <i class="pi pi-fw pi-trophy text-4xl mb-2"></i>
                            <p>Nenhum torneio encontrado</p>
                        </div>
                        <div v-else class="space-y-4">
                            <Card v-for="tournament in tournamentStore.tournaments" :key="tournament.id" class="h-full flex flex-col rounded-none sm:rounded-xl border border-gray-200 dark:border-gray-700">
                                <template #content>
                                    <div class="p-4 space-y-4 flex-grow">
                                        <div class="flex justify-between items-start mb-3">
                                            <div class="flex-1">
                                                <h3 class="font-semibold text-lg text-gray-900 dark:text-gray-100">{{ tournament.name }}</h3>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ tournament.maxParticipants }} participantes</p>
                                                <div class="flex items-center gap-5 mt-2">
                                                    <Tag :value="getStatusText(tournament.status)" :severity="getStatusLabel(tournament.status)" />
                                                    <Tag :value="getPublicText(tournament.isPublic)" :severity="getPublicLabel(tournament.isPublic)" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-2 gap-4 mb-4">
                                            <div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Data Início</p>
                                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ tournament.startDate || '-' }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Data Fim</p>
                                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ tournament.endDate || '-' }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Máx. Participantes</p>
                                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ tournament.maxParticipants }}</p>
                                            </div>
                                        </div>

                                        <div v-if="tournament.description" class="mb-4 pb-4 border-b border-gray-100 dark:border-gray-700">
                                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Descrição</p>
                                            <p class="text-sm text-gray-700 dark:text-gray-300">{{ tournament.description }}</p>
                                        </div>

                                        <div class="pt-3 border-t border-gray-100 dark:border-gray-700">
                                            <div class="grid grid-cols-1 gap-2 font-sans">
                                                <Button label="Ver Detalhes" size="small" outlined severity="info" class="w-full font-sans text-xs" @click="openDetailTournament(tournament)" />
                                                <Button label="Editar" size="small" outlined class="w-full font-sans text-xs" @click="openUpdateTournament(tournament)" />
                                                <Button label="Excluir" size="small" outlined severity="danger" class="w-full font-sans text-xs" @click="openDestroyTournament(tournament)" />
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </Card>
                        </div>

                        <div v-if="tournamentStore.tournaments.length > 0" class="mt-6 flex justify-center">
                            <Paginator
                                :first="(currentPage - 1) * rowsPerPage"
                                :rows="rowsPerPage"
                                :totalRecords="tournamentStore.pagination.total"
                                @page="onPaginatorPageChange"
                                template="FirstPageLink PrevPageLink CurrentPageReport NextPageLink LastPageLink"
                                currentPageReportTemplate="Página {currentPage} de {totalPages}"
                            />
                        </div>
                    </div>
                </div>

                <Dialog v-model:visible="tournamentDialog" modal header="Detalhes do Torneio" :style="{ width: '50vw' }" :breakpoints="{ '960px': '75vw', '640px': '100vw' }" :maximizable="true">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="col-span-1 md:col-span-3">
                            <label for="name" class="flex items-center justify-between font-bold mb-3">
                                <span>Nome <span class="text-red-500">*</span></span>
                                <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:inline" v-tooltip.top="'Nome identificador do torneio'" />
                            </label>
                            <InputText id="name" v-model.trim="tournamentStore.tournament.name" required autofocus :invalid="submitted && !tournamentStore.tournament.name" placeholder="Digite o nome do torneio" fluid />
                            <small v-if="submitted && !tournamentStore.tournament.name" class="text-red-500">Nome é obrigatório.</small>
                        </div>

                        <div>
                            <label for="status" class="flex items-center justify-between font-bold mb-3">
                                <span>Status <span class="text-red-500">*</span></span>
                                <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:inline" v-tooltip.top="'Define o status atual do torneio'" />
                            </label>
                            <Select
                                id="status"
                                v-model="tournamentStore.tournament.status"
                                :options="statusOptions"
                                optionLabel="label"
                                optionValue="value"
                                placeholder="Selecione o status"
                                :invalid="submitted && !tournamentStore.tournament.status"
                                fluid
                            />
                            <small v-if="submitted && !tournamentStore.tournament.status" class="text-red-500">Status é obrigatório.</small>
                        </div>

                        <div>
                            <label for="maxParticipants" class="flex items-center justify-between font-bold mb-3">
                                <span>Máx. Participantes <span class="text-red-500">*</span></span>
                                <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:inline" v-tooltip.top="'Número máximo de participantes permitidos'" />
                            </label>
                            <InputNumber
                                id="maxParticipants"
                                v-model="tournamentStore.tournament.maxParticipants"
                                :min="1"
                                :invalid="submitted && tournamentStore.tournament.maxParticipants <= 0"
                                placeholder="0"
                                showButtons
                                buttonLayout="horizontal"
                                :step="1"
                                fluid
                            >
                                <template #incrementicon>
                                    <span class="pi pi-plus" />
                                </template>
                                <template #decrementicon>
                                    <span class="pi pi-minus" />
                                </template>
                            </InputNumber>
                            <small v-if="submitted && tournamentStore.tournament.maxParticipants <= 0" class="text-red-500">Máx. participantes deve ser maior que 0.</small>
                        </div>

                        <div>
                            <label for="startDate" class="flex items-center justify-between font-bold mb-3">
                                <span>Data de Início <span class="text-red-500">*</span></span>
                                <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:inline" v-tooltip.top="'Data em que o torneio inicia'" />
                            </label>
                            <DatePicker id="startDate" v-model="tournamentStore.tournament.startDate" dateFormat="dd/mm/yy" placeholder="Selecione a data" :invalid="submitted && !tournamentStore.tournament.startDate" fluid />
                            <small v-if="submitted && !tournamentStore.tournament.startDate" class="text-red-500">Data de início é obrigatória.</small>
                        </div>

                        <div>
                            <label for="endDate" class="flex items-center justify-between font-bold mb-3">
                                <span>Data de Fim <span class="text-red-500">*</span></span>
                                <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:inline" v-tooltip.top="'Data em que o torneio termina'" />
                            </label>
                            <DatePicker id="endDate" v-model="tournamentStore.tournament.endDate" dateFormat="dd/mm/yy" placeholder="Selecione a data" :invalid="submitted && !tournamentStore.tournament.endDate" fluid />
                            <small v-if="submitted && !tournamentStore.tournament.endDate" class="text-red-500">Data de fim é obrigatória.</small>
                        </div>

                        <div>
                            <label for="isPublic" class="flex items-center justify-between font-bold mb-3">
                                <span>Visibilidade</span>
                                <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:inline" v-tooltip.top="'Define se o torneio é público ou privado'" />
                            </label>
                            <Select id="isPublic" v-model="tournamentStore.tournament.isPublic" :options="publicOptions" optionLabel="label" optionValue="value" placeholder="Selecione a visibilidade" fluid />
                        </div>

                        <div class="col-span-1 md:col-span-3">
                            <label for="description" class="flex items-center justify-between font-bold mb-3">
                                <span>Descrição</span>
                                <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:inline" v-tooltip.top="'Descrição detalhada do torneio (opcional)'" />
                            </label>
                            <Textarea id="description" v-model="tournamentStore.tournament.description" rows="3" placeholder="Digite a descrição do torneio" fluid />
                        </div>

                        <div class="col-span-1 md:col-span-3">
                            <label for="awards" class="flex items-center justify-between font-bold mb-3">
                                <span>Prêmios</span>
                                <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:inline" v-tooltip.top="'Descrição dos prêmios para os vencedores (opcional)'" />
                            </label>
                            <Textarea id="awards" v-model="tournamentStore.tournament.awards" rows="2" placeholder="Digite os prêmios do torneio" fluid />
                        </div>

                        <div class="col-span-1 md:col-span-3">
                            <label for="welcomeEmail" class="flex items-center justify-between font-bold mb-3">
                                <span>Email de Boas-Vindas</span>
                                <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:inline" v-tooltip.top="'Mensagem de email a ser enviada para novos participantes (opcional)'" />
                            </label>
                            <Textarea id="welcomeEmail" v-model="tournamentStore.tournament.welcomeEmail" rows="2" placeholder="Digite o email de boas-vindas" fluid />
                        </div>
                    </div>

                    <template #footer>
                        <Button label="Cancelar" icon="pi pi-times" text @click="hideDialog" />
                        <Button label="Salvar" icon="pi pi-check" @click="saveTournament" />
                    </template>
                </Dialog>

                <Dialog v-model:visible="deleteTournamentDialog" :style="{ width: '450px' }" header="Confirmar" :modal="true">
                    <div class="flex items-center gap-4">
                        <i class="pi pi-exclamation-triangle !text-3xl" />
                        <span v-if="tournamentStore.tournament">
                            Tem certeza de que deseja deletar o torneio <b>{{ tournamentStore.tournament.name }}</b
                            >?
                        </span>
                    </div>
                    <template #footer>
                        <Button label="Não" icon="pi pi-times" text @click="deleteTournamentDialog = false" />
                        <Button label="Sim" icon="pi pi-check" @click="deleteTournament" />
                    </template>
                </Dialog>

                <Dialog v-model:visible="deleteTournamentsDialog" :style="{ width: '450px' }" header="Confirmar" :modal="true">
                    <div class="flex items-center gap-4">
                        <i class="pi pi-exclamation-triangle !text-3xl" />
                        <span v-if="tournamentStore.selectedTournaments"> Tem certeza de que deseja deletar os torneios selecionados? </span>
                    </div>
                    <template #footer>
                        <Button label="Não" icon="pi pi-times" text @click="deleteTournamentsDialog = false" />
                        <Button label="Sim" icon="pi pi-check" @click="deleteSelectedTournaments" />
                    </template>
                </Dialog>

                <Dialog v-model:visible="detailDialog" modal header="Detalhes do Torneio - Times" :style="{ width: '90vw' }" :breakpoints="{ '960px': '100vw' }" :maximizable="true">
                    <div v-if="tournamentStore.tournament" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 pb-6 border-b border-gray-200 dark:border-gray-700">
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Nome</p>
                                <p class="font-semibold text-gray-900 dark:text-gray-100">{{ tournamentStore.tournament.name }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Status</p>
                                <Tag :value="getStatusText(tournamentStore.tournament.status)" :severity="getStatusLabel(tournamentStore.tournament.status)" />
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Data Início</p>
                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ tournamentStore.tournament.startDate || '-' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Data Fim</p>
                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ tournamentStore.tournament.endDate || '-' }}</p>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Times do Torneio</h3>
                                <Button label="Adicionar Time" icon="pi pi-plus" severity="secondary" size="small" @click="openCreateTeam" v-tooltip.top="'Adicionar novo time ao torneio'" />
                            </div>

                            <div class="block md:hidden text-center py-4 text-sm text-gray-600 dark:text-gray-400">
                                <i class="pi pi-mobile mr-2 text-gray-600 dark:text-gray-400"></i>
                                Para uma melhor visualização, vire o celular na horizontal
                            </div>

                            <DataTable
                                :value="tournamentTeamStore.tournamentTeams"
                                dataKey="id"
                                :paginator="true"
                                :rows="teamsPerPage"
                                :totalRecords="tournamentTeamStore.pagination.total"
                                :loading="tournamentTeamStore.loading"
                                :first="(teamsPage - 1) * teamsPerPage"
                                paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                                :rowsPerPageOptions="[5, 10, 25]"
                                currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} times"
                                :lazy="true"
                                @page="onTeamsPageChange"
                                @sort="onTeamsSort"
                                sortMode="single"
                                :sortField="teamsSortField"
                                :sortOrder="teamsSortOrder === 'asc' ? 1 : -1"
                            >
                                <Column field="team_id" header="Nome do Time" sortable style="min-width: 16rem">
                                    <template #body="slotProps">
                                        <div class="flex items-center gap-2">
                                            <img
                                                :src="slotProps.data.team?.shieldPath || '/image/logo.png'"
                                                alt="Escudo"
                                                class="w-8 h-8 rounded-full object-cover cursor-pointer"
                                                @click="openShieldModal(slotProps.data.team?.shieldPath || '/image/logo.png')"
                                            />
                                            <span>{{ slotProps.data.team?.name || '-' }}</span>
                                        </div>
                                    </template>
                                </Column>

                                <Column field="position" header="Posição" sortable style="min-width: 10rem">
                                    <template #body="slotProps">
                                        {{ slotProps.data.position || '-' }}
                                    </template>
                                </Column>

                                <Column field="points" header="Pontos" sortable style="min-width: 10rem">
                                    <template #body="slotProps">
                                        {{ slotProps.data.points || 0 }}
                                    </template>
                                </Column>

                                <Column field="wins" header="Vitórias" sortable style="min-width: 10rem">
                                    <template #body="slotProps">
                                        {{ slotProps.data.wins || 0 }}
                                    </template>
                                </Column>

                                <Column field="draws" header="Empates" sortable style="min-width: 10rem">
                                    <template #body="slotProps">
                                        {{ slotProps.data.draws || 0 }}
                                    </template>
                                </Column>

                                <Column field="losses" header="Derrotas" sortable style="min-width: 10rem">
                                    <template #body="slotProps">
                                        {{ slotProps.data.losses || 0 }}
                                    </template>
                                </Column>

                                <Column :exportable="false" style="min-width: 12rem">
                                    <template #body="slotProps">
                                        <Button icon="pi pi-pencil" outlined rounded class="mr-2" size="small" @click="openUpdateTeam(slotProps.data)" v-tooltip.top="'Editar Time'" />
                                        <Button icon="pi pi-trash" outlined rounded severity="danger" size="small" @click="openDestroyTeam(slotProps.data)" v-tooltip.top="'Remover Time'" />
                                    </template>
                                </Column>

                                <template #empty>
                                    <div class="text-center py-8 text-gray-500">Nenhum time adicionado a este torneio</div>
                                </template>
                            </DataTable>
                        </div>
                    </div>

                    <template #footer>
                        <Button label="Fechar" icon="pi pi-times" text @click="detailDialog = false" />
                    </template>
                </Dialog>

                <Dialog v-model:visible="teamDialog" modal :header="isEditingTeam ? 'Editar Time' : 'Adicionar Time'" :style="{ width: '50vw' }" :breakpoints="{ '960px': '75vw', '640px': '100vw' }" :maximizable="true">
                    <BlockUI :blocked="loadingTeamData" />
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div v-if="!isEditingTeam" class="col-span-1 md:col-span-2">
                            <label for="teamId" class="flex items-center justify-between font-bold mb-3">
                                <span>ID Público do Time <span class="text-red-500">*</span></span>
                                <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:inline" v-tooltip.top="'Digite o ID do time a adicionar'" />
                            </label>
                            <InputNumber id="teamId" v-model="tournamentTeamStore.tournamentTeam.teamId" :min="1" placeholder="Digite o ID do time" :invalid="teamSubmitted && !tournamentTeamStore.tournamentTeam.teamId" fluid />
                            <small v-if="teamSubmitted && !tournamentTeamStore.tournamentTeam.teamId" class="text-red-500">ID do time é obrigatório.</small>
                        </div>

                        <div v-if="isEditingTeam" class="col-span-1 md:col-span-2">
                            <label for="teamName" class="flex items-center justify-between font-bold mb-3">
                                <span>Nome do Time</span>
                                <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:inline" v-tooltip.top="'Nome do time selecionado'" />
                            </label>
                            <InputText id="teamName" v-model="tournamentTeamStore.tournamentTeam.team.name" placeholder="Nome do time" :readonly="true" fluid />
                        </div>

                        <div v-if="isEditingTeam">
                            <label for="points" class="flex items-center justify-between font-bold mb-3">
                                <span>Pontos</span>
                                <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:inline" v-tooltip.top="'Pontos do time no torneio'" />
                            </label>
                            <InputNumber id="points" v-model="tournamentTeamStore.tournamentTeam.points" :min="0" placeholder="0" showButtons buttonLayout="horizontal" :step="1" fluid>
                                <template #incrementicon>
                                    <span class="pi pi-plus" />
                                </template>
                                <template #decrementicon>
                                    <span class="pi pi-minus" />
                                </template>
                            </InputNumber>
                        </div>

                        <div v-if="isEditingTeam">
                            <label for="position" class="flex items-center justify-between font-bold mb-3">
                                <span>Posição</span>
                                <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:inline" v-tooltip.top="'Posição final do time no torneio'" />
                            </label>
                            <InputNumber id="position" v-model="tournamentTeamStore.tournamentTeam.position" :min="1" placeholder="1" showButtons buttonLayout="horizontal" :step="1" fluid>
                                <template #incrementicon>
                                    <span class="pi pi-plus" />
                                </template>
                                <template #decrementicon>
                                    <span class="pi pi-minus" />
                                </template>
                            </InputNumber>
                        </div>

                        <div v-if="isEditingTeam">
                            <label for="wins" class="flex items-center justify-between font-bold mb-3">
                                <span>Vitórias</span>
                                <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:inline" v-tooltip.top="'Número de vitórias do time'" />
                            </label>
                            <InputNumber id="wins" v-model="tournamentTeamStore.tournamentTeam.wins" :min="0" placeholder="0" showButtons buttonLayout="horizontal" :step="1" fluid>
                                <template #incrementicon>
                                    <span class="pi pi-plus" />
                                </template>
                                <template #decrementicon>
                                    <span class="pi pi-minus" />
                                </template>
                            </InputNumber>
                        </div>

                        <div v-if="isEditingTeam">
                            <label for="draws" class="flex items-center justify-between font-bold mb-3">
                                <span>Empates</span>
                                <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:inline" v-tooltip.top="'Número de empates do time'" />
                            </label>
                            <InputNumber id="draws" v-model="tournamentTeamStore.tournamentTeam.draws" :min="0" placeholder="0" showButtons buttonLayout="horizontal" :step="1" fluid>
                                <template #incrementicon>
                                    <span class="pi pi-plus" />
                                </template>
                                <template #decrementicon>
                                    <span class="pi pi-minus" />
                                </template>
                            </InputNumber>
                        </div>

                        <div v-if="isEditingTeam">
                            <label for="losses" class="flex items-center justify-between font-bold mb-3">
                                <span>Derrotas</span>
                                <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:inline" v-tooltip.top="'Número de derrotas do time'" />
                            </label>
                            <InputNumber id="losses" v-model="tournamentTeamStore.tournamentTeam.losses" :min="0" placeholder="0" showButtons buttonLayout="horizontal" :step="1" fluid>
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
                        <Button label="Cancelar" icon="pi pi-times" text @click="hideTeamDialog" />
                        <Button label="Salvar" icon="pi pi-check" @click="saveTeam" />
                    </template>
                </Dialog>

                <Dialog v-model:visible="deleteTeamDialog" :style="{ width: '450px' }" header="Confirmar" :modal="true">
                    <div class="flex items-center gap-4">
                        <i class="pi pi-exclamation-triangle !text-3xl" />
                        <span v-if="tournamentTeamStore.tournamentTeam">
                            Tem certeza de que deseja remover o time <b>{{ tournamentTeamStore.tournamentTeam.team?.name }}</b> do torneio?
                        </span>
                    </div>
                    <template #footer>
                        <Button label="Não" icon="pi pi-times" text @click="deleteTeamDialog = false" />
                        <Button label="Sim" icon="pi pi-check" @click="deleteTeam" />
                    </template>
                </Dialog>

                <Dialog v-model:visible="shieldDialog" :style="{ width: '500px' }" header="Escudo do Time" :modal="true">
                    <div class="flex justify-center">
                        <img :src="selectedShield" alt="Escudo Ampliada" class="max-w-full max-h-96 object-contain" />
                    </div>
                    <template #footer>
                        <Button label="Fechar" icon="pi pi-times" @click="shieldDialog = false" />
                    </template>
                </Dialog>
            </div>
        </div>
    </div>
</template>
