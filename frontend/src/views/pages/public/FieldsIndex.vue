<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted, watch, provide } from 'vue';
import { useRouter } from 'vue-router';
import { useHead } from '@vueuse/head';
import { usePublicFieldStore } from '@/stores/public/publicFieldStore';
import type { PublicField, PublicFieldFilters } from '@/types/public/field/fieldIndex';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Select from 'primevue/select';
import InputNumber from 'primevue/inputnumber';
import PublicTopbar from '@/components/public/PublicTopbar.vue';
import PublicFooter from '@/components/public/PublicFooter.vue';

interface BeforeInstallPromptEvent extends Event {
    prompt(): Promise<void>;
    userChoice: Promise<{ outcome: 'accepted' | 'dismissed' }>;
}

const router = useRouter();
const publicFieldStore = usePublicFieldStore();

const deferredPrompt = ref<BeforeInstallPromptEvent | null>(null);
const showInstallButton = ref(false);

const currentPage = ref(1);
const itemsPerPage = 15;
const searchQuery = ref('');
const showSearchInTopbar = ref(false);
const searchSectionRef = ref<HTMLElement>();
const showFilters = ref(false);

const filters = ref({
    city: '',
    state: '',
    district: '',
    sport_type: '',
    price_min: null as number | null,
    price_max: null as number | null,
    sort: 'newest' as 'name' | 'price' | 'price_low_to_high' | 'price_high_to_low' | 'company_name' | 'city' | 'newest' | 'oldest' | 'created_at' | 'updated_at',
    direction: 'desc' as 'asc' | 'desc'
});

const sportTypeOptions = [
    { label: 'Todos', value: '' },
    { label: 'Futebol', value: 'Futebol' },
    { label: 'Society', value: 'Society' },
    { label: 'Futsal', value: 'Futsal' },
    { label: 'Vôlei', value: 'Vôlei' },
    { label: 'Basquete', value: 'Basquete' }
];

const sortOptions = [
    { label: 'Mais Recentes', value: 'newest' },
    { label: 'Mais Antigos', value: 'oldest' },
    { label: 'Nome A-Z', value: 'name' },
    { label: 'Menor Preço', value: 'price_low_to_high' },
    { label: 'Maior Preço', value: 'price_high_to_low' },
    { label: 'Nome da Empresa', value: 'company_name' },
    { label: 'Cidade', value: 'city' },
    { label: 'Data de Criação', value: 'created_at' },
    { label: 'Última Atualização', value: 'updated_at' }
];

const fields = computed(() => publicFieldStore.fields);
const loading = computed(() => publicFieldStore.loading);
const paginatedFields = computed(() => fields.value);
const totalPages = computed(() => publicFieldStore.pagination.lastPage);

const activeFiltersCount = computed(() => {
    let count = 0;
    Object.entries(filters.value).forEach(([key, value]) => {
        if (key !== 'sort' && key !== 'direction' && value !== '' && value !== null) {
            count++;
        }
    });
    return count;
});

const viewMore = (field: PublicField) => {
    router.push({ name: 'field-detail', params: { id: field.id } });
};

const fetchFields = async () => {
    const apiFilters: Partial<PublicFieldFilters> = {
        page: currentPage.value,
        perPage: itemsPerPage,
        search: searchQuery.value || undefined
    };

    if (filters.value.sort) {
        apiFilters.sort = filters.value.sort;
        apiFilters.direction = filters.value.direction;
    }

    Object.entries(filters.value).forEach(([key, value]) => {
        if (value !== '' && value !== null && key !== 'sort' && key !== 'direction') {
            (apiFilters as Record<string, string | number>)[key] = value;
        }
    });

    await publicFieldStore.fetchFields(apiFilters);
};

const clearFilters = () => {
    filters.value = {
        city: '',
        state: '',
        district: '',
        sport_type: '',
        price_min: null,
        price_max: null,
        sort: 'newest',
        direction: 'desc'
    };
    searchQuery.value = '';
    currentPage.value = 1;
    fetchFields();
};

