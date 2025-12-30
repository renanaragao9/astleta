<script setup lang="ts">
import { ref, reactive, onMounted, watch } from 'vue';
import { useHead } from '@vueuse/head';
import Button from 'primevue/button';
import Card from 'primevue/card';
import InputText from 'primevue/inputtext';
import Dialog from 'primevue/dialog';
import Badge from 'primevue/badge';
import Tag from 'primevue/tag';
import { useToast } from 'primevue/usetoast';
import { useRouter } from 'vue-router';
import { useBaaStore } from '@/stores/public/baaStore';
import { useAuthStore } from '@/stores/auth/loginStore';
import type { AthleteProfile, AthleteProfileFilters } from '@/types/public/baa';
import PublicFooter from '@/components/public/PublicFooter.vue';
import PublicTopbar from '@/components/public/PublicTopbar.vue';

useHead({
    title: 'Boletim do Atleta Amador',
    meta: [
        {
            name: 'description',
            content: 'Consulte o Boletim do Atleta Amador e conheça os perfis completos dos atletas cadastrados na plataforma SeuRacha. Busque por nome, esporte, posição e lado dominante.'
        },
        {
            name: 'keywords',
            content: 'atletas amadores, perfis esportivos, busca atletas, SeuRacha, boletim atleta amador, lado dominante'
        },
        { property: 'og:title', content: 'Boletim do Atleta Amador - Encontre Atletas | SeuRacha' },
        {
            property: 'og:description',
            content: 'Consulte perfis completos de atletas amadores na plataforma SeuRacha.'
        },
        { property: 'og:type', content: 'website' },
        { name: 'twitter:card', content: 'summary' },
        { name: 'twitter:title', content: 'Boletim do Atleta Amador - Encontre Atletas | SeuRacha' },
        {
            name: 'twitter:description',
            content: 'Consulte perfis completos de atletas amadores na plataforma SeuRacha.'
        }
    ]
});

const toast = useToast();
const router = useRouter();
const baaStore = useBaaStore();
const authStore = useAuthStore();

const searchForm = reactive({
    name: ''
});

const showProfileDialog = ref(false);

const showImageZoom = ref(false);
const zoomedImage = ref('');

const openImageZoom = (src?: string) => {
    if (src) {
        zoomedImage.value = src;
        showImageZoom.value = true;
    }
};

const searchAthletes = async () => {
    const filters: Partial<AthleteProfileFilters> = {
        page: baaStore.currentPage,
        per_page: baaStore.pagination.perPage
    };

    if (searchForm.name.trim()) {
        filters.name = searchForm.name.trim();
    }

    try {
        await baaStore.getAthletes(filters);
    } catch {
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: baaStore.error || 'Não foi possível buscar os atletas.',
            life: 5000
        });
    }
};

const performSearch = () => {
    baaStore.currentPage = 1;
    searchAthletes();
};

const viewAthleteProfile = (athlete: AthleteProfile) => {
    baaStore.selectedAthlete = athlete;
    showProfileDialog.value = true;
};

const clearSearch = () => {
    searchForm.name = '';
    baaStore.currentPage = 1;
    searchAthletes();
};

const getDominantSideLabel = (side?: string) => {
    switch (side) {
        case 'esquerdo':
            return 'Canhoto';
        case 'direito':
            return 'Destro';
        case 'ambos':
            return 'Ambivalente';
        default:
            return 'Não informado';
    }
};

const getRatingColor = (rating?: number) => {
    if (!rating) return 'gray';
    if (rating >= 4.5) return 'green';
    if (rating >= 4.0) return 'blue';
    if (rating >= 3.5) return 'yellow';
    return 'red';
};

const getProfileImage = (image?: string) => image || '/image/profile-image.png';

const defaultStatIcon = 'pi pi-chart-bar';

watch(
    () => baaStore.error,
    (newError) => {
        if (newError) {
            setTimeout(() => {
                baaStore.clearError();
            }, 5000);
        }
    }
);

