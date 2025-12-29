<script setup lang="ts">
import { onMounted, ref, computed, watch } from 'vue';
import { useFieldStore } from '@/stores/company/fieldStore';
import { useFieldTypeStore } from '@/stores/company/select/fieldTypeStore';
import { useFieldSurfaceStore } from '@/stores/company/select/fieldSurfaceStore';
import { useFieldSizeStore } from '@/stores/company/select/fieldSizeStore';
import { useFieldItemStore } from '@/stores/company/select/fieldItemStore';
import type { Field } from '@/types/company/field';
import type { FieldPayload } from '@/types/company/field';
import type { FieldSchedule } from '@/types/company/field/FieldSchedule';
import type { DataTableSortEvent, DataTablePageEvent } from 'primevue/datatable';
import type { FileUploadSelectEvent } from 'primevue/fileupload';
import { useToast } from 'primevue/usetoast';
import { useFormat } from '@/utils/useFormat';
import { useKeyboardShortcuts } from '@/utils/useKeyboardShortcuts';

const fieldStore = useFieldStore();
const fieldTypeStore = useFieldTypeStore();
const fieldSurfaceStore = useFieldSurfaceStore();
const fieldSizeStore = useFieldSizeStore();
const fieldItemStore = useFieldItemStore();

const toast = useToast();
const { formatCurrency } = useFormat();

const searchTerm = ref('');
const currentPage = ref(1);
const rowsPerPage = ref(10);
const sortField = ref('id');
const sortOrder = ref<'asc' | 'desc'>('asc');
const activeTab = ref('dados-basicos');

const fieldDialog = ref(false);
const deleteFieldDialog = ref(false);
const deleteFieldsDialog = ref(false);
const submitted = ref(false);
const imageUploadDialog = ref(false);
const isUploading = ref(false);
const zoomDialog = ref(false);
const selectedFieldForImage = ref<Field | null>(null);
const zoomedImage = ref<string | null>(null);
const scheduleFilterDay = ref<number | null>(null);
const selectedReferenceField = ref<Field | null>(null);
const originalSchedules = ref<FieldSchedule[]>([]);

const newSchedule = ref<FieldSchedule>({
    dayOfWeek: 0,
    startTime: '',
    endTime: ''
});

const statusOptions = ref<{ label: string; value: boolean }[]>([
    { label: 'Ativo', value: true },
    { label: 'Inativo', value: false }
]);

const dayOfWeekOptions = ref([
    { label: 'Segunda-feira', value: 1 },
    { label: 'Terça-feira', value: 2 },
    { label: 'Quarta-feira', value: 3 },
    { label: 'Quinta-feira', value: 4 },
    { label: 'Sexta-feira', value: 5 },
    { label: 'Sábado', value: 6 },
    { label: 'Domingo', value: 7 }
]);

const allowExtraHourOptions = ref<{ label: string; value: boolean }[]>([
    { label: 'Sim', value: true },
    { label: 'Não', value: false }
]);

const timeOptions = computed(() => {
    const options = [];
    for (let i = 0; i < 24; i++) {
        const hour = i.toString().padStart(2, '0');
        options.push({ label: `${hour}:00`, value: `${hour}:00` });
    }
    return options;
});

const selectedSchedules = computed({
    get: () => fieldStore.field.schedules || [],
    set: (value) => {
        if (fieldStore.field.schedules) {
            fieldStore.field.schedules = value;
        }
    }
});

const filteredSchedules = computed(() => {
    let schedules = selectedSchedules.value;
    if (scheduleFilterDay.value !== null) {
        schedules = schedules.filter((schedule) => schedule.dayOfWeek === scheduleFilterDay.value);
    }
    return schedules.sort((a, b) => {
        if (a.dayOfWeek !== b.dayOfWeek) {
            return a.dayOfWeek - b.dayOfWeek;
        }
        return a.startTime.localeCompare(b.startTime);
    });
});

const groupedSchedules = computed(() => {
    const groups: Record<number, FieldSchedule[]> = {};
    filteredSchedules.value.forEach((schedule) => {
        if (!groups[schedule.dayOfWeek]) {
            groups[schedule.dayOfWeek] = [];
        }
        groups[schedule.dayOfWeek].push(schedule);
    });
    let sortedDays = Object.keys(groups)
        .map(Number)
        .sort((a, b) => a - b);

    if (scheduleFilterDay.value !== null && !groups[scheduleFilterDay.value]) {
        sortedDays.push(scheduleFilterDay.value);
        sortedDays = sortedDays.sort((a, b) => a - b);
        groups[scheduleFilterDay.value] = [];
    }

    return sortedDays.map((day) => ({
        day,
        dayName: getDayName(day),
        schedules: groups[day].sort((a, b) => a.startTime.localeCompare(b.startTime))
    }));
});

const referenceFieldOptions = computed(() => {
    return fieldStore.fields.filter((f) => f.schedules && f.schedules.length > 0).map((f) => ({ label: f.name, value: f }));
});

useKeyboardShortcuts(openCreateField, saveField, fieldDialog, exportCSV);

watch(
    () => fieldStore.pagination?.currentPage,
    (newPage) => {
        if (newPage && newPage !== currentPage.value) {
            currentPage.value = newPage;
        }
    }
);

watch(
    () => newSchedule.value.startTime,
    (newStartTime) => {
        if (newStartTime && !newSchedule.value.endTime) {
            const startHour = parseInt(newStartTime.split(':')[0]);
            const endHour = (startHour + 1) % 24;
            newSchedule.value.endTime = `${endHour.toString().padStart(2, '0')}:00`;
        }
    }
);