const applyFilters = () => {
    currentPage.value = 1;
    fetchFields();
};

const toggleFilters = () => {
    showFilters.value = !showFilters.value;
};

const handleScroll = () => {
    if (searchSectionRef.value) {
        const rect = searchSectionRef.value.getBoundingClientRect();
        showSearchInTopbar.value = rect.bottom < 0;
    }
};

provide('searchQuery', searchQuery);
provide('showSearchInTopbar', showSearchInTopbar);
provide('handleSearch', fetchFields);

watch(currentPage, () => {
    fetchFields();
});

onMounted(() => {
    window.scrollTo(0, 0);
    fetchFields();
    window.addEventListener('scroll', handleScroll);

    window.addEventListener('beforeinstallprompt', (e: Event) => {
        e.preventDefault();
        deferredPrompt.value = e as BeforeInstallPromptEvent;
        showInstallButton.value = true;
    });

    window.addEventListener('appinstalled', () => {
        showInstallButton.value = false;
        deferredPrompt.value = null;
    });
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
});

const installPWA = async () => {
    if (deferredPrompt.value) {
        deferredPrompt.value.prompt();
        const { outcome } = await deferredPrompt.value.userChoice;
        if (outcome === 'accepted') {
            showInstallButton.value = false;
        }
        deferredPrompt.value = null;
    }
};

useHead({
    title: 'Campos Society e Arenas para Racha | SeuRacha',
    meta: [
        {
            name: 'description',
            content: 'Encontre campos society e arenas esportivas para racha perto de você. Reserve online quadras de futebol, futsal, vôlei e basquete com o SeuRacha. Encontre o local ideal para seu próximo jogo!'
        },
        { property: 'og:title', content: 'Campos Society e Arenas para Racha | SeuRacha' },
        {
            property: 'og:description',
            content: 'Reserve campos society e arenas esportivas perto de você com rapidez e facilidade.'
        },
        { property: 'og:type', content: 'website' },
        { property: 'og:url', content: 'https://seuracha.com/campos' },
        { property: 'og:image', content: 'https://seuracha.com/og/home.jpg' }
    ],
    link: [
        {
            rel: 'canonical',
            href: 'https://seuracha.com/campos'
        }
    ]
});
</script>

