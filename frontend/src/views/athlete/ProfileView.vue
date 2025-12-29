<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { useAthleteProfileStore } from '@/stores/athlete/profileStore';
import { useFeatureStore } from '@/stores/athlete/select/featureStore';
import { usePositionStore } from '@/stores/athlete/select/positionStore';
import { useSkillStore } from '@/stores/athlete/select/skillStore';
import { useSportStore } from '@/stores/athlete/select/sportStore';
import { genderOptions } from '@/services/genderOptionsService';
import type { AthleteProfile, AthleteProfilePayload, UserPayload } from '@/types/athlete/profile';
import type { FileUploadSelectEvent } from 'primevue/fileupload';
import { useToast } from 'primevue/usetoast';
import { formatPhone } from '@/utils/phoneFormatter';
import PlayerCard from '@/components/PlayerCard.vue';

const toast = useToast();
const athleteProfileStore = useAthleteProfileStore();
const featureStore = useFeatureStore();
const positionStore = usePositionStore();
const skillStore = useSkillStore();
const sportStore = useSportStore();

const isLoading = ref(true);
const fileUploadRef = ref();
const isUploading = ref(false);
const showEditModal = ref(false);
const submittedUser = ref(false);
const isCreating = ref(false);
const submitted = ref(false);
const showUserEditModal = ref(false);
const athlete = ref<AthleteProfile | null>(null);
const editForm = ref<AthleteProfilePayload>({});
const userEditForm = ref<UserPayload>({
    name: '',
    date: '',
    phone: '',
    gender: '',
    is_public: false
});

const dominantSideOptions = ref([
    { label: 'Esquerdo', value: 'esquerdo' },
    { label: 'Direito', value: 'direito' },
    { label: 'Ambos', value: 'ambos' }
]);

const age = computed(() => {
    const dateVal: unknown = athlete.value?.date;
    if (!dateVal) return '-';

    let birthDate: Date | null = null;

    if (typeof dateVal === 'string') {
        const dateParts = dateVal.split('/');
        if (dateParts.length !== 3) return '-';
        birthDate = new Date(`${dateParts[2]}-${dateParts[1]}-${dateParts[0]}`);
    } else if (dateVal instanceof Date) {
        birthDate = dateVal;
    } else {
        const parsed = new Date(String(dateVal));
        if (!isNaN(parsed.getTime())) birthDate = parsed;
    }

    if (!birthDate || isNaN(birthDate.getTime())) return '-';
    const today = new Date();
    let calculatedAge = today.getFullYear() - birthDate.getFullYear();
    const monthDiff = today.getMonth() - birthDate.getMonth();
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
        calculatedAge--;
    }
    return calculatedAge;
});

const statisticsArray = computed(() => {
    if (!athlete.value?.statistics) return [];
    return Object.entries(athlete.value.statistics).map(([name, data]) => ({
        name,
        value: data.value,
        icon: data.icon || 'pi pi-chart-bar',
        color: data.color || 'primary'
    }));
});

const nationFlag = computed(() => {
    if (athlete.value?.lang) {
        return `/image/country/${athlete.value.lang}.png`;
    }
    return '/image/country/pt.png';
});

const clubBadge = computed(() => {
    return athlete.value?.team?.shieldPath || '/image/sem-time.png';
});

const positionAbbrev = computed(() => {
    return athlete.value?.athleteProfile?.position?.name?.substring(0, 3) || 'ATA';
});

const athleteImage = computed(() => {
    return athlete.value?.imagePath || '/image/sem-imagens.png';
});

const athleteRating = computed(() => {
    const ratings = athlete.value?.ratings;
    if (!ratings) return 0;
    const values = [ratings.overallAverage, ratings.technicalAverage, ratings.tacticalAverage, ratings.physicalAverage, ratings.mentalAverage, ratings.teamworkAverage];
    const sum = values.reduce((acc, val) => acc + val, 0);
    return Math.round(sum / values.length);
});

const athleteStats = computed(() => {
    const ratings = athlete.value?.ratings;
    return {
        pac: ratings?.physicalAverage || 0,
        sho: ratings?.technicalAverage || 0,
        pas: ratings?.tacticalAverage || 0,
        dri: ratings?.mentalAverage || 0,
        def: ratings?.overallAverage || 0,
        tea: ratings?.teamworkAverage || 0
    };
});

const loadAthleteProfile = async () => {
    try {
        isLoading.value = true;
        await athleteProfileStore.fetchProfile();
        athlete.value = athleteProfileStore.profile;
    } catch {
        if (athleteProfileStore.error) {
            toast.add({
                severity: 'error',
                summary: 'Erro',
                detail: athleteProfileStore.error,
                life: 3000
            });
        }
    } finally {
        isLoading.value = false;
    }
};