onMounted(async () => {
    try {
        await Promise.all([loadFields(), fieldTypeStore.fetchFieldTypes(), fieldSurfaceStore.fetchFieldSurfaces(), fieldSizeStore.fetchFieldSizes(), fieldItemStore.fetchFieldItems()]);
    } catch {
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: 'Erro ao carregar dados da página',
            life: 5000
        });
    }
});

async function loadFields(): Promise<void> {
    const filters = {
        search: searchTerm.value || undefined,
        sort: sortField.value,
        direction: sortOrder.value,
        perPage: rowsPerPage.value,
        page: currentPage.value
    };

    await fieldStore.fetchFields(filters);
}

async function onSearch(): Promise<void> {
    currentPage.value = 1;
    await loadFields();
}

async function onPageChange(event: DataTablePageEvent): Promise<void> {
    currentPage.value = event.page + 1;
    rowsPerPage.value = event.rows;
    await loadFields();
}

async function onPaginatorPageChange(event: { page: number; first: number; rows: number }): Promise<void> {
    currentPage.value = event.page + 1;
    rowsPerPage.value = event.rows;
    await loadFields();
}

async function onSort(event: DataTableSortEvent): Promise<void> {
    if (typeof event.sortField === 'string') {
        const mappedField = sortFieldMapping[event.sortField];
        if (mappedField) {
            sortField.value = mappedField;
            sortOrder.value = event.sortOrder === 1 ? 'asc' : 'desc';
        } else {
            sortField.value = 'name';
            sortOrder.value = 'asc';
        }
        await loadFields();
    }
}

async function saveField(): Promise<void> {
    submitted.value = true;

    if (
        (fieldStore.field.name?.trim(),
        fieldStore.field.pricePerHour &&
            fieldStore.field.pricePerHour > 0 &&
            fieldStore.field.fieldTypeId &&
            fieldStore.field.fieldTypeId > 0 &&
            fieldStore.field.fieldSurfaceId &&
            fieldStore.field.fieldSurfaceId > 0 &&
            fieldStore.field.fieldSizeId &&
            fieldStore.field.fieldSizeId > 0)
    ) {
        try {
            const payload: FieldPayload = {
                name: fieldStore.field.name,
                description: fieldStore.field.description || '',
                price_per_hour: fieldStore.field.pricePerHour || 0,
                extra_hour_price: fieldStore.field.extraHourPrice || 0,
                field_type_id: fieldStore.field.fieldTypeId,
                field_surface_id: fieldStore.field.fieldSurfaceId,
                field_size_id: fieldStore.field.fieldSizeId,
                is_active: fieldStore.field.isActive,
                is_allows_extra_hour: fieldStore.field.isAllowsExtraHour || false
            };

            if (fieldStore.field.selectedItemIds && fieldStore.field.selectedItemIds.length > 0) {
                payload.item_ids = fieldStore.field.selectedItemIds;
            }

            if (fieldStore.field.schedules && fieldStore.field.schedules.length > 0) {
                payload.schedules = fieldStore.field.schedules.map((schedule) => ({
                    id: schedule.id,
                    day_of_week: schedule.dayOfWeek,
                    start_time: schedule.startTime,
                    end_time: schedule.endTime
                }));
            }

            if (fieldStore.field.id) {
                await fieldStore.updateField(fieldStore.field.id, payload);
                toast.add({ severity: 'success', summary: 'Sucesso', detail: 'Arena atualizada com sucesso', life: 3000 });
            } else {
                await fieldStore.createField(payload);
                toast.add({ severity: 'success', summary: 'Sucesso', detail: 'Arena criada com sucesso', life: 3000 });
            }

            fieldDialog.value = false;
            fieldStore.clearField();
            await loadFields();
        } catch {
            toast.add({
                severity: 'error',
                summary: 'Erro',
                detail: fieldStore.error,
                life: 3000
            });
        }
    }
}

async function onFileSelect(event: FileUploadSelectEvent): Promise<void> {
    const file = Array.isArray(event.files) ? event.files[0] : event.files;
    if (file && selectedFieldForImage.value) {
        isUploading.value = true;
        try {
            await fieldStore.updateImage(selectedFieldForImage.value.id, file);
            toast.add({ severity: 'success', summary: 'Sucesso', detail: 'Imagem atualizada com sucesso', life: 3000 });
            imageUploadDialog.value = false;
            selectedFieldForImage.value = null;
            await loadFields();
        } catch {
            if (fieldStore.error) {
                toast.add({
                    severity: 'error',
                    summary: 'Erro',
                    detail: fieldStore.error,
                    life: 3000
                });
            }
        } finally {
            isUploading.value = false;
        }
    }
}

async function deleteField(): Promise<void> {
    if (fieldStore.field.id) {
        try {
            await fieldStore.deleteField(fieldStore.field.id);
            deleteFieldDialog.value = false;
            fieldStore.clearField();
            toast.add({ severity: 'success', summary: 'Sucesso', detail: 'Arena deletada com sucesso', life: 3000 });
            await loadFields();
        } catch {
            toast.add({
                severity: 'error',
                summary: 'Erro',
                detail: fieldStore.error,
                life: 3000
            });
        }
    }
}

async function deleteSelectedFields(): Promise<void> {
    try {
        await fieldStore.deleteSelectedFields();
        deleteFieldsDialog.value = false;
        toast.add({ severity: 'success', summary: 'Sucesso', detail: 'Arenas deletadas com sucesso', life: 3000 });
        await loadFields();
    } catch {
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: fieldStore.error,
            life: 3000
        });
    }
}