<template>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <PublicTopbar />

        <div ref="searchSectionRef" class="relative dark:via-primary/10 dark:to-gray-900 py-12 border-b border-primary/10 dark:border-primary/20">
            <div class="absolute inset-0 bg-white/50 dark:bg-gray-900/50 backdrop-blur-sm"></div>
            <div class="relative mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-8">
                    <h1 class="text-4xl font-bold text-gray-800 dark:text-gray-100 mb-4">Encontre a Arena Perfeita</h1>
                    <p class="text-xl text-gray-600 dark:text-gray-300">Descubra as melhores arenas de da sua região</p>
                </div>

                <div class="relative max-w-2xl mx-auto">
                    <div class="bg-white dark:bg-gray-800 shadow-lg border border-primary/20 dark:border-primary/30 overflow-hidden hover:shadow-xl transition-shadow duration-300 rounded-3xl flex items-center">
                        <div class="relative flex-1 w-full">
                            <i class="pi pi-search absolute left-6 top-1/2 transform -translate-y-1/2 text-primary text-lg z-10"></i>
                            <input
                                v-model="searchQuery"
                                @keyup.enter="fetchFields"
                                type="text"
                                placeholder="Buscar por nome da arena..."
                                class="w-full pl-14 pr-4 py-4 text-gray-700 dark:text-gray-200 placeholder-gray-400 dark:placeholder-gray-500 border-0 focus:outline-none focus:ring-0 bg-transparent text-lg"
                            />
                        </div>
                        <button
                            @click="fetchFields"
                            :disabled="loading"
                            class="flex bg-primary hover:bg-primary-600 disabled:bg-gray-400 disabled:cursor-not-allowed text-white px-8 py-4 font-semibold transition-colors duration-200 items-center justify-center gap-2"
                        >
                            <i :class="loading ? 'pi pi-spin pi-spinner text-lg' : 'pi pi-search text-lg'"></i>
                            <span class="hidden sm:inline">{{ loading ? 'Buscando...' : 'Buscar' }}</span>
                        </button>
                    </div>

                    <div class="text-center mt-6">
                        <button
                            @click="toggleFilters"
                            class="inline-flex items-center gap-2 px-6 py-3 bg-white/80 dark:bg-gray-800/80 hover:bg-white dark:hover:bg-gray-800 text-gray-700 dark:text-gray-200 rounded-full border border-primary/20 dark:border-primary/30 hover:border-primary/40 dark:hover:border-primary/50 transition-colors duration-200 font-medium shadow-lg relative"
                        >
                            <i class="pi pi-filter text-lg"></i>
                            <span>Filtros Avançados</span>
                            <span v-if="activeFiltersCount > 0" class="absolute -top-2 -right-2 bg-primary text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold">
                                {{ activeFiltersCount }}
                            </span>
                            <i :class="showFilters ? 'pi pi-chevron-up' : 'pi pi-chevron-down'"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="showFilters" class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Filtros Avançados</h2>
                    <button @click="showFilters = false" class="lg:hidden text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                        <i class="pi pi-times text-xl"></i>
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="space-y-4">
                        <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">Localização</h3>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cidade</label>
                            <InputText v-model="filters.city" placeholder="Ex: São Paulo" class="w-full" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Estado</label>
                            <InputText v-model="filters.state" placeholder="Ex: SP" class="w-full" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Bairro</label>
                            <InputText v-model="filters.district" placeholder="Ex: Centro" class="w-full" />
                        </div>
                    </div>

                    <div class="space-y-4">
                        <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">Características</h3>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo de Esporte</label>
                            <Select v-model="filters.sport_type" :options="sportTypeOptions" optionLabel="label" optionValue="value" placeholder="Todos" class="w-full" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ordenar por</label>
                            <Select v-model="filters.sort" :options="sortOptions" optionLabel="label" optionValue="value" placeholder="Mais Recentes" class="w-full" />
                        </div>
                    </div>

                    <div class="space-y-4">
                        <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">Faixa de Preço</h3>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Preço Mínimo (R$)</label>
                            <InputNumber v-model="filters.price_min" :min="0" :step="10" placeholder="Ex: 50" class="w-full" mode="currency" currency="BRL" locale="pt-BR" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Preço Máximo (R$)</label>
                            <InputNumber v-model="filters.price_max" :min="0" :step="10" placeholder="Ex: 200" class="w-full" mode="currency" currency="BRL" locale="pt-BR" />
                        </div>

                        <div class="pt-4 space-y-3">
                            <button @click="applyFilters" class="w-full bg-primary hover:bg-primary-600 text-white px-4 py-2 rounded-md font-medium transition-colors duration-200">Aplicar Filtros</button>
                            <button @click="clearFilters" class="w-full bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 px-4 py-2 rounded-md font-medium transition-colors duration-200">
                                Limpar Filtros
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <main class="mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div v-if="loading" class="text-center py-16">
                <i class="pi pi-spin pi-spinner text-4xl text-primary mb-4"></i>
                <p class="text-xl text-gray-600 dark:text-gray-300">Buscando as melhores arenas...</p>
            </div>

            <div v-else-if="fields.length === 0" class="text-center py-16">
                <i class="pi pi-search text-6xl text-gray-300 dark:text-gray-600 mb-6"></i>
                <h3 class="text-2xl font-bold text-gray-700 dark:text-gray-200 mb-4">Nenhuma arena encontrada</h3>
                <p class="text-gray-600 dark:text-gray-300">Tente novamente mais tarde.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-8">
                <div v-for="field in paginatedFields" :key="field.id" class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                    <div class="relative h-64 overflow-hidden">
                        <img v-if="field.imagePath" :src="field.imagePath" :alt="field.name" class="w-full h-full object-contain transition-transform duration-300 hover:scale-110" />
                        <div v-else class="w-full h-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center no-image-bg">
                            <div class="text-center">
                                <i class="pi pi-camera text-7xl text-primary mb-2" style="font-size: 5rem"></i>
                                <p class="text-gray-500 dark:text-gray-400">Sem imagem</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-8">
                        <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-2">{{ field.name }}</h3>

                        <div v-if="field.company" class="mb-3 space-y-1">
                            <div class="flex items-center text-gray-600 dark:text-gray-300 text-sm">
                                <i class="pi pi-shop mr-2 text-primary"></i>
                                <button @click="$router.push({ name: 'company-profile', params: { id: field.company.id } })" class="text-primary hover:text-primary-600 transition-colors underline font-medium">
                                    {{ field.company.name }}
                                </button>
                            </div>
                            <div class="flex items-center text-gray-600 dark:text-gray-300 text-sm">
                                <i class="pi pi-map mr-2 text-primary"></i>
                                <span>{{ field.company.address }}</span>
                            </div>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-4">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 dark:text-gray-300">A partir de</span>
                                <span class="text-2xl font-bold text-primary">R$ {{ field.pricePerHour }}</span>
                            </div>
                        </div>

                        <div class="mb-4 space-y-1">
                            <div class="flex items-center text-gray-600 dark:text-gray-300 text-sm">
                                <i class="pi pi-sparkles mr-2 text-primary"></i>
                                <span>{{ field.fieldType.name }}</span>
                            </div>
                            <div class="flex items-center text-gray-600 dark:text-gray-300 text-sm">
                                <i class="pi pi-circle mr-2 text-primary"></i>
                                <span>{{ field.fieldSurface.name }}</span>
                            </div>
                            <div class="flex items-center text-gray-600 dark:text-gray-300 text-sm">
                                <i class="pi pi-expand mr-2 text-primary"></i>
                                <span>{{ field.fieldSize.name }}</span>
                            </div>
                        </div>

                        <Button label="Detalhes" icon="pi pi-eye" @click="viewMore(field)" class="w-full bg-primary text-white" />
                    </div>
                </div>
            </div>

            <div v-if="totalPages" class="flex justify-center items-center space-x-2 mt-12">
                <Button label="Anterior" icon="pi pi-chevron-left" @click="currentPage = Math.max(1, currentPage - 1)" :disabled="currentPage === 1" outlined />
                <Button
                    v-for="page in Array.from({ length: totalPages }, (_, i) => i + 1)"
                    :key="page"
                    :label="page.toString()"
                    @click="currentPage = page"
                    :outlined="currentPage !== page"
                    :class="currentPage === page ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-200'"
                    class="w-10 h-10"
                />
                <Button label="Próxima" icon="pi pi-chevron-right" @click="currentPage = Math.min(totalPages, currentPage + 1)" :disabled="currentPage === totalPages" outlined />
            </div>
        </main>

        <PublicFooter />
    </div>

    <div v-if="showInstallButton" class="fixed bottom-4 right-4 z-50">
        <button @click="installPWA" class="bg-primary text-white px-4 py-2 rounded-lg shadow-lg hover:bg-primary-600 transition-colors flex items-center gap-2">
            <i class="pi pi-download"></i>
            Instalar App
        </button>
    </div>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.no-image-bg {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 20'%3E%3Cpath d='M0,10 Q25,0 50,10 T100,10 V20 H0 Z' fill='rgba(229,231,235,0.3)'/%3E%3C/svg%3E");
    background-repeat: repeat;
    background-size: 100px 20px;
}
</style>