const onSportChange = async (sportId: number | undefined) => {
    editForm.value.position_id = undefined;
    editForm.value.subposition_id = undefined;
    editForm.value.feature_id = undefined;
    editForm.value.subfeature_id = undefined;

    if (!sportId) return;

    try {
        await positionStore.fetchPositions(sportId, true);
        toast.add({
            severity: 'info',
            summary: 'Filtros atualizados',
            detail: 'Posições filtradas pelo esporte selecionado',
            life: 2000
        });
    } catch {
        if (positionStore.error) {
            toast.add({ severity: 'error', summary: 'Erro', detail: positionStore.error, life: 3000 });
        }
    }
};

const onPositionChange = async (positionId: number | undefined, clearValues = true) => {
    if (clearValues) {
        editForm.value.feature_id = undefined;
        editForm.value.subfeature_id = undefined;
    }

    if (!positionId) return;

    try {
        await featureStore.fetchFeatures(positionId, true);
        toast.add({
            severity: 'info',
            summary: 'Características atualizadas',
            detail: 'Características filtradas pela posição selecionada',
            life: 2000
        });
    } catch {
        if (featureStore.error) {
            toast.add({
                severity: 'error',
                summary: 'Erro',
                detail: featureStore.error,
                life: 3000
            });
        }
    }
};

const openEditModal = async () => {
    isCreating.value = false;

    editForm.value = {
        dominant_side: athlete.value?.athleteProfile?.dominantSide?.toLowerCase(),
        height: athlete.value?.athleteProfile?.height ? parseFloat(athlete.value.athleteProfile.height) : undefined,
        weight: athlete.value?.athleteProfile?.weight ? parseFloat(athlete.value.athleteProfile.weight) : undefined,
        bio: athlete.value?.athleteProfile?.bio,
        sport_id: athlete.value?.athleteProfile?.sport?.id,
        position_id: athlete.value?.athleteProfile?.position?.id,
        subposition_id: athlete.value?.athleteProfile?.subposition?.id,
        feature_id: athlete.value?.athleteProfile?.feature?.id,
        subfeature_id: athlete.value?.athleteProfile?.subfeature?.id,
        skill_ids: athlete.value?.skills?.map((skill) => skill.id)
    };

    if (editForm.value.sport_id) await positionStore.fetchPositions(editForm.value.sport_id, true);
    if (editForm.value.position_id) await onPositionChange(editForm.value.position_id, false);

    showEditModal.value = true;
};

const saveProfile = async () => {
    if (isCreating.value) {
        submitted.value = true;
        const form = editForm.value;
        if (!form.dominant_side?.trim() || !form.height || !form.weight || !form.sport_id || !form.position_id || !form.feature_id) {
            toast.add({
                severity: 'warn',
                summary: 'Campos obrigatórios',
                detail: 'Por favor, preencha todos os campos obrigatórios.',
                life: 3000
            });
            return;
        }
    }

    try {
        if (isCreating.value) {
            await athleteProfileStore.createProfile(editForm.value);
            toast.add({ severity: 'success', summary: 'Sucesso', detail: 'Perfil criado com sucesso', life: 3000 });
        } else {
            await athleteProfileStore.updateProfile(editForm.value);
            toast.add({ severity: 'success', summary: 'Sucesso', detail: 'Perfil atualizado com sucesso', life: 3000 });
        }

        showEditModal.value = false;
        await loadAthleteProfile();
    } catch {
        if (athleteProfileStore.error) {
            toast.add({
                severity: 'error',
                summary: 'Erro',
                detail: athleteProfileStore.error,
                life: 3000
            });
        }
    }
};

const saveUser = async () => {
    submittedUser.value = true;

    if (!userEditForm.value.name?.trim() || !userEditForm.value.date || !userEditForm.value.phone?.trim() || !userEditForm.value.gender) {
        toast.add({
            severity: 'warn',
            summary: 'Campos obrigatórios',
            detail: 'Por favor, preencha todos os campos obrigatórios.',
            life: 3000
        });
        return;
    }

    const payload = {
        ...userEditForm.value,
        date: formatDateToISO(userEditForm.value.date)
    };

    try {
        await athleteProfileStore.updateUser(payload);
        toast.add({ severity: 'success', summary: 'Sucesso', detail: 'Informações pessoais atualizadas com sucesso', life: 3000 });
        showUserEditModal.value = false;
        await loadAthleteProfile();
    } catch {
        if (athleteProfileStore.error) {
            toast.add({
                severity: 'error',
                summary: 'Erro',
                detail: athleteProfileStore.error,
                life: 3000
            });
        }
    }
};

const onFileSelect = async (event: FileUploadSelectEvent) => {
    const file = Array.isArray(event.files) ? event.files[0] : event.files;
    if (file) {
        isUploading.value = true;
        try {
            await athleteProfileStore.updateImage(file);
            toast.add({ severity: 'success', summary: 'Sucesso', detail: 'Imagem atualizada com sucesso', life: 3000 });
            await loadAthleteProfile();
        } catch {
            if (athleteProfileStore.error) {
                toast.add({
                    severity: 'error',
                    summary: 'Erro',
                    detail: athleteProfileStore.error,
                    life: 3000
                });
            }
        } finally {
            isUploading.value = false;
        }
    }
};