const sortFieldMapping: Record<string, string> = {
    name: 'name',
    pricePerHour: 'price_per_hour'
};

function openCreateField(): void {
    fieldStore.clearField();
    fieldStore.field.pricePerHour = null;
    fieldStore.field.extraHourPrice = null;
    newSchedule.value = { dayOfWeek: 0, startTime: '', endTime: '' };
    scheduleFilterDay.value = null;
    activeTab.value = 'dados-basicos';
    fieldDialog.value = true;
}

function openUpdateField(fieldData: Field): void {
    fieldStore.field = { ...fieldData };

    if (!fieldStore.field.selectedItemIds) {
        fieldStore.field.selectedItemIds = [];
    }
    if (!fieldStore.field.schedules) {
        fieldStore.field.schedules = [];
    }

    originalSchedules.value = JSON.parse(JSON.stringify(fieldStore.field.schedules));
    newSchedule.value = { dayOfWeek: 0, startTime: '', endTime: '' };
    scheduleFilterDay.value = null;

    fieldDialog.value = true;
}

function openImageUploadModal(field: Field): void {
    selectedFieldForImage.value = field;
    imageUploadDialog.value = true;
}

function hideDialog(): void {
    fieldDialog.value = false;
    submitted.value = false;
    newSchedule.value = {
        dayOfWeek: 0,
        startTime: '',
        endTime: ''
    };
}

function confirmDeleteField(fieldData: Field): void {
    fieldStore.field = fieldData;
    deleteFieldDialog.value = true;
}

const zoomImage = (src: string) => {
    zoomedImage.value = src;
    zoomDialog.value = true;
};

function exportCSV(): void {
    const data = fieldStore.fields.map((field) => ({
        Nome: field.name,
        Descrição: field.description,
        'Preço/Hora': formatCurrency(field.pricePerHour),
        'Extra (30min)': formatCurrency(field.extraHourPrice),
        'Permite 30 min': field.isAllowsExtraHour ? 'Sim' : 'Não',
        Tipo: field.fieldType?.name || '',
        Superfície: field.fieldSurface?.name || '',
        Tamanho: field.fieldSize?.name || '',
        Status: getStatusText(field.isActive)
    }));

    const headers = Object.keys(data[0]);
    const csvContent = [headers.join(','), ...data.map((row) => headers.map((header) => `"${row[header as keyof typeof row]}"`).join(','))].join('\n');

    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', 'arenas.csv');
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function confirmDeleteSelected(): void {
    deleteFieldsDialog.value = true;
}

function addSchedule(): void {
    if (newSchedule.value.dayOfWeek !== undefined && newSchedule.value.startTime && newSchedule.value.endTime) {
        if (!fieldStore.field.schedules) {
            fieldStore.field.schedules = [];
        }

        const isDuplicate = fieldStore.field.schedules.some((schedule) => schedule.dayOfWeek === newSchedule.value.dayOfWeek && schedule.startTime === newSchedule.value.startTime && schedule.endTime === newSchedule.value.endTime);

        if (isDuplicate) {
            toast.add({
                severity: 'warn',
                summary: 'Aviso',
                detail: 'Este horário já foi adicionado',
                life: 3000
            });
            return;
        }

        fieldStore.field.schedules.push({ ...newSchedule.value });
        const lastSchedule = fieldStore.field.schedules[fieldStore.field.schedules.length - 1];
        newSchedule.value = {
            dayOfWeek: lastSchedule.dayOfWeek,
            startTime: lastSchedule.endTime,
            endTime: ''
        };
    }
}

function removeSchedule(schedule: FieldSchedule): void {
    if (fieldStore.field.schedules) {
        const originalIndex = fieldStore.field.schedules.findIndex((s) => s.dayOfWeek === schedule.dayOfWeek && s.startTime === schedule.startTime && s.endTime === schedule.endTime);
        if (originalIndex !== -1) {
            fieldStore.field.schedules.splice(originalIndex, 1);
        }
    }
}

function getDayName(dayOfWeek: number): string {
    const days = ['Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado', 'Domingo'];
    return days[dayOfWeek - 1] || 'Indefinido';
}

function getStatusLabel(status: boolean): string {
    return status ? 'success' : 'danger';
}

function getStatusText(status: boolean): string {
    return status ? 'Ativo' : 'Inativo';
}

function syncSchedulesFromReference(): void {
    if (!selectedReferenceField.value || !selectedReferenceField.value.schedules) {
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: 'Arena de referência não selecionada ou sem horários.',
            life: 3000
        });
        return;
    }

    fieldStore.field.schedules = selectedReferenceField.value.schedules.map((schedule) => ({
        ...schedule,
        id: undefined
    }));

    toast.add({
        severity: 'success',
        summary: 'Sucesso',
        detail: 'Horários sincronizados com sucesso.',
        life: 3000
    });

    selectedReferenceField.value = null;
}

function hasScheduleChanges(): boolean {
    const originalStr = JSON.stringify(originalSchedules.value.sort((a, b) => a.dayOfWeek - b.dayOfWeek || a.startTime.localeCompare(b.startTime)));
    const currentStr = JSON.stringify((fieldStore.field.schedules || []).sort((a, b) => a.dayOfWeek - b.dayOfWeek || a.startTime.localeCompare(b.startTime)));
    return originalStr !== currentStr;
}
</script>