watch(
    () => baaStore.currentPage,
    () => {
        searchAthletes();
    }
);

onMounted(() => {
    if (!authStore.isAuthenticated) {
        toast.add({
            severity: 'warn',
            summary: 'Acesso Restrito',
            detail: 'Você precisa estar logado para acessar o Boletim do Atleta Amador.',
            life: 5000
        });
        router.push({ name: 'login' });
        return;
    }
    searchAthletes();
});
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <PublicTopbar />

        <section class="bg-primary text-white py-20">
            <div class="container mx-auto px-4">
                <div class="max-w-4xl mx-auto text-center">
                    <div class="inline-flex items-center bg-white text-gray-900 text-sm font-medium px-4 py-2 rounded-full mb-6">
                        <i class="pi pi-search mr-2"></i>
                        Boletim do Atleta Amador - BAA
                    </div>
                    <h1 class="text-5xl lg:text-6xl font-bold leading-tight mb-6">
                        Encontre Atletas
                        <span class="text-white">da Sua Região</span>
                    </h1>
                    <p class="text-xl text-white leading-relaxed mb-8 max-w-3xl mx-auto">Consulte o Boletim do Atleta Amador e conheça os perfis completos dos atletas cadastrados na plataforma SeuRacha.</p>
                </div>
            </div>
        </section>

        <section class="py-12 bg-white">
            <div class="container mx-auto px-4">
                <div class="max-w-8xl mx-auto">
                    <Card class="!shadow-lg !border-0">
                        <template #content>
                            <div class="space-y-6">
                                <div class="flex flex-col sm:flex-row gap-6 items-end">
                                    <div class="flex-1 w-full">
                                        <label for="search-name" class="block text-sm font-medium text-gray-700 mb-2">Buscar por nome ou Id público</label>
                                        <InputText
                                            id="search-name"
                                            v-model="searchForm.name"
                                            placeholder="Digite o nome ou ID público do atleta..."
                                            class="w-full p-3 border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200"
                                            @keyup.enter="performSearch"
                                        />
                                    </div>
                                    <div class="flex gap-3 w-full sm:w-auto">
                                        <Button
                                            @click="performSearch"
                                            :loading="baaStore.loading"
                                            class="w-full sm:w-auto px-6 py-3 bg-blue-600 border-blue-600 text-white hover:bg-blue-700 hover:border-blue-700 rounded-lg font-medium transition-all duration-200 shadow-md hover:shadow-lg"
                                        >
                                            <i class="pi pi-search mr-2"></i>
                                            Buscar
                                        </Button>
                                        <Button @click="clearSearch" severity="secondary" outlined class="w-full sm:w-auto px-6 py-3 border-2 border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg font-medium transition-all duration-200">
                                            <i class="pi pi-times mr-2"></i>
                                            Limpar
                                        </Button>
                                    </div>
                                </div>

                                <div v-if="baaStore.loading" class="text-center py-8">
                                    <i class="pi pi-spin pi-spinner text-7xl text-gray-400 mb-4 leading-none"></i>
                                    <p class="text-gray-600">Carregando atletas...</p>
                                </div>

                                <div v-else-if="!baaStore.hasAthletes" class="text-center py-12">
                                    <i class="pi pi-users text-7xl text-gray-300 mb-4"></i>
                                    <h3 class="text-xl font-semibold text-gray-700 mb-2">Nenhum atleta encontrado</h3>
                                </div>

                                <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                                    <Card
                                        v-for="athlete in baaStore.athletes"
                                        :key="athlete.id"
                                        class="h-full flex flex-col border border-gray-200 hover:shadow-xl transition-all duration-300 cursor-pointer hover:scale-105"
                                        @click="viewAthleteProfile(athlete)"
                                    >
                                        <template #content>
                                            <div class="p-6 space-y-6 flex-grow">
                                                <div class="flex items-center space-x-6">
                                                    <img :src="getProfileImage(athlete.user.image)" :alt="athlete.user.name" class="w-16 h-16 rounded-full object-cover flex-shrink-0" />
                                                    <div class="flex-grow min-w-0">
                                                        <h3 class="font-bold text-xl text-gray-900 truncate">
                                                            {{ athlete.user.name }}
                                                        </h3>
                                                    </div>
                                                </div>

                                                <div class="space-y-4">
                                                    <div v-if="athlete.sport" class="flex items-center justify-between">
                                                        <span class="text-base text-gray-600 font-medium">Esporte:</span>
                                                        <Tag :value="athlete.sport.name" severity="primary" class="text-sm" />
                                                    </div>

                                                    <div v-if="athlete.position" class="flex items-center justify-between">
                                                        <span class="text-base text-gray-600 font-medium">Posição:</span>
                                                        <span class="text-base font-semibold">{{ athlete.position.name }}</span>
                                                    </div>

                                                    <div v-if="athlete.subposition" class="flex items-center justify-between">
                                                        <span class="text-base text-gray-600 font-medium">Subposição:</span>
                                                        <span class="text-base text-gray-500">{{ athlete.subposition.name }}</span>
                                                    </div>

                                                    <div class="flex items-center justify-between">
                                                        <span class="text-base text-gray-600 font-medium">Time:</span>
                                                        <div class="text-base font-semibold text-gray-700">
                                                            <div v-if="athlete.team" class="flex items-center space-x-2">
                                                                <img v-if="athlete.team.shieldPath" :src="athlete.team.shieldPath" alt="Escudo" class="w-6 h-6 object-contain" />
                                                                <span class="text-base font-semibold">{{ athlete.team.name }}</span>
                                                            </div>
                                                            <span v-else class="text-gray-500">-</span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="flex justify-between items-center pt-4 border-t border-gray-200">
                                                    <div class="text-sm text-gray-500 font-medium">
                                                        {{ athlete.dominantSide ? getDominantSideLabel(athlete.dominantSide) : '-' }}
                                                    </div>
                                                    <Button icon="pi pi-eye" text severity="primary" size="large" @click.stop="viewAthleteProfile(athlete)" class="p-3" />
                                                </div>
                                            </div>
                                        </template>
                                    </Card>
                                </div>
                            </div>
                            <div class="flex justify-center items-center space-x-2 mt-12">
                                <Button label="Anterior" icon="pi pi-chevron-left" @click="baaStore.currentPage = Math.max(1, baaStore.currentPage - 1)" :disabled="baaStore.currentPage === 1" outlined />
                                <Button
                                    v-for="page in Array.from({ length: baaStore.pagination.lastPage }, (_, i) => i + 1)"
                                    :key="page"
                                    :label="page.toString()"
                                    @click="baaStore.currentPage = page"
                                    :outlined="baaStore.currentPage !== page"
                                    :class="baaStore.currentPage === page ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-200'"
                                    class="w-10 h-10"
                                />
                                <Button
                                    label="Próxima"
                                    icon="pi pi-chevron-right"
                                    @click="baaStore.currentPage = Math.min(baaStore.pagination.lastPage, baaStore.currentPage + 1)"
                                    :disabled="baaStore.currentPage === baaStore.pagination.lastPage"
                                    outlined
                                />
                            </div>
                        </template>
                    </Card>
                </div>
            </div>
        </section>

        <Dialog v-model:visible="showProfileDialog" modal header="Perfil do Atleta" :style="{ width: '90vw', maxWidth: '1000px' }" :breakpoints="{ '960px': '95vw' }">
            <div v-if="baaStore.selectedAthlete" class="space-y-6">
                <div class="flex flex-col md:flex-row items-center md:items-start space-y-4 md:space-y-0 md:space-x-6 p-6 bg-gradient-to-r from-blue-50 to-purple-50 rounded-lg">
                    <div class="relative">
                        <img
                            :src="getProfileImage(baaStore.selectedAthlete.user.image)"
                            :alt="baaStore.selectedAthlete.user.name"
                            class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-lg cursor-pointer"
                            @click="openImageZoom(getProfileImage(baaStore.selectedAthlete.user.image))"
                        />
                        <div v-if="baaStore.selectedAthlete.rating" class="absolute -bottom-2 -right-2">
                            <Badge :value="baaStore.selectedAthlete.rating.toFixed(1)" :severity="getRatingColor(baaStore.selectedAthlete.rating)" class="text-sm" />
                        </div>
                    </div>

                    <div class="flex-1 text-center md:text-left">
                        <h2 class="text-3xl font-bold text-gray-900 mb-2">{{ baaStore.selectedAthlete.user.name }}</h2>

                        <div class="flex flex-wrap justify-center md:justify-start gap-2">
                            <Tag v-if="baaStore.selectedAthlete.sport" :value="baaStore.selectedAthlete.sport.name" severity="info" />
                            <Tag v-if="baaStore.selectedAthlete.position" :value="baaStore.selectedAthlete.position.name" severity="success" />
                            <Tag v-if="baaStore.selectedAthlete.dominantSide" :value="getDominantSideLabel(baaStore.selectedAthlete.dominantSide)" severity="warn" />
                        </div>

                        <div v-if="baaStore.selectedAthlete.team" class="flex items-center justify-center md:justify-start gap-2 mt-2">
                            <img
                                v-if="baaStore.selectedAthlete.team.shieldPath"
                                :src="baaStore.selectedAthlete.team.shieldPath"
                                alt="Escudo"
                                class="w-8 h-8 object-contain cursor-pointer"
                                @click="openImageZoom(baaStore.selectedAthlete.team.shieldPath)"
                            />
                            <span class="text-lg font-semibold">{{ baaStore.selectedAthlete.team.name }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col lg:flex-row gap-6">
                    <Card class="flex-1">
                        <template #title>
                            <div class="flex items-center gap-2">
                                <i class="pi pi-user text-blue-600"></i>
                                Informações Pessoais
                            </div>
                        </template>
                        <template #content>
                            <div class="space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div v-if="baaStore.selectedAthlete.height" class="text-center p-3 bg-gray-50 rounded-lg">
                                        <div class="text-2xl font-bold text-primary">{{ baaStore.selectedAthlete.height }}m</div>
                                        <div class="text-sm text-gray-600">Altura</div>
                                    </div>
                                    <div v-if="baaStore.selectedAthlete.weight" class="text-center p-3 bg-gray-50 rounded-lg">
                                        <div class="text-2xl font-bold text-primary">{{ baaStore.selectedAthlete.weight }}kg</div>
                                        <div class="text-sm text-gray-600">Peso</div>
                                    </div>
                                </div>

                                <div class="space-y-3">
                                    <div v-if="baaStore.selectedAthlete.sport" class="flex justify-between">
                                        <span class="text-gray-600">Esporte:</span>
                                        <span class="font-medium">{{ baaStore.selectedAthlete.sport.name }}</span>
                                    </div>
                                    <div v-if="baaStore.selectedAthlete.position" class="flex justify-between">
                                        <span class="text-gray-600">Posição:</span>
                                        <span class="font-medium">{{ baaStore.selectedAthlete.position.name }}</span>
                                    </div>
                                    <div v-if="baaStore.selectedAthlete.dominantSide" class="flex justify-between">
                                        <span class="text-gray-600">Lado Dominante:</span>
                                        <span class="font-medium">{{ getDominantSideLabel(baaStore.selectedAthlete.dominantSide) }}</span>
                                    </div>
                                    <div v-if="baaStore.selectedAthlete.subposition" class="flex justify-between">
                                        <span class="text-gray-600">Subposição:</span>
                                        <span class="font-medium">{{ baaStore.selectedAthlete.subposition.name }}</span>
                                    </div>
                                    <div v-if="baaStore.selectedAthlete.feature" class="flex justify-between">
                                        <span class="text-gray-600">Característica:</span>
                                        <span class="font-medium">{{ baaStore.selectedAthlete.feature.name }}</span>
                                    </div>
                                    <div v-if="baaStore.selectedAthlete.subfeature" class="flex justify-between">
                                        <span class="text-gray-600">Sub-característica:</span>
                                        <span class="font-medium">{{ baaStore.selectedAthlete.subfeature.name }}</span>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </Card>

                    <Card class="flex-1">
                        <template #title>
                            <div class="flex items-center gap-2">
                                <i class="pi pi-chart-bar text-primary"></i>
                                Estatísticas
                            </div>
                        </template>
                        <template #content>
                            <div v-if="baaStore.selectedAthlete.statistics && Object.keys(baaStore.selectedAthlete.statistics).length > 0" class="space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div v-for="(stat, name) in baaStore.selectedAthlete.statistics" :key="name" class="text-center p-3 bg-gray-50 rounded-lg">
                                        <i :class="[stat.icon || defaultStatIcon, 'text-2xl mb-2', stat.color || 'text-primary']"></i>
                                        <div :class="['text-2xl font-bold', stat.color || 'text-primary']">{{ stat.value }}</div>
                                        <div class="text-sm text-gray-600">{{ name }}</div>
                                    </div>
                                </div>

                                <div v-if="baaStore.selectedAthlete.rating" class="text-center p-4 bg-gradient-to-r from-yellow-50 to-orange-50 rounded-lg border border-yellow-200">
                                    <div class="text-3xl font-bold text-yellow-600 mb-1">{{ baaStore.selectedAthlete.rating.toFixed(1) }}</div>
                                    <div class="text-sm text-gray-600">Avaliação Média</div>
                                    <div class="flex justify-center mt-2">
                                        <div v-for="star in 5" :key="star" class="text-yellow-400">
                                            <i :class="star <= baaStore.selectedAthlete.rating ? 'pi pi-star-fill' : 'pi pi-star'"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="text-center text-gray-500 py-8">
                                <i class="pi pi-chart-bar text-4xl mb-2 block"></i>
                                Nenhuma estatística disponível
                            </div>
                        </template>
                    </Card>
                </div>

                <Card v-if="baaStore.selectedAthlete.bio" class="mt-6">
                    <template #title>
                        <div class="flex items-center gap-2">
                            <i class="pi pi-file-edit text-purple-600"></i>
                            Sobre o Atleta
                        </div>
                    </template>
                    <template #content>
                        <p class="text-gray-700 leading-relaxed">{{ baaStore.selectedAthlete.bio }}</p>
                    </template>
                </Card>
            </div>
        </Dialog>

        <Dialog v-model:visible="showImageZoom" modal header="Imagem Ampliada" :style="{ width: '80vw', maxWidth: '600px' }">
            <img :src="zoomedImage" alt="Imagem ampliada" class="w-full h-auto" />
        </Dialog>

        <PublicFooter />
    </div>
</template>

<style scoped>
.container {
    max-width: 1200px;
}

:deep(.p-datatable .p-datatable-tbody > tr > td) {
    padding: 0.75rem;
}

:deep(.p-datatable .p-datatable-thead > tr > th) {
    padding: 0.75rem;
    background-color: #f8fafc;
    font-weight: 600;
    color: #374151;
}

:deep(.p-paginator) {
    justify-content: center;
    padding: 1rem 0;
}

:deep(.p-dialog .p-dialog-header) {
    padding: 1.5rem;
    border-bottom: 1px solid #e5e7eb;
}

:deep(.p-dialog .p-dialog-content) {
    padding: 1.5rem;
}

:deep(.p-badge) {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}
</style>