const copyToClipboard = async (text: string) => {
    await navigator.clipboard.writeText(text);
    toast.add({ severity: 'success', summary: 'Copiado', detail: 'ID público copiado para a área de transferência', life: 2000 });
};

const openCreateModal = () => {
    isCreating.value = true;
    editForm.value = {
        height: 1.4,
        weight: 20.5
    };
    submitted.value = false;
    showEditModal.value = true;
};

const openUserEditModal = () => {
    userEditForm.value = {
        name: athlete.value?.name || '',
        date: athlete.value?.date || '',
        phone: athlete.value?.phone || '',
        gender: athlete.value?.gender || '',
        is_public: athlete.value?.isPublic || false
    };
    submittedUser.value = false;
    showUserEditModal.value = true;
};

const getDominantSideLabel = (dominantSide?: string): string => {
    if (!dominantSide) return '-';
    const sideMap: Record<string, string> = {
        esquerdo: 'Esquerdo',
        direito: 'Direito',
        ambos: 'Ambos'
    };
    return sideMap[dominantSide.toLowerCase()] || dominantSide;
};

const getProfileVisibilityLabel = (isPublic: boolean): string => {
    return isPublic ? 'Público' : 'Privado';
};

const formatDateToISO = (dateStr: string): string => {
    if (!dateStr) return '';
    const parts = dateStr.split('/');
    if (parts.length !== 3) return '';
    return `${parts[2]}-${parts[1].padStart(2, '0')}-${parts[0].padStart(2, '0')}`;
};

onMounted(async () => {
    try {
        await Promise.all([loadAthleteProfile(), featureStore.fetchFeatures(), positionStore.fetchPositions(), skillStore.fetchSkills(), sportStore.fetchSports()]);
    } catch {
        const errors = [athleteProfileStore.error, featureStore.error, positionStore.error, skillStore.error, sportStore.error].filter(Boolean);

        if (errors.length > 0) {
            toast.add({
                severity: 'error',
                summary: 'Erro',
                detail: errors[0],
                life: 5000
            });
        }
    }
});
</script>

