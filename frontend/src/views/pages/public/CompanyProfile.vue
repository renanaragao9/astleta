<script setup lang="ts">
import { computed, onMounted, onUnmounted, ref } from 'vue';
import { useRoute } from 'vue-router';
import { usePublicCompanyStore } from '@/stores/public/publicCompanyStore';
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import { GOOGLE_API_KEY } from '@/config/apiGoogle';

const route = useRoute();
const publicCompanyStore = usePublicCompanyStore();

const companyId = parseInt(route.params.id as string);

const company = computed(() => publicCompanyStore.company);
const fields = computed(() => publicCompanyStore.fields);
const loading = computed(() => publicCompanyStore.loading);

const showImageModal = ref(false);

const showWifiModal = ref(false);
const wifiPassword = ref('');

const getContactHref = (contact: { type: string; value: string; icon?: string }) => {
    switch (contact.type) {
        case 'WhatsApp':
            return `https://wa.me/+55${contact.value.replace(/\D/g, '')}`;
        case 'E-mail':
            return `mailto:${contact.value}`;
        case 'Instagram':
        case 'Facebook':
            return contact.value;
        default:
            return contact.value;
    }
};

const openLocation = () => {
    const address = company.value?.addresses?.[0]?.fullAddress || '';
    if (!address) return;
    const wazeUrl = `https://waze.com/ul?q=${encodeURIComponent(address)}`;
    window.open(wazeUrl, '_blank');
    setTimeout(() => {
        const googleMapsUrl = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(address)}`;
        window.open(googleMapsUrl, '_blank');
    }, 1000);
};

const fetchCompanyProfile = async () => {
    await publicCompanyStore.fetchCompanyProfile(companyId);
};

const getMapUrl = () => {
    const address = company.value?.addresses?.[0]?.fullAddress || '';
    if (!address) return 'https://www.openstreetmap.org/export/embed.html?bbox=-180,-90,180,90&layer=mapnik';

    const apiKey = GOOGLE_API_KEY;
    return `https://www.google.com/maps/embed/v1/place?key=${apiKey}&q=${encodeURIComponent(address + ', Brasil')}&zoom=16`;
};

const openWifiModal = (password: string) => {
    wifiPassword.value = password;
    showWifiModal.value = true;
};

onMounted(() => {
    window.scrollTo(0, 0);
    fetchCompanyProfile();
});

onUnmounted(() => {
    publicCompanyStore.clearCompany();
});
</script>

<template>
    <div class="min-h-screen bg-white dark:bg-gray-900">
        <PublicTopbar />

        <div v-if="company" class="relative bg-white dark:bg-gray-900">
            <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10">
                <div class="block xl:hidden">
                    <div class="grid lg:grid-cols-3 gap-8 items-start">
                        <div class="lg:col-span-2">
                            <div class="flex flex-col sm:flex-row items-center sm:items-center gap-4 mb-8">
                                <div class="relative">
                                    <img
                                        @click="showImageModal = true"
                                        :src="company.imagePath || 'https://via.placeholder.com/200x200?text=Logo'"
                                        :alt="company.name"
                                        class="w-32 h-32 sm:w-40 sm:h-40 rounded-2xl object-cover shadow-xl ring-4 ring-white dark:ring-gray-800 cursor-pointer"
                                    />
                                </div>
                                <div class="text-center sm:text-center">
                                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-900 dark:text-white mb-3 leading-tight">{{ company.name }}</h1>
                                    <div class="flex items-center justify-center sm:justify-start mb-2">
                                        <i class="pi pi-verified text-primary text-xl mr-2"></i>
                                        <span class="text-sm font-medium text-primary">Verificado</span>
                                    </div>
                                    <p v-if="company.description" class="text-lg text-gray-600 dark:text-gray-300 mb-4 max-w-2xl">
                                        {{ company.description }}
                                    </p>

                                    <div class="flex flex-wrap gap-2 justify-center sm:justify-start">
                                        <div v-if="company.phone" class="inline-flex items-center gap-2 bg-white dark:bg-gray-800 px-3 py-1.5 rounded-full shadow-sm border border-gray-200 dark:border-gray-700">
                                            <i class="pi pi-phone text-primary text-sm"></i>
                                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ company.phone }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="lg:col-span-1">
                            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 border border-gray-100 dark:border-gray-700">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Contatos</h3>
                                <div v-if="company.contacts && company.contacts.length > 0" class="space-y-3">
                                    <template v-for="contact in company.contacts" :key="contact.type">
                                        <a
                                            v-if="contact.type !== 'Wi-Fi'"
                                            :href="getContactHref(contact)"
                                            target="_blank"
                                            class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 dark:bg-gray-700 hover:bg-primary/5 dark:hover:bg-primary/10 transition-all duration-200 group"
                                        >
                                            <div class="w-10 h-10 bg-primary/10 dark:bg-primary/20 rounded-xl flex items-center justify-center group-hover:bg-primary group-hover:text-white transition-all duration-200">
                                                <i :class="(contact as any).icon" class="text-primary group-hover:text-white"></i>
                                            </div>
                                            <span class="font-medium text-gray-700 dark:text-gray-200 group-hover:text-primary">{{ contact.type }}</span>
                                        </a>
                                        <div
                                            v-else
                                            @click="openWifiModal(contact.value)"
                                            class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 dark:bg-gray-700 hover:bg-primary/5 dark:hover:bg-primary/10 transition-all duration-200 group cursor-pointer"
                                        >
                                            <div class="w-10 h-10 bg-primary/10 dark:bg-primary/20 rounded-xl flex items-center justify-center group-hover:bg-primary group-hover:text-white transition-all duration-200">
                                                <i :class="(contact as any).icon || 'pi pi-wifi'" class="text-primary group-hover:text-white"></i>
                                            </div>
                                            <span class="font-medium text-gray-700 dark:text-gray-200 group-hover:text-primary">{{ contact.type }}</span>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <div v-if="company.addresses && company.addresses.length > 0" class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 border border-gray-100 dark:border-gray-700 mt-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Localização</h3>
                                <div v-for="address in company.addresses" :key="address.id" class="space-y-3">
                                    <div class="flex items-start gap-3">
                                        <div class="w-10 h-10 bg-primary/10 dark:bg-primary/20 rounded-xl flex items-center justify-center mt-0.5">
                                            <i class="pi pi-map text-primary"></i>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-gray-600 dark:text-gray-300 text-sm leading-relaxed">{{ address.fullAddress }}</p>
                                            <a
                                                :href="'https://www.google.com/maps?q=' + encodeURIComponent(address.fullAddress)"
                                                target="_blank"
                                                class="inline-flex items-center gap-2 mt-2 text-primary hover:text-primary/80 transition-colors font-medium text-sm"
                                            >
                                                <i class="pi pi-external-link text-xs"></i>
                                                Como chegar
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="hidden xl:block">
                    <div class="text-center mb-12">
                        <div class="flex justify-center mb-6">
                            <div class="relative">
                                <img v-if="company.imagePath" @click="showImageModal = true" :src="company.imagePath" :alt="company.name" class="w-40 h-40 rounded-2xl object-cover shadow-xl ring-6 ring-white dark:ring-gray-800 cursor-pointer" />
                                <div v-else class="w-40 h-40 bg-gray-200 dark:bg-gray-700 flex items-center justify-center rounded-2xl cursor-pointer" @click="showImageModal = true">
                                    <div class="text-center">
                                        <i class="pi pi-camera text-4xl text-primary mb-2"></i>
                                        <p class="text-gray-500 dark:text-gray-400">Sem imagem</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <h1 class="text-4xl xl:text-5xl font-bold text-gray-900 dark:text-white mb-4 leading-tight">{{ company.name }}</h1>

                        <div class="flex items-center justify-center mb-6">
                            <i class="pi pi-verified text-primary text-xl mr-2"></i>
                            <span class="text-base font-medium text-primary">Empresa Verificada</span>
                        </div>

                        <p v-if="company.description" class="text-lg xl:text-xl text-gray-600 dark:text-gray-300 mb-8 max-w-4xl mx-auto leading-relaxed">
                            {{ company.description }}
                        </p>

                        <div class="flex justify-center">
                            <div v-if="company.phone" class="inline-flex items-center gap-3 bg-white dark:bg-gray-800 px-6 py-3 rounded-xl shadow-md border border-gray-200 dark:border-gray-700">
                                <i class="pi pi-phone text-primary text-lg"></i>
                                <span class="text-base font-medium text-gray-700 dark:text-gray-200">{{ company.phone }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-8 max-w-5xl mx-auto">
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 border border-gray-100 dark:border-gray-700">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-12 h-12 bg-primary/10 dark:bg-primary/20 rounded-xl flex items-center justify-center">
                                    <i class="pi pi-phone text-primary text-xl"></i>
                                </div>
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Contatos</h3>
                            </div>

                            <div v-if="company.contacts && company.contacts.length > 0" class="space-y-4">
                                <template v-for="contact in company.contacts" :key="contact.type">
                                    <a
                                        v-if="contact.type !== 'Wi-Fi'"
                                        :href="getContactHref(contact)"
                                        target="_blank"
                                        class="flex items-center gap-4 p-4 rounded-xl bg-gray-50 dark:bg-gray-700 hover:bg-primary/5 dark:hover:bg-primary/10 transition-all duration-300 group"
                                    >
                                        <div class="w-12 h-12 bg-primary/10 dark:bg-primary/20 rounded-xl flex items-center justify-center group-hover:bg-primary group-hover:text-white transition-all duration-300">
                                            <i :class="(contact as any).icon" class="text-primary group-hover:text-white text-lg"></i>
                                        </div>
                                        <span class="font-semibold text-gray-700 dark:text-gray-200 group-hover:text-primary text-lg">{{ contact.type }}</span>
                                    </a>
                                    <div
                                        v-else
                                        @click="openWifiModal(contact.value)"
                                        class="flex items-center gap-4 p-4 rounded-xl bg-gray-50 dark:bg-gray-700 hover:bg-primary/5 dark:hover:bg-primary/10 transition-all duration-300 group cursor-pointer"
                                    >
                                        <div class="w-12 h-12 bg-primary/10 dark:bg-primary/20 rounded-xl flex items-center justify-center group-hover:bg-primary group-hover:text-white transition-all duration-300">
                                            <i :class="(contact as any).icon || 'pi pi-wifi'" class="text-primary group-hover:text-white text-lg"></i>
                                        </div>
                                        <span class="font-semibold text-gray-700 dark:text-gray-200 group-hover:text-primary text-lg">{{ contact.type }}</span>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div v-if="company.addresses && company.addresses.length > 0" class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 border border-gray-100 dark:border-gray-700">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-12 h-12 bg-primary/10 dark:bg-primary/20 rounded-xl flex items-center justify-center">
                                    <i class="pi pi-map text-primary text-xl"></i>
                                </div>
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Localização</h3>
                            </div>

                            <div class="space-y-6">
                                <div class="bg-gray-100 dark:bg-gray-700 rounded-lg overflow-hidden">
                                    <iframe :src="getMapUrl()" width="100%" height="250" style="border: none" title="Localização da empresa"></iframe>
                                </div>

                                <Button label="Como Chegar" icon="pi pi-directions" @click="openLocation" class="w-full bg-primary hover:bg-primary/90 transition-colors" size="large" />
                            </div>

                            <div v-for="address in company.addresses" :key="address.id" class="mt-6">
                                <div class="flex items-start gap-4">
                                    <div class="w-12 h-12 bg-primary/10 dark:bg-primary/20 rounded-xl flex items-center justify-center mt-1">
                                        <i class="pi pi-map-marker text-primary text-xl"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-gray-600 dark:text-gray-300 text-base leading-relaxed mb-3">{{ address.fullAddress }}</p>
                                        <a
                                            :href="'https://www.google.com/maps?q=' + encodeURIComponent(address.fullAddress)"
                                            target="_blank"
                                            class="inline-flex items-center gap-2 text-primary hover:text-primary/80 transition-colors font-semibold text-base"
                                        >
                                            <i class="pi pi-external-link text-lg"></i>
                                            Ver no Google Maps
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <Dialog v-model:visible="showImageModal" modal header="Logo da Empresa" :style="{ width: '50vw' }">
                <img :src="company.imagePath || 'https://via.placeholder.com/200x200?text=Logo'" :alt="company.name" class="w-full h-auto object-cover rounded-lg" />
            </Dialog>

            <Dialog v-model:visible="showWifiModal" modal header="Wi-Fi" :style="{ width: '400px' }">
                <div class="text-center py-8">
                    <div class="w-20 h-20 bg-primary/10 dark:bg-primary/20 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="pi pi-wifi text-primary text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Senha do Wi-Fi</h3>
                    <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-4">
                        <p class="text-2xl font-mono font-bold text-gray-800 dark:text-gray-200">{{ wifiPassword }}</p>
                    </div>
                </div>
            </Dialog>
        </div>

        <main class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16">
            <div class="text-center mb-12">
                <div class="inline-flex items-center gap-2 bg-primary/10 text-primary px-4 py-2 rounded-full text-sm font-medium mb-4">
                    <i class="pi pi-sparkles"></i>
                    Nossas Arenas
                </div>
                <h2 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">Arenas Disponíveis</h2>
                <p class="text-xl text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">Descubra nossos espaços modernos e equipados para você praticar seu esporte favorito</p>
            </div>
            <div v-if="loading" class="text-center py-16">
                <i class="pi pi-spin pi-spinner text-4xl text-primary mb-4"></i>
                <p class="text-xl text-gray-600 dark:text-gray-300">Carregando arenas...</p>
            </div>

            <div v-else-if="fields.length === 0" class="text-center py-16">
                <i class="pi pi-search text-6xl text-gray-300 dark:text-gray-600 mb-6"></i>
                <h3 class="text-2xl font-bold text-gray-700 dark:text-gray-200 mb-4">Nenhuma arena encontrada</h3>
                <p class="text-gray-600 dark:text-gray-300">Esta empresa ainda não possui arenas cadastradas ou nenhuma arena atende aos filtros selecionados.</p>
            </div>

            <div v-else class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-3 gap-8">
                <div v-for="field in fields" :key="field.id" class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                    <div class="relative h-64 overflow-hidden">
                        <img v-if="field.imagePath" :src="field.imagePath" :alt="field.name" class="w-full h-full object-cover transition-transform duration-300 hover:scale-110" />
                        <div v-else class="w-full h-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center no-image-bg">
                            <div class="text-center">
                                <i class="pi pi-camera text-7xl text-primary mb-2" style="font-size: 5rem"></i>
                                <p class="text-gray-500 dark:text-gray-400">Sem imagem</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-8">
                        <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-2">{{ field.name }}</h3>

                        <div v-if="company" class="mb-3 space-y-1">
                            <div class="flex items-center text-gray-600 dark:text-gray-300 text-sm">
                                <i class="pi pi-shop mr-2 text-primary"></i>
                                <span class="font-medium">{{ company.name }}</span>
                            </div>
                            <div v-if="company.addresses && company.addresses.length > 0" class="flex items-center text-gray-600 dark:text-gray-300 text-sm">
                                <i class="pi pi-map mr-2 text-primary"></i>
                                <span>{{ company.addresses[0].fullAddress }}</span>
                            </div>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-4">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 dark:text-gray-300">A partir de</span>
                                <span class="text-2xl font-bold text-primary">R$ {{ field.pricePerHour }}</span>
                            </div>
                        </div>

                        <div class="mb-4 space-y-1">
                            <div v-if="field.fieldType" class="flex items-center text-gray-600 dark:text-gray-300 text-sm">
                                <i class="pi pi-sparkles mr-2 text-primary"></i>
                                <span>{{ field.fieldType.name }}</span>
                            </div>
                            <div v-if="field.fieldSurface" class="flex items-center text-gray-600 dark:text-gray-300 text-sm">
                                <i class="pi pi-circle mr-2 text-primary"></i>
                                <span>{{ field.fieldSurface.name }}</span>
                            </div>
                            <div v-if="field.fieldSize" class="flex items-center text-gray-600 dark:text-gray-300 text-sm">
                                <i class="pi pi-expand mr-2 text-primary"></i>
                                <span>{{ field.fieldSize.name }}</span>
                            </div>
                        </div>

                        <Button label="Detalhes" icon="pi pi-eye" @click="$router.push({ name: 'field-detail', params: { id: field.id } })" class="w-full bg-primary text-white" />
                    </div>
                </div>
            </div>
        </main>

        <PublicFooter />
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
</style>