<template>
    <div class="space-y-6 lg:space-y-8">
        <div class="lg:grid-cols-12 gap-6">
            <div class="lg:col-span-4 -mx-4 sm:mx-0">
                <div class="card">
                    <Toolbar class="mb-6">
                        <template #start>
                            <div class="hidden md:block">
                                <Button label="Adicionar Arena (F1)" icon="pi pi-plus" severity="secondary" class="mr-2" @click="openCreateField" v-tooltip.top="'Cadastrar nova arena'" />
                                <Button
                                    label="Deletar"
                                    icon="pi pi-trash"
                                    severity="secondary"
                                    @click="confirmDeleteSelected"
                                    :disabled="!fieldStore.selectedFields || !fieldStore.selectedFields.length"
                                    v-tooltip.top="'Excluir arenas selecionadas'"
                                />
                            </div>
                        </template>

                        <template #end>
                            <Button label="Exportar (F4)" icon="pi pi-upload" severity="secondary" @click="exportCSV" v-tooltip.top="'Exportar Dados da Tabela'" />
                        </template>
                    </Toolbar>

                    <div class="block md:hidden mb-6">
                        <div class="grid grid-cols-1 gap-4">
                            <Button label="Adicionar Arena" icon="pi pi-plus" severity="secondary" @click="openCreateField" class="w-full" />
                        </div>

                        <div class="mt-4 flex gap-2">
                            <IconField class="flex-1">
                                <InputIcon>
                                    <i class="pi pi-search" />
                                </InputIcon>
                                <InputText v-model="searchTerm" placeholder="Buscar arenas..." @keyup.enter="onSearch" class="w-full" />
                            </IconField>
                            <Button icon="pi pi-search" severity="secondary" @click="onSearch" :loading="fieldStore.loading" />
                        </div>
                    </div>

                    <div class="hidden md:block">
                        <DataTable
                            ref="dt"
                            v-model:selection="fieldStore.selectedFields"
                            :value="fieldStore.fields"
                            dataKey="id"
                            :paginator="true"
                            :rows="rowsPerPage"
                            :totalRecords="fieldStore.pagination.total"
                            :loading="fieldStore.loading"
                            :first="(currentPage - 1) * rowsPerPage"
                            paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                            :rowsPerPageOptions="[5, 10, 25, 50]"
                            currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} arenas"
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
                                            <i class="pi pi-fw pi-map"></i>
                                        </span>
                                        Gerenciar Arenas
                                    </h4>
                                    <div class="flex gap-2">
                                        <IconField>
                                            <InputIcon>
                                                <i class="pi pi-search" />
                                            </InputIcon>
                                            <InputText v-model="searchTerm" placeholder="Buscar arenas..." @keyup.enter="onSearch" />
                                        </IconField>
                                        <Button icon="pi pi-search" severity="secondary" @click="onSearch" :loading="fieldStore.loading" v-tooltip.top="'Buscar'" />
                                    </div>
                                </div>
                            </template>

                            <Column selectionMode="multiple" style="width: 3rem" :exportable="false"></Column>

                            <Column header="Imagem" style="width: 6rem" :exportable="false">
                                <template #body="slotProps">
                                    <img
                                        v-if="slotProps.data.imagePath && typeof slotProps.data.imagePath === 'string'"
                                        :src="slotProps.data.imagePath"
                                        alt="Imagem da arena"
                                        class="w-16 h-16 object-cover rounded cursor-pointer"
                                        @click="zoomImage(slotProps.data.imagePath)"
                                    />
                                    <div v-else class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center">
                                        <i class="pi pi-image text-gray-400"></i>
                                    </div>
                                </template>
                            </Column>

                            <Column field="name" header="Nome" sortable style="min-width: 8rem">
                                <template #body="slotProps">
                                    {{ slotProps.data.name }}
                                </template>
                            </Column>

                            <Column field="price_per_hour" header="Preço/Hora" style="min-width: 8rem">
                                <template #body="slotProps">
                                    {{ formatCurrency(slotProps.data.pricePerHour) }}
                                </template>
                            </Column>

                            <Column field="extra_hour_price" header="Extra (30min)" style="min-width: 8rem">
                                <template #body="slotProps">
                                    {{ formatCurrency(slotProps.data.extraHourPrice) }}
                                </template>
                            </Column>

                            <Column field="is_allows_extra_hour" header="Permite 30 min" style="min-width: 8rem">
                                <template #body="slotProps">
                                    <Tag :value="slotProps.data.isAllowsExtraHour ? 'Sim' : 'Não'" :severity="slotProps.data.isAllowsExtraHour ? 'success' : 'danger'" />
                                </template>
                            </Column>

                            <Column field="field_type_id" header="Tipo" style="min-width: 8rem">
                                <template #body="slotProps">
                                    {{ slotProps.data.fieldType?.name || '-' }}
                                </template>
                            </Column>

                            <Column field="field_surface_id" header="Superfície" style="min-width: 8rem">
                                <template #body="slotProps">
                                    {{ slotProps.data.fieldSurface?.name || '-' }}
                                </template>
                            </Column>

                            <Column field="field_size_id" header="Tamanho" style="min-width: 8rem">
                                <template #body="slotProps">
                                    {{ slotProps.data.fieldSize?.name || '-' }}
                                </template>
                            </Column>

                            <Column field="is_active" header="Status" style="min-width: 8rem">
                                <template #body="slotProps">
                                    <Tag :value="getStatusText(slotProps.data.isActive)" :severity="getStatusLabel(slotProps.data.isActive)" />
                                </template>
                            </Column>

                            <Column :exportable="false" style="min-width: 8rem">
                                <template #body="slotProps">
                                    <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="openUpdateField(slotProps.data)" v-tooltip.top="'Editar Arena'" />
                                    <Button icon="pi pi-camera" outlined rounded class="mr-2" @click="openImageUploadModal(slotProps.data)" v-tooltip.top="'Alterar Imagem'" />
                                    <Button icon="pi pi-trash" outlined rounded severity="danger" @click="confirmDeleteField(slotProps.data)" v-tooltip.top="'Excluir Arena'" />
                                </template>
                            </Column>

                            <template #empty>
                                <div class="text-center py-8 text-gray-500">
                                    <i class="pi pi-fw pi-map text-4xl mb-2"></i>
                                    <p>Nenhuma arena encontrada</p>
                                </div>
                            </template>
                        </DataTable>
                    </div>

                    <div class="block md:hidden">
                        <div v-if="fieldStore.loading" class="text-center py-8">
                            <ProgressSpinner />
                        </div>

                        <div v-else-if="fieldStore.fields.length === 0" class="text-center py-8 text-gray-500">
                            <i class="pi pi-fw pi-map text-4xl mb-2"></i>
                            <p>Nenhuma arena encontrada</p>
                        </div>

                        <div v-else class="space-y-4">
                            <Card v-for="field in fieldStore.fields" :key="field.id" class="h-full flex flex-col rounded-none sm:rounded-xl border border-gray-200 dark:border-gray-700">
                                <template #content>
                                    <div class="p-4 space-y-4 flex-grow">
                                        <div class="flex justify-between items-start mb-3">
                                            <div class="flex-1">
                                                <div class="mb-2">
                                                    <img
                                                        v-if="field.imagePath && typeof field.imagePath === 'string'"
                                                        :src="field.imagePath"
                                                        alt="Imagem da arena"
                                                        class="w-full h-40 object-cover rounded cursor-pointer"
                                                        @click="zoomImage(field.imagePath)"
                                                    />
                                                    <div v-else class="w-full h-40 bg-gray-200 rounded flex items-center justify-center">
                                                        <i class="pi pi-image text-3xl text-gray-400"></i>
                                                    </div>
                                                </div>
                                                <h3 class="font-semibold text-lg text-gray-900 dark:text-gray-100">{{ field.name }}</h3>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ field.fieldType?.name || '-' }}</p>
                                                <div class="flex gap-2 items-center">
                                                    <Tag :value="getStatusText(field.isActive)" :severity="getStatusLabel(field.isActive)" />
                                                    <Tag :value="field.isAllowsExtraHour ? 'Permite 30 min' : 'Sem 30 min'" :severity="field.isAllowsExtraHour ? 'success' : 'danger'" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-2 gap-4 mb-4">
                                            <div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Preço/Hora</p>
                                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ formatCurrency(field.pricePerHour) }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Extra (30min)</p>
                                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ formatCurrency(field.extraHourPrice) }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Superfície</p>
                                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ field.fieldSurface?.name || '-' }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Tamanho</p>
                                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ field.fieldSize?.name || '-' }}</p>
                                            </div>
                                        </div>

                                        <div class="pt-3 border-t border-gray-100 dark:border-gray-700">
                                            <div class="grid grid-cols-1 gap-2 font-sans">
                                                <Button label="Editar" size="small" outlined class="w-full font-sans text-xs" @click="openUpdateField(field)" />
                                                <Button label="Imagem" size="small" outlined class="w-full font-sans text-xs" @click="openImageUploadModal(field)" />
                                                <Button label="Excluir" size="small" outlined severity="danger" class="w-full font-sans text-xs" @click="confirmDeleteField(field)" />
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </Card>
                        </div>

                        <div v-if="fieldStore.fields.length > 0" class="mt-6 flex justify-center">
                            <Paginator
                                :first="(currentPage - 1) * rowsPerPage"
                                :rows="rowsPerPage"
                                :totalRecords="fieldStore.pagination.total"
                                @page="onPaginatorPageChange"
                                template="FirstPageLink PrevPageLink CurrentPageReport NextPageLink LastPageLink"
                                currentPageReportTemplate="Página {currentPage} de {totalPages}"
                            />
                        </div>
                    </div>
                </div>

                <Dialog v-model:visible="fieldDialog" modal header="Detalhes da Arena" :style="{ width: '50vw' }" :breakpoints="{ '960px': '75vw', '640px': '100vw' }" :maximizable="true">
                    <Tabs value="dados-basicos">
                        <TabList>
                            <Tab value="dados-basicos">
                                <div class="flex items-center gap-2">
                                    <i class="pi pi-info-circle" />
                                    <span>Dados Básicos</span>
                                </div>
                            </Tab>
                            <Tab value="horarios">
                                <div class="flex items-center gap-2">
                                    <i class="pi pi-clock" />
                                    <span>Horários</span>
                                </div>
                            </Tab>
                        </TabList>

                        <TabPanels>
                            <TabPanel value="dados-basicos">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label for="name" class="flex items-center justify-between font-bold mb-3">
                                            <span>Nome <span class="text-red-500">*</span></span>
                                            <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:inline" v-tooltip.top="'Nome identificador da arena (ex: Campo 1, Quadra A)'" />
                                        </label>
                                        <InputText id="name" v-model.trim="fieldStore.field.name" :invalid="submitted && (!fieldStore.field.name || !fieldStore.field.name?.trim())" placeholder="Digite o nome da arena" fluid />
                                        <small v-if="submitted && (!fieldStore.field.name || !fieldStore.field.name?.trim())" class="text-red-500"> Nome da arena é obrigatório. </small>
                                    </div>

                                    <div>
                                        <label for="pricePerHour" class="flex items-center justify-between font-bold mb-3">
                                            <span>Preço por Hora <span class="text-red-500">*</span></span>
                                            <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:inline" v-tooltip.top="'Valor cobrado por hora de utilização da arena'" />
                                        </label>
                                        <InputNumber
                                            id="pricePerHour"
                                            v-model="fieldStore.field.pricePerHour"
                                            mode="currency"
                                            currency="BRL"
                                            locale="pt-BR"
                                            :minFractionDigits="2"
                                            placeholder="R$ 0,00"
                                            :invalid="submitted && (!fieldStore.field.pricePerHour || fieldStore.field.pricePerHour <= 0)"
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
                                        <small v-if="submitted && (!fieldStore.field.pricePerHour || fieldStore.field.pricePerHour <= 0)" class="text-red-500"> Preço por hora é obrigatório. </small>
                                        <small class="text-gray-500 block mt-1 md:hidden">Se tiver dificuldade em digitar o valor, apague tudo e digite novamente.</small>
                                    </div>

                                    <div>
                                        <label for="isAllowsExtraHour" class="flex items-center justify-between font-bold mb-3">
                                            <span>Permitir 30 min</span>
                                            <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:inline" v-tooltip.top="'Permite ou não a cobrança de horas extras além do tempo reservado'" />
                                        </label>
                                        <Select id="isAllowsExtraHour" v-model="fieldStore.field.isAllowsExtraHour" :options="allowExtraHourOptions" optionLabel="label" optionValue="value" placeholder="Selecione uma opção" fluid />
                                    </div>

                                    <div>
                                        <label for="extraHourPrice" class="flex items-center justify-between font-bold mb-3">
                                            <span>Extra (30min):</span>
                                            <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:inline" v-tooltip.top="'Valor cobrado por 30 minutos adicionais ao tempo reservado (opcional)'" />
                                        </label>
                                        <InputNumber
                                            id="extraHourPrice"
                                            v-model="fieldStore.field.extraHourPrice"
                                            mode="currency"
                                            currency="BRL"
                                            locale="pt-BR"
                                            :minFractionDigits="2"
                                            placeholder="R$ 0,00"
                                            showButtons
                                            buttonLayout="horizontal"
                                            :step="1"
                                            :disabled="!fieldStore.field.isAllowsExtraHour"
                                            fluid
                                        >
                                            <template #incrementicon>
                                                <span class="pi pi-plus" />
                                            </template>
                                            <template #decrementicon>
                                                <span class="pi pi-minus" />
                                            </template>
                                        </InputNumber>
                                        <small class="text-gray-500 block mt-1 md:hidden">Se tiver dificuldade em digitar o valor, apague tudo e digite novamente.</small>
                                    </div>

                                    <div>
                                        <label for="fieldTypeId" class="flex items-center justify-between font-bold mb-3">
                                            <span>Tipo da Arena <span class="text-red-500">*</span></span>
                                            <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:inline" v-tooltip.top="'Categoria da arena (futebol, basquete, vôlei, etc.)'" />
                                        </label>
                                        <Select
                                            id="fieldTypeId"
                                            v-model="fieldStore.field.fieldTypeId"
                                            :options="fieldTypeStore.fieldTypeOptions"
                                            optionLabel="label"
                                            optionValue="value"
                                            placeholder="Selecione o tipo da arena"
                                            :invalid="submitted && (!fieldStore.field.fieldTypeId || fieldStore.field.fieldTypeId <= 0)"
                                            fluid
                                        />
                                        <small v-if="submitted && (!fieldStore.field.fieldTypeId || fieldStore.field.fieldTypeId <= 0)" class="text-red-500"> Tipo da arena é obrigatório. </small>
                                    </div>

                                    <div>
                                        <label for="fieldSurfaceId" class="flex items-center justify-between font-bold mb-3">
                                            <span>Superfície da Arena <span class="text-red-500">*</span></span>
                                            <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:inline" v-tooltip.top="'Tipo de piso ou superfície da arena (grama, sintética, madeira, etc.)'" />
                                        </label>
                                        <Select
                                            id="fieldSurfaceId"
                                            v-model="fieldStore.field.fieldSurfaceId"
                                            :options="fieldSurfaceStore.fieldSurfaceOptions"
                                            optionLabel="label"
                                            optionValue="value"
                                            placeholder="Selecione a superfície da arena"
                                            :invalid="submitted && (!fieldStore.field.fieldSurfaceId || fieldStore.field.fieldSurfaceId <= 0)"
                                            fluid
                                        />
                                        <small v-if="submitted && (!fieldStore.field.fieldSurfaceId || fieldStore.field.fieldSurfaceId <= 0)" class="text-red-500"> Superfície da arena é obrigatória. </small>
                                    </div>

                                    <div>
                                        <label for="fieldSizeId" class="flex items-center justify-between font-bold mb-3">
                                            <span>Tamanho da Arena <span class="text-red-500">*</span></span>
                                            <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:inline" v-tooltip.top="'Dimensões padrão da arena (campo oficial, meia quadra, etc.)'" />
                                        </label>
                                        <Select
                                            id="fieldSizeId"
                                            v-model="fieldStore.field.fieldSizeId"
                                            :options="fieldSizeStore.fieldSizeOptions"
                                            optionLabel="label"
                                            optionValue="value"
                                            placeholder="Selecione o tamanho da arena"
                                            :invalid="submitted && (!fieldStore.field.fieldSizeId || fieldStore.field.fieldSizeId <= 0)"
                                            fluid
                                        />
                                        <small v-if="submitted && (!fieldStore.field.fieldSizeId || fieldStore.field.fieldSizeId <= 0)" class="text-red-500"> Tamanho da arena é obrigatório. </small>
                                    </div>

                                    <div>
                                        <label for="isActive" class="flex items-center justify-between font-bold mb-3">
                                            <span>Status</span>
                                            <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:inline" v-tooltip.top="'Define se a arena está ativa para reservas ou inativa'" />
                                        </label>
                                        <Select id="isActive" v-model="fieldStore.field.isActive" :options="statusOptions" optionLabel="label" optionValue="value" placeholder="Selecione o status" fluid />
                                    </div>

                                    <div>
                                        <label for="selectedItems" class="flex items-center justify-between font-bold mb-3">
                                            <span>Comodidades</span>
                                            <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:inline" v-tooltip.top="'Comodidades que a arena oferece ao usuário, como Wifi, Apito, Cronômetro, etc.'" />
                                        </label>
                                        <MultiSelect
                                            id="selectedItems"
                                            v-model="fieldStore.field.selectedItemIds"
                                            :options="fieldItemStore.fieldItemOptions"
                                            optionLabel="label"
                                            optionValue="value"
                                            placeholder="Selecione as comodidades da arena"
                                            :maxSelectedLabels="3"
                                            fluid
                                            filter
                                        />
                                    </div>

                                    <div class="col-span-1 md:col-span-3">
                                        <label for="description" class="flex items-center justify-between font-bold mb-3">
                                            <span>Descrição</span>
                                            <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:inline" v-tooltip.top="'Descrição opcional com informações adicionais sobre a arena'" />
                                        </label>
                                        <InputText id="description" v-model="fieldStore.field.description" placeholder="Digite uma descrição para a arena" class="w-full" />
                                    </div>
                                </div>
                            </TabPanel>

                            <TabPanel value="horarios">
                                <div class="space-y-6">
                                    <div class="border rounded-lg p-4 bg-gray-50 dark:bg-gray-800">
                                        <h5 class="font-bold mb-4 dark:text-white">Copiar horários de outra arena cadastrada</h5>
                                        <div class="flex flex-col md:flex-row gap-4">
                                            <div class="w-full md:w-auto">
                                                <label class="flex items-center justify-between font-bold mb-2">
                                                    <span>Arena de Referência</span>
                                                    <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:inline" v-tooltip.top="'Selecione uma arena existente para copiar todos os horários configurados'" />
                                                </label>
                                                <Select v-model="selectedReferenceField" :options="referenceFieldOptions" optionLabel="label" optionValue="value" placeholder="Selecione uma arena" class="w-full md:w-64" />
                                            </div>
                                            <div class="w-full md:w-auto flex md:items-end">
                                                <Button label="Sincronizar Horários" icon="pi pi-sync" @click="syncSchedulesFromReference" :disabled="!selectedReferenceField" class="w-full md:w-auto" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="border rounded-lg p-4 bg-gray-50 dark:bg-gray-800">
                                        <h5 class="font-bold mb-4 dark:text-white">Adicionar novo horário</h5>
                                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                            <div>
                                                <label class="flex items-center justify-between font-bold mb-2">
                                                    <span>Dia da Semana</span>
                                                    <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:inline" v-tooltip.top="'Dia da semana em que este horário estará disponível'" />
                                                </label>
                                                <Select
                                                    v-model="newSchedule.dayOfWeek"
                                                    :options="dayOfWeekOptions"
                                                    optionLabel="label"
                                                    optionValue="value"
                                                    placeholder="Selecione o dia"
                                                    fluid
                                                    @change="
                                                        newSchedule.startTime = '';
                                                        newSchedule.endTime = '';
                                                        scheduleFilterDay = newSchedule.dayOfWeek;
                                                    "
                                                />
                                            </div>
                                            <div>
                                                <label class="flex items-center justify-between font-bold mb-2">
                                                    <span>Horário de Início</span>
                                                    <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:inline" v-tooltip.top="'Horário em que começa a disponibilidade da arena'" />
                                                </label>
                                                <Select v-model="newSchedule.startTime" :options="timeOptions" optionLabel="label" optionValue="value" placeholder="Selecione o horário" fluid />
                                            </div>
                                            <div>
                                                <label class="flex items-center justify-between font-bold mb-2">
                                                    <span>Horário de Término</span>
                                                    <i class="pi pi-info-circle text-blue-500 cursor-help hidden md:inline" v-tooltip.top="'Horário em que termina a disponibilidade da arena'" />
                                                </label>
                                                <Select v-model="newSchedule.endTime" :options="timeOptions" optionLabel="label" optionValue="value" placeholder="Selecione o horário" fluid />
                                            </div>
                                            <div class="flex items-end">
                                                <Button label="Adicionar Horário" icon="pi pi-plus" @click="addSchedule" fluid />
                                            </div>
                                        </div>
                                    </div>

                                    <div v-if="selectedSchedules.length > 0">
                                        <div v-if="hasScheduleChanges()" class="bg-orange-50 border-2 border-orange-500 rounded-lg p-4 mb-4 flex flex-col items-center gap-2">
                                            <i class="pi pi-exclamation-triangle text-orange-600 text-2xl"></i>
                                            <span class="text-base font-medium text-orange-800 text-center">Atenção: Existem alterações nos horários que ainda não foram salvas.</span>
                                        </div>
                                        <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-4 gap-2">
                                            <div class="flex items-center gap-2 w-full md:w-auto">
                                                <h5 class="font-bold">Horários Cadastrados</h5>
                                            </div>

                                            <div class="flex items-center gap-2 w-full md:w-auto">
                                                <label class="text-sm font-medium hidden md:block">Filtrar por dia:</label>
                                                <label class="text-sm font-medium md:hidden">Filtrar:</label>
                                                <Select v-model="scheduleFilterDay" :options="[{ label: 'Todos', value: null }, ...dayOfWeekOptions]" optionLabel="label" optionValue="value" placeholder="Selecione o dia" class="w-full md:w-48" />
                                            </div>
                                        </div>
                                        <div class="space-y-4">
                                            <div v-for="dayGroup in groupedSchedules" :key="dayGroup.day" class="mb-4">
                                                <h6 class="font-semibold text-lg dark:text-white">{{ dayGroup.dayName }}</h6>
                                                <div v-if="dayGroup.schedules.length === 0" class="flex flex-col items-center justify-center gap-2 p-6 text-gray-500 ml-4 dark:bg-gray-700 rounded-lg">
                                                    <i class="pi pi-exclamation-triangle text-2xl"></i>
                                                    <span class="text-center">Nenhum horário cadastrado para este dia</span>
                                                </div>
                                                <div v-else class="space-y-2 ml-4">
                                                    <div v-for="(schedule, index) in dayGroup.schedules" :key="index" class="flex items-center justify-between p-3 border rounded-lg bg-white dark:bg-gray-700">
                                                        <span class="dark:text-white">{{ schedule.startTime }} às {{ schedule.endTime }}</span>
                                                        <Button icon="pi pi-trash" severity="danger" size="small" @click="removeSchedule(schedule)" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </TabPanel>
                        </TabPanels>
                    </Tabs>

                    <template #footer>
                        <Button label="Cancelar" icon="pi pi-times" text @click="hideDialog" />
                        <Button label="Salvar" icon="pi pi-check" @click="saveField" />
                    </template>
                </Dialog>

                <Dialog v-model:visible="deleteFieldDialog" :style="{ width: '450px' }" header="Confirmar" :modal="true">
                    <div class="flex items-center gap-4">
                        <i class="pi pi-exclamation-triangle !text-3xl" />
                        <span v-if="fieldStore.field">
                            Tem certeza que deseja deletar <b>{{ fieldStore.field.name }}</b
                            >?
                        </span>
                    </div>
                    <template #footer>
                        <Button label="Não" icon="pi pi-times" text @click="deleteFieldDialog = false" />
                        <Button label="Sim" icon="pi pi-check" @click="deleteField" />
                    </template>
                </Dialog>

                <Dialog v-model:visible="deleteFieldsDialog" :style="{ width: '450px' }" header="Confirmar" :modal="true">
                    <div class="flex items-center gap-4">
                        <i class="pi pi-exclamation-triangle !text-3xl" />
                        <span>Tem certeza que deseja deletar as arenas selecionadas?</span>
                    </div>
                    <template #footer>
                        <Button label="Não" icon="pi pi-times" text @click="deleteFieldsDialog = false" />
                        <Button label="Sim" icon="pi pi-check" text @click="deleteSelectedFields" />
                    </template>
                </Dialog>

                <Dialog v-model:visible="imageUploadDialog" modal header="Alterar Imagem da Arena" :style="{ width: '30vw' }" :breakpoints="{ '960px': '75vw', '640px': '100vw' }" :maximizable="true">
                    <div v-if="selectedFieldForImage" class="text-center">
                        <div class="mb-4">
                            <img
                                v-if="selectedFieldForImage.imagePath && typeof selectedFieldForImage.imagePath === 'string'"
                                :src="selectedFieldForImage.imagePath"
                                :alt="`Imagem de ${selectedFieldForImage.name}`"
                                class="w-32 h-32 object-cover rounded mx-auto mb-4"
                            />
                            <div v-else class="w-32 h-32 bg-gray-200 rounded flex items-center justify-center mx-auto mb-4">
                                <i class="pi pi-image text-4xl text-gray-400"></i>
                            </div>
                            <h3 class="text-lg font-semibold">{{ selectedFieldForImage.name }}</h3>
                        </div>

                        <div class="flex justify-center gap-2 mb-4">
                            <FileUpload ref="fileUploadRef" mode="basic" name="demo[]" accept="image/*" :auto="false" @select="onFileSelect" :disabled="isUploading" />
                            <ProgressSpinner v-if="isUploading" strokeWidth="1" class="w-0.5 h-0.5" />
                        </div>

                        <p class="text-sm text-gray-600">Selecione uma nova imagem para a arena. Formatos aceitos: JPEG, PNG, WebP. Tamanho máximo: 4MB.</p>
                    </div>

                    <template #footer>
                        <div class="flex flex-col sm:flex-row gap-2 w-full">
                            <Button label="Cancelar" icon="pi pi-times" class="p-button-text w-full sm:w-auto" @click="imageUploadDialog = false" />
                        </div>
                    </template>
                </Dialog>
            </div>

            <Dialog v-model:visible="zoomDialog" modal header="Imagem Ampliada" :style="{ width: '50vw' }" :breakpoints="{ '960px': '75vw', '640px': '100vw' }" :maximizable="true">
                <img v-if="zoomedImage" :src="zoomedImage" alt="Imagem ampliada" class="w-full h-auto" />
            </Dialog>
        </div>
    </div>
</template>