<template>
    <div v-if="isLoading" class="flex items-center justify-center min-h-[400px]">
        <ProgressSpinner strokeWidth="4" class="w-12 h-12" />
    </div>

    <div v-else-if="athlete">
        <div class="space-y-6 lg:space-y-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <div class="lg:col-span-4 -mx-4 sm:mx-0">
                    <Card class="h-full min-h-[500px] hover:shadow-lg transition-all duration-300 rounded-none sm:rounded-xl">
                        <template #header>
                            <div class="p-4 sm:p-6 pb-0 flex flex-col gap-2">
                                <div class="flex-shrink-0">
                                    <Button v-if="athlete.athleteProfile" label="Editar" icon="pi pi-pencil" class="p-button-sm p-button-outlined" @click="openEditModal" />
                                    <Button v-else label="Criar Perfil" icon="pi pi-plus" class="p-button-sm p-button-primary" @click="openCreateModal" />
                                </div>

                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white leading-tight">{{ athlete.name }}</h1>
                                        <p class="text-lg text-gray-600 dark:text-gray-400 mt-1">{{ athlete.athleteProfile?.position?.name || 'Atleta' }}</p>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <template #content>
                            <div class="">
                                <div class="flex justify-center mb-6">
                                    <PlayerCard :image-path="athleteImage" :athlete-name="athlete.name" :rating="athleteRating" :position="positionAbbrev" :nation-flag="nationFlag" :club-badge="clubBadge" :stats="athleteStats" />
                                </div>

                                <div class="flex justify-center gap-2 mb-4">
                                    <FileUpload ref="fileUploadRef" mode="basic" name="demo[]" accept="image/*" :auto="false" @select="onFileSelect" :disabled="isUploading" />
                                    <ProgressSpinner v-if="isUploading" strokeWidth="1" class="w-0.5 h-0.5" />
                                </div>

                                <div class="border-t pt-4 space-y-3">
                                    <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-100 uppercase tracking-wide mb-3">Contato</h4>

                                    <div class="flex justify-between items-center gap-2 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors" @click="copyToClipboard(athlete.id.toString())">
                                        <span class="text-sm text-gray-600 dark:text-gray-400 flex items-center gap-2">
                                            <i class="pi pi-tag text-xs text-primary team-gradient-text"></i>
                                            Id público:
                                        </span>
                                        <span class="font-semibold text-gray-800 dark:text-gray-200 text-right">{{ athlete.uuid }}</span>
                                    </div>

                                    <div class="flex justify-between items-center gap-2">
                                        <span class="text-sm text-gray-600 dark:text-gray-400 flex items-center gap-2">
                                            <i class="pi pi-envelope text-primary"></i>
                                            Email:
                                        </span>
                                        <span class="font-semibold text-gray-800 dark:text-gray-200 text-right break-all">
                                            {{ athlete.email || '—' }}
                                        </span>
                                    </div>

                                    <div class="flex justify-between items-center gap-2">
                                        <span class="text-sm text-gray-600 dark:text-gray-400 flex items-center gap-2">
                                            <i class="pi pi-phone text-primary"></i>
                                            Telefone:
                                        </span>
                                        <span class="font-semibold text-gray-800 dark:text-gray-200 text-right">
                                            {{ athlete.phone ? formatPhone(athlete.phone) : '—' }}
                                        </span>
                                    </div>

                                    <div class="flex justify-between items-center gap-2">
                                        <span class="text-sm text-gray-600 dark:text-gray-400 flex items-center gap-2">
                                            <i class="pi pi-calendar text-primary"></i>
                                            Criado em:
                                        </span>
                                        <span class="font-semibold text-gray-800 dark:text-gray-200 text-right">
                                            {{ athlete.createdAt || '—' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </Card>
                </div>

                <div class="lg:col-span-8 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:gap-6 items-stretch -mx-4 sm:mx-0">
                        <Card class="h-full flex flex-col rounded-none sm:rounded-xl">
                            <template #header>
                                <div class="p-4 lg:p-6 pb-0">
                                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                                        <span class="bg-primary-100 text-primary rounded-full w-8 h-8 flex items-center justify-center">
                                            <i class="pi pi-trophy"></i>
                                        </span>
                                        Informações Esportivas
                                    </h3>
                                </div>
                            </template>

                            <template #content>
                                <div class="px-4 lg:px-6 pb-4 lg:pb-6 space-y-4 flex-grow">
                                    <div class="flex justify-between items-center gap-2">
                                        <span class="text-sm text-gray-600 dark:text-gray-400 flex items-center gap-2">
                                            <i class="fa-solid fa-medal text-xs text-primary"></i>
                                            Esporte:
                                        </span>
                                        <span class="font-semibold text-gray-800 dark:text-gray-200 text-right">
                                            {{ athlete.athleteProfile?.sport?.name || '—' }}
                                        </span>
                                    </div>

                                    <div class="flex justify-between items-center gap-2">
                                        <span class="text-sm text-gray-600 dark:text-gray-400 flex items-center gap-2">
                                            <i class="pi pi-circle-fill text-xs text-primary"></i>
                                            Posição Principal:
                                        </span>
                                        <span class="font-semibold text-gray-800 dark:text-gray-200 text-right">
                                            {{ athlete.athleteProfile?.position?.name || '—' }}
                                        </span>
                                    </div>

                                    <div class="flex justify-between items-center gap-2">
                                        <span class="text-sm text-gray-600 dark:text-gray-400 flex items-center gap-2">
                                            <i class="pi pi-circle text-xs text-primary"></i>
                                            Posição Secundária:
                                        </span>
                                        <span class="font-semibold text-gray-800 dark:text-gray-200 text-right">
                                            {{ athlete.athleteProfile?.subposition?.name || '—' }}
                                        </span>
                                    </div>

                                    <div class="flex justify-between items-center gap-2">
                                        <span class="text-sm text-gray-600 dark:text-gray-400 flex items-center gap-2">
                                            <i class="pi pi-check-circle text-primary"></i>
                                            Especialidade:
                                        </span>
                                        <span class="font-semibold text-gray-800 dark:text-gray-200 text-right">
                                            {{ athlete.athleteProfile?.feature?.name || '—' }}
                                        </span>
                                    </div>

                                    <div class="flex justify-between items-center gap-2">
                                        <span class="text-sm text-gray-600 dark:text-gray-400 flex items-center gap-2">
                                            <i class="pi pi-check text-primary"></i>
                                            Subespecialidade:
                                        </span>
                                        <span class="font-semibold text-gray-800 dark:text-gray-200 text-right">
                                            {{ athlete.athleteProfile?.subfeature?.name || '—' }}
                                        </span>
                                    </div>

                                    <div class="flex justify-between items-center gap-2">
                                        <span class="text-sm text-gray-600 dark:text-gray-400 flex items-center gap-2">
                                            <i class="pi pi-shield text-primary"></i>
                                            Clube:
                                        </span>
                                        <span class="font-semibold text-gray-800 dark:text-gray-200 text-right">
                                            {{ athlete?.team?.name || '—' }}
                                        </span>
                                    </div>

                                    <div class="flex justify-between items-center gap-2">
                                        <span class="text-sm text-gray-600 dark:text-gray-400 flex items-center gap-2">
                                            <i :class="`${athlete?.statistics?.Jogos?.icon} text-primary`"></i>
                                            Total de Jogos:
                                        </span>
                                        <span class="font-semibold text-gray-800 dark:text-gray-200 text-right">
                                            {{ athlete?.statistics?.Jogos?.value || 0 }}
                                        </span>
                                    </div>
                                </div>
                            </template>
                        </Card>

                        <Card class="h-full flex flex-col rounded-none sm:rounded-xl">
                            <template #header>
                                <div class="p-4 lg:p-6 pb-0 flex justify-between items-center">
                                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                                        <span class="bg-primary-100 text-primary rounded-full w-8 h-8 flex items-center justify-center">
                                            <i class="pi pi-user"></i>
                                        </span>
                                        Características Físicas
                                    </h3>
                                    <Button label="Editar" icon="pi pi-pencil" class="p-button-sm p-button-outlined" @click="openUserEditModal" />
                                </div>
                            </template>
                            <template #content>
                                <div class="px-4 lg:px-6 pb-4 lg:pb-6 space-y-4 flex-grow">
                                    <div class="flex justify-between items-center gap-2">
                                        <span class="text-sm text-gray-600 dark:text-gray-400 flex items-center gap-2">
                                            <i class="fa-solid fa-venus-mars text-xs text-primary"></i>
                                            Gênero:
                                        </span>
                                        <span class="font-semibold text-gray-800 dark:text-gray-200 text-right">{{ athlete.gender }}</span>
                                    </div>
                                    <div class="flex justify-between items-center gap-2">
                                        <span class="text-sm text-gray-600 dark:text-gray-400 flex items-center gap-2">
                                            <i class="fa-solid fa-calendar text-xs text-primary"></i>
                                            Data de nascimento:
                                        </span>
                                        <span class="font-semibold text-gray-800 dark:text-gray-200 text-right">{{ athlete.date }}</span>
                                    </div>
                                    <div class="flex justify-between items-center gap-2">
                                        <span class="text-sm text-gray-600 dark:text-gray-400 flex items-center gap-2">
                                            <i class="fa-solid fa-birthday-cake text-xs text-primary"></i>
                                            Idade:
                                        </span>
                                        <span class="font-semibold text-gray-800 dark:text-gray-200 text-right">{{ age }} anos</span>
                                    </div>
                                    <div class="flex justify-between items-center gap-2">
                                        <span class="text-sm text-gray-600 dark:text-gray-400 flex items-center gap-2">
                                            <i class="fa-solid fa-ruler-vertical text-xs text-primary"></i>
                                            Altura:
                                        </span>
                                        <span class="font-semibold text-gray-800 dark:text-gray-200 text-right">{{ athlete.athleteProfile?.height || 0 }} m</span>
                                    </div>
                                    <div class="flex justify-between items-center gap-2">
                                        <span class="text-sm text-gray-600 dark:text-gray-400 flex items-center gap-2">
                                            <i class="fa-solid fa-weight-hanging text-xs text-primary"></i>
                                            Peso:
                                        </span>
                                        <span class="font-semibold text-gray-800 dark:text-gray-200 text-right">{{ athlete.athleteProfile?.weight || 0 }} kg</span>
                                    </div>
                                    <div class="flex justify-between items-center gap-2">
                                        <span class="text-sm text-gray-600 dark:text-gray-400 flex items-center gap-2">
                                            <i class="fa-solid fa-shoe-prints text-xs text-primary"></i>
                                            Lado dominante:
                                        </span>
                                        <Tag :value="getDominantSideLabel(athlete.athleteProfile?.dominantSide || '')" severity="primary" />
                                    </div>
                                    <div class="flex justify-between items-center gap-2">
                                        <span class="text-sm text-gray-600 dark:text-gray-400 flex items-center gap-2">
                                            <i class="fa-solid fa-eye text-xs text-primary"></i>
                                            Visibilidade do perfil no BAA:
                                        </span>
                                        <Tag :value="getProfileVisibilityLabel(athlete.isPublic || false)" severity="primary" />
                                    </div>
                                </div>
                            </template>
                        </Card>

                        <Card class="h-full flex flex-col rounded-none sm:rounded-xl col-span-1 md:col-span-2">
                            <template #header>
                                <div class="p-4 lg:p-6 pb-0">
                                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                                        <span class="bg-primary-100 text-primary rounded-full w-8 h-8 flex items-center justify-center">
                                            <i class="pi pi-cog"></i>
                                        </span>
                                        Habilidades Principais
                                    </h3>
                                </div>
                            </template>
                            <template #content>
                                <div class="px-4 lg:px-6 pb-4 lg:pb-6 flex-grow flex flex-col">
                                    <div v-if="athlete.skills && athlete.skills.length > 0" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div
                                            v-for="skill in athlete.skills"
                                            :key="skill.id"
                                            class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-700 dark:to-gray-600 rounded-lg p-3 text-center border border-blue-100 dark:border-gray-600 hover:shadow-md transition-shadow duration-200"
                                        >
                                            <span class="text-sm font-medium text-primary-700 dark:text-primary-500">{{ skill.name }}</span>
                                        </div>
                                    </div>
                                    <div v-else class="text-center py-8">
                                        <i class="pi pi-info-circle text-3xl text-gray-500 dark:text-gray-400 mb-3"></i>
                                        <p class="text-gray-500 dark:text-gray-400">Nenhuma habilidade cadastrada ainda.</p>
                                    </div>
                                </div>
                            </template>
                        </Card>
                    </div>

                    <Card class="-mx-4 sm:mx-0 rounded-none sm:rounded-xl">
                        <template #header>
                            <div class="p-4 lg:p-4 pb-0">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                                    <span class="bg-primary-100 text-primary rounded-full w-8 h-8 flex items-center justify-center">
                                        <i class="pi pi-book"></i>
                                    </span>
                                    Sobre
                                </h3>
                            </div>
                        </template>
                        <template #content>
                            <div class="px-4 lg:px-6 pb-4 lg:pb-6">
                                <div class="flex flex-col items-center gap-2 mb-2">
                                    <i class="pi pi-book text-gray-600 dark:text-gray-400 text-xl"></i>
                                    <span class="sr-only">Biografia</span>
                                </div>
                                <p class="text-gray-600 dark:text-gray-400 leading-relaxed text-sm lg:text-base text-center">
                                    {{ athlete.athleteProfile?.bio || 'Nenhuma informação biográfica disponível.' }}
                                </p>
                            </div>
                        </template>
                    </Card>
                </div>
            </div>
        </div>

        <Card class="-mx-4 mt-6 sm:mx-0 rounded-none sm:rounded-xl">
            <template #header>
                <div class="p-4 lg:p-6 pb-0">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                        <span class="bg-primary-100 text-primary rounded-full w-8 h-8 flex items-center justify-center">
                            <i class="pi pi-chart-bar"></i>
                        </span>
                        Estatísticas
                    </h3>
                </div>
            </template>
            <template #content>
                <div class="px-4 lg:px-6 pb-4 lg:pb-6">
                    <div v-if="statisticsArray.length > 0" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 lg:gap-4">
                        <Card v-for="stat in statisticsArray" :key="stat.name" class="text-center hover:shadow-lg transition-all duration-300 cursor-pointer group border border-gray-200 dark:border-gray-700">
                            <template #content>
                                <div class="py-4 lg:py-6">
                                    <i :class="`${stat.icon} text-xl lg:text-2xl text-${stat.color} mb-2 group-hover:scale-110 transition-transform duration-200`"></i>
                                    <div :class="`text-xl lg:text-2xl font-bold text-${stat.color} mb-1`">{{ stat.value }}</div>
                                    <div class="text-xs lg:text-sm text-gray-600 dark:text-gray-400 font-medium">{{ stat.name }}</div>
                                </div>
                            </template>
                        </Card>
                    </div>
                    <div v-else class="text-center py-8">
                        <i class="pi pi-chart-bar text-4xl text-gray-500 dark:text-gray-400 mb-4"></i>
                        <p class="text-gray-500 dark:text-gray-400">Nenhuma estatística disponível ainda.</p>
                        <p class="text-sm text-gray-400 dark:text-gray-500 mt-2">Participe de rachas para gerar suas estatísticas!</p>
                    </div>
                </div>
            </template>
        </Card>
    </div>

    <div v-else class="flex items-center justify-center min-h-[400px] px-4">
        <div class="text-center max-w-md mx-auto">
            <div class="bg-gray-100 dark:bg-gray-800 rounded-full w-20 h-20 lg:w-24 lg:h-24 mx-auto mb-6 flex items-center justify-center">
                <i class="pi pi-user text-3xl lg:text-4xl text-gray-400"></i>
            </div>
            <h3 class="text-xl lg:text-2xl font-semibold text-gray-800 dark:text-gray-200 mb-3">Perfil não encontrado</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-6 text-sm lg:text-base leading-relaxed">Você ainda não possui um perfil de atleta. Crie seu perfil para começar a mostrar suas habilidades e conquistas!</p>
            <Button label="Criar Perfil" icon="pi pi-plus" class="p-button-primary p-button-lg w-full sm:w-auto" @click="openCreateModal" />
        </div>
    </div>

    <Dialog v-model:visible="showEditModal" modal :header="isCreating ? 'Criar Perfil' : 'Editar Perfil'" :style="{ width: 'min(50rem, 95vw)' }" :closable="true" class="p-dialog-maximized-sm" :maximizable="true">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 lg:gap-6">
            <div class="col-span-1">
                <label for="dominant_side" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Lado Dominante <span class="text-red-500">*</span> </label>
                <Select
                    id="dominant_side"
                    v-model="editForm.dominant_side"
                    :options="dominantSideOptions"
                    optionLabel="label"
                    optionValue="value"
                    placeholder="Selecione o lado dominante"
                    class="w-full"
                    fluid
                    :invalid="submitted && isCreating && !editForm.dominant_side"
                />
                <small v-if="submitted && isCreating && !editForm.dominant_side" class="text-red-500">Campo obrigatório</small>
            </div>

            <div class="col-span-1">
                <label for="height" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"> Altura (m) <span class="text-red-500">*</span> </label>
                <InputNumber
                    v-model="editForm.height"
                    inputId="height-buttons"
                    class="w-full"
                    showButtons
                    buttonLayout="horizontal"
                    mode="decimal"
                    locale="pt-BR"
                    placeholder="Ex: 1,80"
                    fluid
                    :step="0.1"
                    :min="0.5"
                    :max="3.0"
                    :invalid="submitted && isCreating && !editForm.height"
                >
                    <template #incrementicon>
                        <span class="pi pi-plus" />
                    </template>
                    <template #decrementicon>
                        <span class="pi pi-minus" />
                    </template>
                </InputNumber>
                <small v-if="submitted && isCreating && !editForm.height" class="text-red-500"> Campo obrigatório (min: 0.5m, max: 3.0m) </small>
            </div>

            <div class="col-span-1">
                <label for="weight" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"> Peso (kg) <span class="text-red-500">*</span> </label>
                <InputNumber
                    v-model="editForm.weight"
                    inputId="weight-buttons"
                    showButtons
                    buttonLayout="horizontal"
                    mode="decimal"
                    locale="pt-BR"
                    placeholder="Ex: 75"
                    class="w-full"
                    fluid
                    :step="0.1"
                    :min="20"
                    :max="300"
                    :invalid="submitted && isCreating && !editForm.weight"
                >
                    <template #incrementicon>
                        <span class="pi pi-plus" />
                    </template>
                    <template #decrementicon>
                        <span class="pi pi-minus" />
                    </template>
                </InputNumber>
                <small v-if="submitted && isCreating && !editForm.weight" class="text-red-500"> Campo obrigatório (min: 20kg, max: 300kg) </small>
            </div>

            <div class="col-span-1">
                <label for="sport_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"> Esporte <span class="text-red-500">*</span> </label>
                <Select
                    id="sport_id"
                    v-model="editForm.sport_id"
                    :options="sportStore.sportOptions"
                    optionLabel="label"
                    optionValue="value"
                    placeholder="Selecione o esporte"
                    class="w-full"
                    fluid
                    :invalid="submitted && isCreating && !editForm.sport_id"
                    @change="onSportChange(editForm.sport_id)"
                />
                <small v-if="submitted && isCreating && !editForm.sport_id" class="text-red-500">Campo obrigatório</small>
            </div>

            <div class="col-span-1">
                <label for="position_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"> Posição <span class="text-red-500">*</span> </label>
                <Select
                    id="position_id"
                    v-model="editForm.position_id"
                    :options="positionStore.positionOptions"
                    optionLabel="label"
                    optionValue="value"
                    :placeholder="editForm.sport_id ? 'Selecione a posição' : 'Selecione um esporte primeiro'"
                    class="w-full"
                    fluid
                    :invalid="submitted && isCreating && !editForm.position_id"
                    :disabled="!editForm.sport_id"
                    @change="onPositionChange(editForm.position_id, true)"
                />
                <small v-if="!editForm.sport_id" class="text-amber-500">Selecione um esporte primeiro</small>
                <small v-else-if="submitted && isCreating && !editForm.position_id" class="text-red-500">Campo obrigatório</small>
            </div>

            <div class="col-span-1">
                <label for="subposition_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"> Subposição </label>
                <Select
                    id="subposition_id"
                    v-model="editForm.subposition_id"
                    :options="positionStore.positionOptions"
                    optionLabel="label"
                    optionValue="value"
                    :placeholder="editForm.sport_id ? 'Selecione a subposição' : 'Selecione um esporte primeiro'"
                    class="w-full"
                    fluid
                    showClear
                    :disabled="!editForm.sport_id"
                />
                <small v-if="!editForm.sport_id" class="text-amber-500">Selecione um esporte primeiro</small>
            </div>

            <div class="col-span-1">
                <label for="feature_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"> Característica <span class="text-red-500">*</span> </label>
                <Select
                    id="feature_id"
                    v-model="editForm.feature_id"
                    :options="featureStore.featureOptions"
                    optionLabel="label"
                    optionValue="value"
                    :placeholder="editForm.sport_id ? 'Selecione a característica' : 'Selecione um esporte primeiro'"
                    class="w-full"
                    fluid
                    :invalid="submitted && isCreating && !editForm.feature_id"
                    :disabled="!editForm.sport_id"
                />
                <small v-if="!editForm.sport_id" class="text-amber-500">Selecione um esporte primeiro</small>
                <small v-else-if="submitted && isCreating && !editForm.feature_id" class="text-red-500">Campo obrigatório</small>
            </div>

            <div class="col-span-1">
                <label for="subfeature_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"> Subcaracterística </label>
                <Select
                    id="subfeature_id"
                    v-model="editForm.subfeature_id"
                    :options="featureStore.featureOptions"
                    optionLabel="label"
                    optionValue="value"
                    :placeholder="editForm.sport_id ? 'Selecione a subcaracterística' : 'Selecione um esporte primeiro'"
                    class="w-full"
                    fluid
                    showClear
                    :disabled="!editForm.sport_id"
                />
                <small v-if="!editForm.sport_id" class="text-amber-500">Selecione um esporte primeiro</small>
                <small v-else class="text-gray-500">Opcional</small>
            </div>

            <div class="col-span-1 sm:col-span-2">
                <label for="skill_ids" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Habilidades
                    <span v-if="editForm.skill_ids && editForm.skill_ids.length > 0" class="text-gray-500"> ({{ editForm.skill_ids.length }}/6) </span>
                </label>
                <MultiSelect id="skill_ids" v-model="editForm.skill_ids" :options="skillStore.skillOptions" optionLabel="label" optionValue="value" placeholder="Selecione as habilidades (máx. 6)" :maxSelectedLabels="3" class="w-full" fluid filter />
                <small class="text-gray-500">máximo de 6 habilidades</small>
            </div>

            <div class="col-span-1 sm:col-span-2">
                <label for="bio" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Biografia
                    <span v-if="editForm.bio" class="text-gray-500"> ({{ editForm.bio.length }}/1000) </span>
                </label>
                <Textarea id="bio" v-model="editForm.bio" rows="4" :maxlength="1000" placeholder="Conte um pouco sobre você..." class="w-full" fluid />
                <small class="text-gray-500">máximo de 1000 caracteres</small>
            </div>
        </div>

        <template #footer>
            <div class="flex flex-col sm:flex-row gap-2 w-full">
                <Button label="Cancelar" icon="pi pi-times" class="p-button-text w-full sm:w-auto" @click="showEditModal = false" />
                <Button :label="isCreating ? 'Criar' : 'Salvar'" :icon="isCreating ? 'pi pi-plus' : 'pi pi-check'" class="p-button-primary w-full sm:w-auto" @click="saveProfile" />
            </div>
        </template>
    </Dialog>

    <Dialog v-model:visible="showUserEditModal" modal header="Editar Informações Pessoais" :style="{ width: 'min(50rem, 95vw)' }" :closable="true" class="p-dialog-maximized-sm" :maximizable="true">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 lg:gap-6">
            <div class="col-span-1 sm:col-span-1">
                <label for="user-name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"> Nome <span class="text-red-500">*</span> </label>
                <InputText id="user-name" v-model="userEditForm.name" placeholder="Digite seu nome completo" class="w-full" fluid :invalid="submittedUser && !userEditForm.name?.trim()" />
                <small v-if="submittedUser && !userEditForm.name?.trim()" class="text-red-500">Campo obrigatório</small>
            </div>

            <div class="col-span-1">
                <label for="birthDate" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Data de nascimento <span class="text-red-500">*</span></label>
                <InputMask id="birthDate" v-model="userEditForm.date" mask="99/99/9999" placeholder="dd/mm/aaaa" class="w-full mb-4" type="tel" />
                <span v-if="submittedUser && !userEditForm.date" class="text-red-500 text-sm">Data de nascimento é obrigatória.</span>
            </div>
            <div class="col-span-1">
                <label for="user-phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"> Telefone <span class="text-red-500">*</span> </label>
                <InputMask id="user-phone" v-model="userEditForm.phone" mask="99 99999-9999" placeholder="00 00000-0000" class="w-full" fluid :invalid="submittedUser && !userEditForm.phone?.trim()" />
                <small v-if="submittedUser && !userEditForm.phone?.trim()" class="text-red-500">Campo obrigatório</small>
            </div>

            <div class="col-span-1">
                <label for="user-gender" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"> Gênero <span class="text-red-500">*</span> </label>
                <Select id="user-gender" v-model="userEditForm.gender" :options="genderOptions" optionLabel="label" optionValue="value" placeholder="Selecione o gênero" class="w-full" fluid :invalid="submittedUser && !userEditForm.gender" />
                <small v-if="submittedUser && !userEditForm.gender" class="text-red-500">Campo obrigatório</small>
            </div>

            <div class="col-span-1">
                <label for="user-visibility" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"> Visibilidade do Perfil no BAA </label>
                <div class="flex items-center gap-2">
                    <ToggleSwitch id="user-visibility" v-model="userEditForm.is_public" />
                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ userEditForm.is_public ? 'Público' : 'Privado' }}</span>
                </div>
            </div>
        </div>

        <template #footer>
            <div class="flex flex-col sm:flex-row gap-2 w-full">
                <Button label="Cancelar" icon="pi pi-times" class="p-button-text w-full sm:w-auto" @click="showUserEditModal = false" />
                <Button label="Salvar" icon="pi pi-check" class="p-button-primary w-full sm:w-auto" @click="saveUser" />
            </div>
        </template>
    </Dialog>
</template>
