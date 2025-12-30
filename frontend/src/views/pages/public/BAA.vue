<script setup lang="ts">
import { ref, reactive, onMounted, watch } from 'vue';
import { useHead } from '@vueuse/head';
import Button from 'primevue/button';
import Card from 'primevue/card';
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
        window.scrollTo(0, 0);
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

        <div class="relative dark:via-primary/10 dark:to-gray-900 py-12 border-b border-primary/10 dark:border-primary/20">
            <div class="absolute inset-0 bg-white/50 dark:bg-gray-900/50 backdrop-blur-sm"></div>
            <div class="relative mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-8">
                    <h1 class="text-4xl font-bold text-gray-800 dark:text-gray-100 mb-4">Encontre Atletas da Sua Região</h1>
                    <p class="text-xl text-gray-600 dark:text-gray-300">Consulte o Boletim do Atleta Amador e conheça os perfis completos dos atletas cadastrados na plataforma SeuRacha.</p>
                </div>

                <div class="relative max-w-2xl mx-auto">
                    <div class="bg-white dark:bg-gray-800 shadow-lg border border-primary/20 dark:border-primary/30 overflow-hidden hover:shadow-xl transition-shadow duration-300 rounded-3xl flex items-center">
                        <div class="relative flex-1 w-full">
                            <i class="pi pi-search absolute left-6 top-1/2 transform -translate-y-1/2 text-primary text-lg z-10"></i>
                            <input
                                v-model="searchForm.name"
                                @keyup.enter="performSearch"
                                type="text"
                                placeholder="Buscar por nome ou ID público do atleta..."
                                class="w-full pl-14 pr-4 py-4 text-gray-700 dark:text-gray-200 placeholder-gray-400 dark:placeholder-gray-500 border-0 focus:outline-none focus:ring-0 bg-transparent text-lg"
                            />
                        </div>
                        <button
                            @click="performSearch"
                            :disabled="baaStore.loading"
                            class="flex bg-primary hover:bg-primary-600 disabled:bg-gray-400 disabled:cursor-not-allowed text-white px-8 py-4 font-semibold transition-colors duration-200 items-center justify-center gap-2"
                        >
                            <i :class="baaStore.loading ? 'pi pi-spin pi-spinner text-lg' : 'pi pi-search text-lg'"></i>
                            <span class="hidden sm:inline">{{ baaStore.loading ? 'Buscando...' : 'Buscar' }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <main class="mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div v-if="baaStore.loading" class="text-center py-16">
                <i class="pi pi-spin pi-spinner text-4xl text-primary mb-4"></i>
                <p class="text-xl text-gray-600 dark:text-gray-300">Buscando atletas...</p>
            </div>

            <div v-else-if="!baaStore.hasAthletes" class="text-center py-16">
                <i class="pi pi-users text-6xl text-gray-300 dark:text-gray-600 mb-6"></i>
                <h3 class="text-2xl font-bold text-gray-700 dark:text-gray-200 mb-4">Nenhum atleta encontrado</h3>
                <p class="text-gray-600 dark:text-gray-300">Tente novamente com outros termos de busca.</p>
            </div>

            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-8">
                <div
                    v-for="athlete in baaStore.athletes"
                    :key="athlete.id"
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300 cursor-pointer hover:scale-105"
                    @click="viewAthleteProfile(athlete)"
                >
                    <div class="relative h-56 overflow-hidden">
                        <img :src="getProfileImage(athlete.user.image)" :alt="athlete.user.name" class="w-full h-full object-contain transition-transform duration-300 hover:scale-110" />
                    </div>

                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-3 flex items-center">
                            <i class="pi pi-id-card mr-2 text-primary"></i>
                            {{ athlete.user.name }}
                        </h3>

                        <div class="mb-4 space-y-3">
                            <div v-if="athlete.sport" class="flex items-center justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-300 font-medium">Esporte:</span>
                                <Tag :value="athlete.sport.name" severity="primary" class="text-sm" />
                            </div>
                            <div v-if="athlete.position" class="flex items-center justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-300 font-medium">Posição:</span>
                                <Tag :value="athlete.position.name" severity="success" class="text-sm" />
                            </div>
                            <div v-if="athlete.team" class="flex items-center text-gray-600 dark:text-gray-300 text-sm">
                                <i class="pi pi-users mr-2 text-primary"></i>
                                <span>{{ athlete.team.name }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-300 font-medium">Lado Dominante:</span>
                                <span class="text-sm text-gray-700 dark:text-gray-200">{{ getDominantSideLabel(athlete.dominantSide) }}</span>
                            </div>
                            <div v-if="athlete.height || athlete.weight" class="flex items-center justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-300 font-medium">Altura/Peso:</span>
                                <span class="text-sm text-gray-700 dark:text-gray-200">
                                    <span v-if="athlete.height">{{ athlete.height }}m</span>
                                    <span v-if="athlete.height && athlete.weight"> / </span>
                                    <span v-if="athlete.weight">{{ athlete.weight }}kg</span>
                                </span>
                            </div>
                            <div v-if="athlete.rating" class="flex items-center text-gray-600 dark:text-gray-300 text-sm">
                                <i class="pi pi-star mr-2 text-primary"></i>
                                <span>{{ athlete.rating.toFixed(1) }}</span>
                            </div>
                        </div>

                        <Button label="Ver Perfil" icon="pi pi-eye" @click.stop="viewAthleteProfile(athlete)" class="w-full bg-primary text-white hover:bg-primary-600" />
                    </div>
                </div>
            </div>

            <div v-if="baaStore.pagination.lastPage > 1" class="flex justify-center items-center space-x-2 mt-12">
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
                <Button label="Próxima" icon="pi pi-chevron-right" @click="baaStore.currentPage = Math.min(baaStore.pagination.lastPage, baaStore.currentPage + 1)" :disabled="baaStore.currentPage === baaStore.pagination.lastPage" outlined />
            </div>
        </main>

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
