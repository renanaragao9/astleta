<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useHead } from '@vueuse/head';
import { usePublicFieldStore } from '@/stores/public/publicFieldStore';
import { usePublicBookingStore } from '@/stores/public/publicBookingStore';
import { usePaymentFormStore } from '@/stores/company/select/paymentFormStore';
import { useAuthStore } from '@/stores/auth/loginStore';
import type { PublicFieldSchedule } from '@/types/public/field/fieldDetail';
import type { PublicBookingPayload } from '@/types/public/booking/booking';
import type { PriceDetails } from '@/types/public/booking/priceCalculation';
import type { AvailableSlot } from '@/types/public/booking/availability';
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import DatePicker from 'primevue/datepicker';
import Select from 'primevue/select';
import Textarea from 'primevue/textarea';
import ToggleSwitch from 'primevue/toggleswitch';
import Tag from 'primevue/tag';
import { useFormat } from '@/utils/useFormat';
import { getErrorMessage } from '@/utils/errorUtils';
import { GOOGLE_API_KEY } from '@/config/apiGoogle';
import PublicTopbar from '@/components/public/PublicTopbar.vue';
import PublicFooter from '@/components/public/PublicFooter.vue';
import { formatPhone } from '@/utils/phoneFormatter';

const route = useRoute();
const router = useRouter();
const publicFieldStore = usePublicFieldStore();
const publicBookingStore = usePublicBookingStore();
const paymentFormStore = usePaymentFormStore();
const authStore = useAuthStore();
const { formatCurrency } = useFormat();

const currentImageIndex = ref(0);
const showImageModal = ref(false);
const selectedDate = ref<Date | null>(null);
const submitted = ref(false);
const selectedDay = ref<number | null>(null);

const showConfirmationModal = ref(false);
const isLoadingBooking = ref(false);
const bookingConfirmationMessage = ref<{ type: 'success' | 'error' | 'info' | 'warning'; text: string } | null>(null);

const availableSlots = ref<AvailableSlot[]>([]);
const calculatedDetails = ref<PriceDetails | null>(null);
const timeSlots = ref<string[]>([]);
const includeExtraHour = ref(false);
const priceCalculationError = ref<string>('');

const availabilityMessage = ref<{ type: 'success' | 'error' | 'info' | 'warning'; text: string } | null>(null);
const bookingMessage = ref<{ type: 'success' | 'error' | 'info' | 'warning'; text: string } | null>(null);
const authMessage = ref<{ type: 'success' | 'error' | 'info' | 'warning'; text: string } | null>(null);

const startTime = ref('');
const endTime = ref('');
const paymentType = ref<'online' | 'presencial'>('online');
const paymentFormId = ref<number | null>(null);
const notes = ref('');

const field = computed(() => publicFieldStore.field);
const loading = computed(() => publicFieldStore.loading);

const groupedSchedules = computed(() => {
    if (!field.value?.schedules) return [];

    const grouped: { [key: number]: { day: number; schedules: PublicFieldSchedule[] } } = {};

    field.value.schedules.forEach((schedule: PublicFieldSchedule) => {
        if (!grouped[schedule.dayOfWeek]) {
            grouped[schedule.dayOfWeek] = {
                day: schedule.dayOfWeek,
                schedules: []
            };
        }
        grouped[schedule.dayOfWeek].schedules.push(schedule);
    });

    return Object.values(grouped).sort((a, b) => {
        const dayA = a.day === 0 ? 7 : a.day;
        const dayB = b.day === 0 ? 7 : b.day;
        return dayA - dayB;
    });
});

const images = computed(() => {
    if (!field.value) return [];

    const fieldImages = field.value.fieldImage?.map((img) => img.imagePath) || [];

    if (field.value.imagePath && !fieldImages.includes(field.value.imagePath)) {
        return [field.value.imagePath, ...fieldImages];
    }

    return fieldImages;
});

const filteredPaymentFormOptions = computed(() => {
    return paymentFormStore.paymentFormOptions;
});

const isAuthenticated = computed(() => authStore.isAuthenticated);

const dayOptions = computed(() => {
    return groupedSchedules.value.map((day) => ({
        label: getDayName(day.day),
        value: day.day
    }));
});

const filteredSchedules = computed(() => {
    if (selectedDay.value === null) return groupedSchedules.value;
    return groupedSchedules.value.filter((day) => day.day === selectedDay.value);
});

const goBack = () => {
    if (window.history.length > 1) {
        router.back();
    } else {
        router.push({ name: 'home' });
    }
};

const openLocation = () => {
    const address = field.value?.company?.address || '';
    if (!address) return;
    const wazeUrl = `https://waze.com/ul?q=${encodeURIComponent(address)}`;
    window.open(wazeUrl, '_blank');
    setTimeout(() => {
        const googleMapsUrl = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(address)}`;
        window.open(googleMapsUrl, '_blank');
    }, 1000);
};

const getMapUrl = () => {
    const address = field.value?.company?.address || '';
    if (!address) return 'https://www.openstreetmap.org/export/embed.html?bbox=-180,-90,180,90&layer=mapnik';

    const apiKey = GOOGLE_API_KEY;
    return `https://www.google.com/maps/embed/v1/place?key=${apiKey}&q=${encodeURIComponent(address + ', Brasil')}&zoom=16`;
};

const previousImage = () => {
    currentImageIndex.value = currentImageIndex.value === 0 ? images.value.length - 1 : currentImageIndex.value - 1;
};

const nextImage = () => {
    currentImageIndex.value = currentImageIndex.value === images.value.length - 1 ? 0 : currentImageIndex.value + 1;
};

const openImageModal = () => {
    showImageModal.value = true;
};

const closeImageModal = () => {
    showImageModal.value = false;
};

const getDayName = (dayOfWeek: number): string => {
    const days: { [key: number]: string } = {
        1: 'Segunda-feira',
        2: 'Terça-feira',
        3: 'Quarta-feira',
        4: 'Quinta-feira',
        5: 'Sexta-feira',
        6: 'Sábado',
        7: 'Domingo'
    };
    return days[dayOfWeek] || 'Dia não definido';
};

async function checkAvailability(): Promise<void> {
    if (!authStore.isAuthenticated) {
        authMessage.value = { type: 'warning', text: 'Faça login para consultar disponibilidade.' };
        return;
    }

    if (field.value?.id && selectedDate.value) {
        try {
            const dateParam = new Date(selectedDate.value);
            const response = await publicBookingStore.getAvailability(field.value.id, dateParam);
            availableSlots.value = response.availableSlots || [];
            generateTimeSlots();
            if (availableSlots.value.length === 0) {
                availabilityMessage.value = { type: 'info', text: 'Não há horários disponíveis para este dia.' };
                startTime.value = '';
                endTime.value = '';
            } else {
                availabilityMessage.value = null;
            }
            priceCalculationError.value = '';
            bookingMessage.value = null;
        } catch {
            availabilityMessage.value = { type: 'error', text: 'Erro ao consultar disponibilidade' };
        }
    }
}

function generateTimeSlots(): void {
    if (availableSlots.value.length > 0) {
        const startTimes = availableSlots.value.map((slot: AvailableSlot) => slot.startTime);
        const endTimes = availableSlots.value.map((slot: AvailableSlot) => slot.endTime);
        timeSlots.value = [...new Set([...startTimes, ...endTimes])].sort();
    } else {
        timeSlots.value = [];
    }
}

function clearPriceErrorAndCalculate() {
    priceCalculationError.value = '';
    bookingMessage.value = null;
    calculatePrice();
}

function getAdjustedEndTime(): string {
    if (!endTime.value) return '';
    if (!includeExtraHour.value) return endTime.value;

    const [hours, minutes] = endTime.value.split(':').map(Number);
    const date = new Date();
    date.setHours(hours, minutes, 0, 0);
    date.setMinutes(date.getMinutes() + 30);

    return date.toTimeString().slice(0, 5);
}

async function calculatePrice(): Promise<void> {
    if (!authStore.isAuthenticated) {
        authMessage.value = { type: 'warning', text: 'Faça login para calcular preço.' };
        return;
    }

    if (field.value?.id && startTime.value && endTime.value && selectedDate.value) {
        try {
            const payload = {
                field_id: field.value.id,
                start_time: startTime.value,
                end_time: endTime.value,
                include_extra_hour: includeExtraHour.value
            };
            const calculation = await publicBookingStore.calculatePrice(payload);
            calculatedDetails.value = calculation;
            priceCalculationError.value = '';
        } catch (error: unknown) {
            let errorMessage = 'Erro ao calcular preço';

            if (error && typeof error === 'object' && 'response' in error) {
                const axiosError = error as { response?: { data?: { message?: string } } };
                errorMessage = axiosError.response?.data?.message || errorMessage;
            }

            priceCalculationError.value = errorMessage;
            calculatedDetails.value = null;
        }
    }
}

function openConfirmationModal(): void {
    if (!authStore.isAuthenticated) {
        authMessage.value = { type: 'error', text: 'Você precisa estar logado para fazer uma reserva.' };
        router.push({ name: 'login', query: { redirect: route.fullPath } });
        return;
    }

    submitted.value = true;

    const paymentFormRequired = filteredPaymentFormOptions.value && filteredPaymentFormOptions.value.length > 0;

    if (field.value?.id && selectedDate.value && startTime.value && endTime.value && paymentType.value && (!paymentFormRequired || paymentFormId.value)) {
        bookingConfirmationMessage.value = null;
        showConfirmationModal.value = true;
    }
}

async function confirmBooking(): Promise<void> {
    isLoadingBooking.value = true;

    try {
        const date = new Date(selectedDate.value!);
        const availability = await publicBookingStore.getAvailability(field.value!.id, date);
        const start = startTime.value;
        const end = endTime.value;

        const isAvailable = (() => {
            if (!availability.availableSlots || availability.availableSlots.length === 0) return false;

            const mappedSlots = availability.availableSlots.map((slot: AvailableSlot) => ({
                startTime: slot.startTime,
                endTime: slot.endTime
            }));

            const sortedSlots = mappedSlots.sort((a: { startTime: string; endTime: string }, b: { startTime: string; endTime: string }) => a.startTime.localeCompare(b.startTime));

            let currentTime = start;
            for (const slot of sortedSlots) {
                if (slot.startTime === currentTime && slot.endTime <= end) {
                    currentTime = slot.endTime;
                    if (currentTime === end) return true;
                }
            }
            return false;
        })();

        if (!isAvailable) {
            bookingConfirmationMessage.value = { type: 'error', text: 'Horário não disponível' };
            return;
        }

        const payload: PublicBookingPayload = {
            field_id: field.value!.id,
            booking_date: date,
            start_time: startTime.value,
            end_time: endTime.value,
            payment_type: paymentType.value,
            booking_status: 'pendente',
            is_extra_hour: includeExtraHour.value,
            notes: notes.value || ''
        };

        const paymentFormRequired = filteredPaymentFormOptions.value && filteredPaymentFormOptions.value.length > 0;

        if (paymentFormRequired && paymentFormId.value) {
            payload.payment_form_id = paymentFormId.value;
        }

        const response = await publicBookingStore.createBooking(payload);

        bookingConfirmationMessage.value = { type: 'success', text: response.message || 'Reserva criada com sucesso!' };
        bookingMessage.value = { type: 'success', text: 'Reserva criada com sucesso!' };
        publicBookingStore.clearBooking();
        availableSlots.value = [];
        calculatedDetails.value = null;
        priceCalculationError.value = '';
        includeExtraHour.value = false;
        startTime.value = '';
        endTime.value = '';
        paymentType.value = 'online';
        paymentFormId.value = null;
        notes.value = '';
        submitted.value = false;
    } catch (error: unknown) {
        const errorMessage = getErrorMessage(error, 'Erro ao salvar reserva');
        bookingConfirmationMessage.value = { type: 'error', text: errorMessage };
    } finally {
        isLoadingBooking.value = false;
    }
}

function closeConfirmationModal(): void {
    if (!isLoadingBooking.value) {
        showConfirmationModal.value = false;
        bookingConfirmationMessage.value = null;
    }
}

onMounted(async () => {
    window.scrollTo(0, 0);

    const fieldId = Number(route.params.id);

    if (fieldId) {
        await publicFieldStore.fetchField(fieldId);
    }

    if (isAuthenticated.value) {
        await paymentFormStore.fetchPaymentForms('online');
    }

    if (groupedSchedules.value.length > 0) {
        selectedDay.value = groupedSchedules.value[0].day;
    }

    document.addEventListener('keydown', (e: KeyboardEvent) => {
        if (showImageModal.value) {
            if (e.key === 'ArrowLeft') {
                previousImage();
            } else if (e.key === 'ArrowRight') {
                nextImage();
            } else if (e.key === 'Escape') {
                closeImageModal();
            }
        }
    });
});

watch(selectedDate, (newDate) => {
    if (newDate) {
        const jsDay = newDate.getDay();
        const backendDay = jsDay === 0 ? 7 : jsDay;
        selectedDay.value = backendDay;
    } else {
        if (groupedSchedules.value.length > 0) {
            selectedDay.value = groupedSchedules.value[0].day;
        } else {
            selectedDay.value = null;
        }
    }
});

const pageTitle = computed(() => (field.value ? `${field.value.name} - Astleta` : 'Detalhes do Campo - Astleta'));
const pageDescription = computed(() => (field.value ? `Reserve ${field.value.name} na ${field.value.company.name}. ${field.value.description || 'Campo esportivo disponível para reserva.'}` : 'Detalhes do campo esportivo.'));

useHead({
    title: pageTitle,
    meta: [
        {
            name: 'description',
            content: pageDescription
        },
        {
            name: 'keywords',
            content: 'reserva de campo, arena esportiva, quadra, esporte'
        },
        { property: 'og:title', content: pageTitle },
        { property: 'og:description', content: pageDescription },
        { property: 'og:type', content: 'website' }
    ]
});
</script>

<template>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <PublicTopbar />

        <div v-if="loading" class="flex justify-center items-center h-96">
            <i class="pi pi-spin pi-spinner text-4xl text-primary"></i>
            <p class="text-xl text-gray-600 dark:text-gray-300 ml-4">Carregando detalhes do campo...</p>
        </div>

        <div v-else-if="publicFieldStore.error" class="flex justify-center items-center h-96">
            <div class="text-center">
                <i class="pi pi-exclamation-triangle text-6xl text-red-500 mb-4"></i>
                <h3 class="text-2xl font-bold text-gray-700 dark:text-gray-200 mb-4">Campo não encontrado</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-6">{{ publicFieldStore.error }}</p>
                <Button label="Voltar" icon="pi pi-arrow-left" @click="goBack" />
            </div>
        </div>

        <div v-else-if="field">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-2 space-y-8">
                        <div class="bg-white dark:bg-gray-800 rounded-xl overflow-hidden">
                            <div class="relative h-96 md:h-[500px]">
                                <img v-if="images.length > 0" :src="images[currentImageIndex]" :alt="`${field.name} - Imagem ${currentImageIndex + 1}`" class="w-full h-full object-cover cursor-pointer" @click="openImageModal" />
                                <div v-else class="w-full h-full flex items-center justify-center bg-gray-100 dark:bg-gray-700">
                                    <i class="pi pi-image text-6xl text-gray-400"></i>
                                </div>

                                <Button v-if="images.length > 1" icon="pi pi-chevron-left" @click="previousImage" rounded class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 hover:bg-opacity-70" />

                                <Button v-if="images.length > 1" icon="pi pi-chevron-right" @click="nextImage" rounded class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 hover:bg-opacity-70" />

                                <div v-if="images.length > 0" class="absolute bottom-4 right-4 bg-black bg-opacity-70 text-white px-3 py-1 rounded-full text-sm">{{ currentImageIndex + 1 }} / {{ images.length }}</div>
                            </div>
                        </div>

                        <div v-if="images.length > 1" class="flex justify-center mt-4 space-x-20">
                            <Button icon="pi pi-chevron-left" @click="previousImage" rounded class="bg-black bg-opacity-50 hover:bg-opacity-70" />
                            <Button icon="pi pi-chevron-right" @click="nextImage" rounded class="bg-black bg-opacity-50 hover:bg-opacity-70" />
                        </div>

                        <div class="flex justify-center mt-4">
                            <Button
                                v-if="images.length > 0"
                                icon="pi pi-images"
                                :label="`Ver todas as ${images.length} fotos`"
                                @click="openImageModal"
                                class="bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600 shadow-lg border border-gray-300 dark:border-gray-600"
                            />
                        </div>

                        <div class="bg-white dark:bg-gray-800 rounded-xl p-6">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1">
                                    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">{{ field.name }}</h1>
                                    <div v-if="field.company" class="flex items-center text-gray-600 dark:text-gray-300 mb-3">
                                        <i class="pi pi-map-marker mr-2 text-primary"></i>
                                        <span>{{ field.company.address || 'Endereço não informado' }}</span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-2xl font-bold text-primary">R$ {{ field.pricePerHour }}/h</div>
                                    <div v-if="field.extraHourPrice" class="text-sm text-gray-500">Extra (30min): R$ {{ field.extraHourPrice }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-gray-800 rounded-xl p-6">
                            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-6 flex items-center">
                                <i class="pi pi-info-circle text-primary mr-3"></i>
                                Especificações do Campo
                            </h2>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 text-center">
                                    <i class="pi pi-expand text-primary text-2xl mb-2"></i>
                                    <div class="text-sm text-gray-600 dark:text-gray-300">Tamanho</div>
                                    <div class="font-semibold text-gray-800 dark:text-gray-200">{{ field.fieldSize.name }}</div>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 text-center">
                                    <i class="pi pi-sparkles text-primary text-2xl mb-2"></i>
                                    <div class="text-sm text-gray-600 dark:text-gray-300">Tipo</div>
                                    <div class="font-semibold text-gray-800 dark:text-gray-200">{{ field.fieldType.name }}</div>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 text-center">
                                    <i class="pi pi-circle text-primary text-2xl mb-2"></i>
                                    <div class="text-sm text-gray-600 dark:text-gray-300">Superfície</div>
                                    <div class="font-semibold text-gray-800 dark:text-gray-200">{{ field.fieldSurface.name }}</div>
                                </div>
                            </div>
                        </div>

                        <div v-if="field.description" class="bg-white dark:bg-gray-800 rounded-xl p-6">
                            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-4 flex items-center">
                                <i class="pi pi-align-left text-primary mr-3"></i>
                                Sobre o Campo
                            </h2>
                            <p class="text-gray-700 dark:text-gray-300 leading-relaxed">{{ field.description }}</p>
                        </div>

                        <div class="bg-white dark:bg-gray-800 rounded-xl p-6">
                            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-6 flex items-center">
                                <i class="pi pi-calendar text-primary mr-3"></i>
                                Horários de Funcionamento
                            </h2>

                            <div class="mb-4">
                                <label for="day_filter" class="block font-bold mb-2 text-gray-900 dark:text-gray-100">Filtrar por Dia</label>
                                <Select id="day_filter" v-model="selectedDay" :options="dayOptions" optionLabel="label" optionValue="value" placeholder="Selecione um dia ou veja todos" class="w-full" :clearable="true" />
                            </div>

                            <div class="space-y-4">
                                <div v-for="daySchedule in filteredSchedules" :key="daySchedule.day" class="border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <h3 class="font-semibold text-gray-800 dark:text-gray-200">{{ getDayName(daySchedule.day) }}</h3>
                                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ daySchedule.schedules.length }} horário(s)</span>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                        <div v-for="schedule in daySchedule.schedules" :key="schedule.id" class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 border-l-4 border-primary hover:shadow-md transition-shadow">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center">
                                                    <i class="pi pi-clock text-primary mr-2"></i>
                                                    <span class="font-medium text-gray-800 dark:text-gray-200"> {{ schedule.startTime }} - {{ schedule.endTime }} </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-if="field.selectedItem && field.selectedItem.length > 0" class="bg-white dark:bg-gray-800 rounded-xl p-6">
                            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-6 flex items-center">
                                <i class="pi pi-star text-primary mr-3"></i>
                                Comodidades
                            </h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div v-for="amenity in field.selectedItem" :key="amenity.name" class="flex items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div class="w-10 h-10 flex items-center justify-center bg-primary-50 text-primary rounded-full mr-3">
                                        <i :class="amenity.icon"></i>
                                    </div>
                                    <span class="text-gray-800 dark:text-gray-200 font-medium">{{ amenity.name }}</span>
                                </div>
                            </div>
                        </div>

                        <div v-if="field.company" class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-6 flex items-center">
                                <i class="pi pi-building text-primary mr-3"></i>
                                Sobre a Empresa
                            </h2>

                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0">
                                    <div v-if="field.company.imagePath" class="w-16 h-16 rounded-full overflow-hidden bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                        <img :src="field.company.imagePath" :alt="`Logo ${field.company.name}`" class="w-full h-full object-contain" />
                                    </div>
                                    <div v-else class="w-16 h-16 bg-primary-100 dark:bg-primary-900 rounded-full flex items-center justify-center">
                                        <i class="pi pi-building text-primary text-2xl"></i>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center text-gray-600 dark:text-gray-300 text-sm mb-2">
                                        <i class="pi pi-shop mr-2 text-primary"></i>
                                        <button @click="$router.push({ name: 'company-profile', params: { id: field.company.id } })" class="text-primary hover:text-primary-600 transition-colors underline font-medium">
                                            {{ field.company.name }}
                                        </button>
                                    </div>
                                    <div class="space-y-2 text-sm text-gray-600 dark:text-gray-300">
                                        <div class="flex items-center">
                                            <i class="pi pi-phone mr-2 text-primary"></i>
                                            <a :href="`tel:${field.company.phone || ''}`" class="hover:text-primary transition-colors">
                                                {{ formatPhone(field.company.phone || '') }}
                                            </a>
                                        </div>
                                        <div class="flex items-start">
                                            <i class="pi pi-map-marker mr-2 text-primary mt-1"></i>
                                            <span>{{ field.company.address || 'Endereço não informado' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-if="field.company" class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-6 flex items-center">
                                <i class="pi pi-map text-primary mr-3"></i>
                                Localização
                            </h2>

                            <div class="space-y-4">
                                <div class="bg-gray-100 dark:bg-gray-700 rounded-lg overflow-hidden">
                                    <iframe :src="getMapUrl()" width="100%" height="200" style="border: none" title="Localização do campo"></iframe>
                                </div>

                                <Button label="Como Chegar" icon="pi pi-directions" @click="openLocation" class="w-full bg-primary hover:bg-primary-600" />
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-1">
                        <div class="sticky top-24">
                            <div v-if="isAuthenticated" class="bg-white dark:bg-gray-800 rounded-xl p-6">
                                <div class="text-center mb-6">
                                    <h3 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-2">Reservar Campo</h3>
                                    <div class="text-2xl font-bold text-primary">R$ {{ field.pricePerHour }}/h</div>
                                    <div v-if="field.extraHourPrice" class="text-sm text-gray-500 dark:text-gray-400">Extra (30min): R$ {{ field.extraHourPrice }}</div>
                                </div>

                                <div
                                    v-if="authMessage"
                                    class="mb-4 p-3 rounded-lg"
                                    :class="{
                                        'bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800': authMessage.type === 'warning',
                                        'bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800': authMessage.type === 'error'
                                    }"
                                >
                                    <div class="flex items-center">
                                        <i :class="['mr-2', authMessage.type === 'warning' ? 'pi pi-exclamation-triangle text-yellow-500' : 'pi pi-times-circle text-red-500']"></i>
                                        <span :class="authMessage.type === 'warning' ? 'text-yellow-800 dark:text-yellow-200' : 'text-red-800 dark:text-red-200'">{{ authMessage.text }}</span>
                                    </div>
                                </div>

                                <div
                                    v-if="availabilityMessage"
                                    class="mb-4 p-3 rounded-lg"
                                    :class="{
                                        'bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800': availabilityMessage.type === 'info',
                                        'bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800': availabilityMessage.type === 'error'
                                    }"
                                >
                                    <div class="flex items-center">
                                        <i :class="['mr-2', availabilityMessage.type === 'info' ? 'pi pi-info-circle text-blue-500' : 'pi pi-times-circle text-red-500']"></i>
                                        <span :class="availabilityMessage.type === 'info' ? 'text-blue-800 dark:text-blue-200' : 'text-red-800 dark:text-red-200'">{{ availabilityMessage.text }}</span>
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <div>
                                        <label for="date" class="flex items-center justify-between font-bold mb-2 text-gray-900 dark:text-gray-100">
                                            <span>Data <span class="text-red-500">*</span></span>
                                        </label>
                                        <DatePicker
                                            id="date"
                                            v-model="selectedDate"
                                            dateFormat="dd/mm/yy"
                                            :minDate="new Date()"
                                            :invalid="submitted && !selectedDate"
                                            placeholder="Selecione a data"
                                            class="w-full"
                                            fluid
                                            @date-select="checkAvailability"
                                        />
                                        <small v-if="submitted && !selectedDate" class="text-red-500">Data é obrigatória.</small>
                                    </div>

                                    <div>
                                        <label for="start_time" class="flex items-center justify-between font-bold mb-2 text-gray-900 dark:text-gray-100">
                                            <span>Horário Início <span class="text-red-500">*</span></span>
                                        </label>
                                        <Select
                                            id="start_time"
                                            v-model="startTime"
                                            :options="timeSlots?.map((slot: any) => ({ label: slot, value: slot })) || []"
                                            optionLabel="label"
                                            optionValue="value"
                                            placeholder="Selecione o horário de início"
                                            :invalid="submitted && !startTime"
                                            class="w-full"
                                            @change="clearPriceErrorAndCalculate"
                                        />
                                        <small v-if="submitted && !startTime" class="text-red-500">Horário de início é obrigatório.</small>
                                    </div>

                                    <div>
                                        <label for="end_time" class="flex items-center justify-between font-bold mb-2 text-gray-900 dark:text-gray-100">
                                            <span>Horário Fim <span class="text-red-500">*</span></span>
                                        </label>
                                        <Select
                                            id="end_time"
                                            v-model="endTime"
                                            :options="timeSlots?.map((slot: any) => ({ label: slot, value: slot })) || []"
                                            optionLabel="label"
                                            optionValue="value"
                                            placeholder="Selecione o horário de fim"
                                            :invalid="submitted && !endTime"
                                            class="w-full"
                                            @change="clearPriceErrorAndCalculate"
                                        />
                                        <small v-if="submitted && !endTime" class="text-red-500">Horário de fim é obrigatório.</small>
                                    </div>

                                    <div v-if="filteredPaymentFormOptions && filteredPaymentFormOptions.length > 0">
                                        <label for="payment_form_id" class="flex items-center justify-between font-bold mb-2 text-gray-900 dark:text-gray-100">
                                            <span>Forma de Pagamento <span class="text-red-500">*</span></span>
                                        </label>
                                        <Select
                                            id="payment_form_id"
                                            v-model="paymentFormId"
                                            :options="filteredPaymentFormOptions || []"
                                            optionLabel="label"
                                            optionValue="value"
                                            placeholder="Selecione a forma de pagamento"
                                            :invalid="submitted && filteredPaymentFormOptions && filteredPaymentFormOptions.length > 0 && !paymentFormId"
                                            class="w-full"
                                        />
                                        <small v-if="submitted && filteredPaymentFormOptions && filteredPaymentFormOptions.length > 0 && !paymentFormId" class="text-red-500">Forma de pagamento é obrigatória.</small>
                                    </div>

                                    <div>
                                        <label for="include_extra_hour" class="block font-bold mb-2 text-gray-900 dark:text-gray-100">Incluir Tempo Extra (30min)</label>
                                        <ToggleSwitch v-model="includeExtraHour" @change="clearPriceErrorAndCalculate">
                                            <template #handle="{ checked }">
                                                <i :class="['!text-xs pi', { 'pi-check': checked, 'pi-times': !checked }]" />
                                            </template>
                                        </ToggleSwitch>
                                    </div>

                                    <div>
                                        <label for="notes" class="block font-bold mb-2 text-gray-900 dark:text-gray-100">Observações</label>
                                        <Textarea id="notes" v-model="notes" placeholder="Digite observações sobre a reserva" :autoResize="true" rows="3" class="w-full" />
                                    </div>

                                    <div v-if="availableSlots.length > 0">
                                        <label class="block font-bold mb-2 text-gray-900 dark:text-gray-100">Horários Disponíveis</label>
                                        <div class="flex flex-wrap gap-2">
                                            <Tag v-for="slot in availableSlots" :key="slot.startTime" :value="`${slot.startTime} - ${slot.endTime}`" severity="info" />
                                        </div>
                                    </div>

                                    <div v-if="calculatedDetails">
                                        <label class="block font-bold mb-2 text-gray-900 dark:text-gray-100">Detalhes do Preço</label>
                                        <div class="border dark:border-gray-700 rounded p-4 bg-gray-50 dark:bg-gray-800">
                                            <div class="grid grid-cols-1 gap-2">
                                                <div><span class="font-semibold text-gray-900 dark:text-gray-100">Preço Base:</span> {{ formatCurrency(calculatedDetails.basePrice) }}</div>
                                                <div><span class="font-semibold text-gray-900 dark:text-gray-100">Tempo Extra (30min):</span> {{ formatCurrency(calculatedDetails.extraHourPrice) }}</div>
                                                <div><span class="font-semibold text-gray-900 dark:text-gray-100">Total:</span> {{ formatCurrency(calculatedDetails.totalPrice) }}</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div v-if="priceCalculationError" class="mb-4 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                                        <div class="flex items-center">
                                            <i class="pi pi-exclamation-triangle text-red-500 mr-2"></i>
                                            <span class="text-red-800 dark:text-red-200 text-sm font-medium">{{ priceCalculationError }}</span>
                                        </div>
                                    </div>

                                    <div
                                        v-if="bookingMessage"
                                        class="mb-4 p-3 rounded-lg"
                                        :class="{
                                            'bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800': bookingMessage.type === 'success',
                                            'bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800': bookingMessage.type === 'error'
                                        }"
                                    >
                                        <div class="flex items-center">
                                            <i :class="['mr-2', bookingMessage.type === 'success' ? 'pi pi-check-circle text-green-500' : 'pi pi-times-circle text-red-500']"></i>
                                            <span :class="bookingMessage.type === 'success' ? 'text-green-800 dark:text-green-200' : 'text-red-800 dark:text-red-200'">{{ bookingMessage.text }}</span>
                                        </div>
                                    </div>

                                    <Button label="Confirmar Reserva" icon="pi pi-check" @click="openConfirmationModal" class="w-full bg-blue-600 hover:bg-blue-700 border-blue-600" />
                                </div>

                                <div class="mt-4 text-center">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        <i class="pi pi-shield mr-1"></i>
                                        Pagamento seguro e confirmação rápida
                                    </p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">O pagamento será realizado no estabelecimento após confirmação da reserva</p>
                                </div>
                            </div>

                            <div v-else class="bg-white dark:bg-gray-800 rounded-xl p-6">
                                <div class="text-center mb-6">
                                    <div class="text-3xl font-bold text-primary mb-1">R$ {{ field.pricePerHour }}</div>
                                    <div class="text-sm text-gray-600 dark:text-gray-300">por hora</div>
                                    <div v-if="field.extraHourPrice" class="text-xs text-gray-500 dark:text-gray-400 mt-1">Extra (30min): R$ {{ field.extraHourPrice }}</div>
                                </div>

                                <div class="mt-3 p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                                    <p class="text-sm text-yellow-800 dark:text-yellow-200 text-center">
                                        <i class="pi pi-info-circle mr-1"></i>
                                        Para reservar online, você precisa estar logado.
                                        <router-link :to="{ name: 'login', query: { redirect: $route.fullPath } }" class="font-medium underline hover:text-yellow-900 dark:hover:text-yellow-100">Fazer login</router-link>
                                    </p>
                                </div>

                                <div class="mt-4 text-center">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        <i class="pi pi-shield mr-1"></i>
                                        Pagamento seguro e confirmação rápida
                                    </p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">O pagamento será realizado no estabelecimento após confirmação da reserva</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <PublicFooter />

        <Dialog v-model:visible="showImageModal" modal :closable="true" :showHeader="false" class="w-full max-w-screen-xl" :contentStyle="{ padding: 0 }">
            <div class="relative">
                <img v-if="field" :src="images[currentImageIndex]" :alt="`${field.name} - Imagem ${currentImageIndex + 1}`" class="w-full max-h-[90vh] object-contain" />

                <div v-if="images.length > 1" class="absolute bottom-16 left-1/2 transform -translate-x-1/2 flex items-center justify-center space-x-20">
                    <Button icon="pi pi-chevron-left" @click="previousImage" rounded class="bg-black bg-opacity-50 hover:bg-opacity-70" />
                    <Button icon="pi pi-chevron-right" @click="nextImage" rounded class="bg-black bg-opacity-50 hover:bg-opacity-70" />
                </div>

                <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 text-white bg-black bg-opacity-50 px-4 py-2 rounded-full">{{ currentImageIndex + 1 }} / {{ images.length }}</div>

                <div class="absolute bottom-4 right-4">
                    <Button label="Fechar" icon="pi pi-times" @click="closeImageModal" />
                </div>
            </div>
        </Dialog>

        <Dialog v-model:visible="showConfirmationModal" modal header="Confirmar Reserva" :closable="!isLoadingBooking" class="w-full max-w-lg">
            <div class="space-y-6">
                <div v-if="!bookingConfirmationMessage && !isLoadingBooking" class="flex items-start gap-3">
                    <i class="pi pi-info-circle text-blue-500 text-xl mt-0.5"></i>
                    <p class="text-gray-700 dark:text-gray-200 text-sm leading-relaxed">Revise as informações abaixo antes de confirmar a reserva. Após a confirmação, o horário ficará reservado no sistema.</p>
                </div>

                <div v-if="!bookingConfirmationMessage && !isLoadingBooking" class="rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 p-5 space-y-4">
                    <div class="grid grid-cols-1 gap-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-base text-gray-500">Campo</span>
                            <span class="font-medium text-gray-800 dark:text-gray-200">
                                {{ field?.name }}
                            </span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-base text-gray-500">Empresa</span>
                            <span class="font-medium text-gray-800 dark:text-gray-200">
                                {{ field?.company?.name }}
                            </span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-base text-gray-500">Data</span>
                            <span class="font-medium text-gray-800 dark:text-gray-200">
                                {{ selectedDate?.toLocaleDateString('pt-BR') }}
                            </span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-base text-gray-500">Horário</span>
                            <span class="font-medium text-gray-800 dark:text-gray-200"> {{ startTime }} – {{ includeExtraHour ? getAdjustedEndTime() : endTime }} </span>
                        </div>

                        <div v-if="calculatedDetails" class="flex justify-between">
                            <span class="text-base text-gray-500">Duração</span>
                            <span class="font-medium text-gray-800 dark:text-gray-200"> {{ calculatedDetails.durationHours }}h ({{ calculatedDetails.durationMinutes }} min) </span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-base text-gray-500">Valor por hora</span>
                            <span class="font-medium text-gray-800 dark:text-gray-200">
                                {{ field ? formatCurrency(Number(field.pricePerHour)) : '—' }}
                            </span>
                        </div>

                        <div v-if="includeExtraHour && field?.extraHourPrice" class="flex justify-between">
                            <span class="text-base text-gray-500">Tempo extra (30 min)</span>
                            <span class="font-medium text-green-600 dark:text-green-400"> + {{ formatCurrency(Number(field.extraHourPrice)) }} </span>
                        </div>

                        <div class="pt-4 border-t border-dashed dark:border-gray-600">
                            <div class="flex justify-between items-center">
                                <span class="text-base font-semibold text-gray-700 dark:text-gray-300"> Total da reserva </span>
                                <span class="text-xl font-bold text-primary">
                                    {{ calculatedDetails ? formatCurrency(calculatedDetails.totalPrice) : '—' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="isLoadingBooking" class="flex flex-col items-center py-10">
                    <i class="pi pi-spin pi-spinner text-4xl text-primary mb-4"></i>
                    <p class="text-sm text-gray-600 dark:text-gray-300">Confirmando reserva, aguarde…</p>
                </div>

                <div v-if="bookingConfirmationMessage" class="rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                    <div v-if="bookingConfirmationMessage.type === 'success'" class="flex flex-col items-center text-center gap-5">
                        <img src="/image/storyset/Calendar.svg" alt="Reserva confirmada" class="w-48 h-48" />

                        <div class="space-y-3 max-w-sm">
                            <div class="flex items-center justify-center gap-2">
                                <i class="pi pi-check-circle text-green-500 text-2xl"></i>
                                <p class="text-base font-bold" style="font-size: 1rem">
                                    {{ bookingConfirmationMessage.text }}
                                </p>
                            </div>

                            <div class="pt-4 border-t border-dashed border-gray-200 dark:border-gray-600 space-y-2 text-sm text-gray-600 dark:text-gray-400 text-left">
                                <p>• Você receberá um e-mail com os detalhes da reserva</p>
                                <p>• Compareça no horário agendado com antecedência</p>
                                <p>• O pagamento será realizado no local</p>
                            </div>
                        </div>
                    </div>

                    <div v-else class="flex flex-col items-center text-center gap-4">
                        <i class="pi pi-times-circle text-4xl text-red-500"></i>

                        <div class="space-y-2 max-w-sm">
                            <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Não foi possível confirmar a reserva</h3>

                            <p class="text-base text-gray-600 dark:text-gray-300">
                                {{ bookingConfirmationMessage.text }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <template #footer>
                <Button v-if="!bookingConfirmationMessage && !isLoadingBooking" label="Cancelar" icon="pi pi-times" @click="closeConfirmationModal" class="bg-gray-500 hover:bg-gray-600" />
                <Button v-if="!bookingConfirmationMessage && !isLoadingBooking" label="Confirmar" icon="pi pi-check" @click="confirmBooking" class="bg-green-600 hover:bg-green-700 ml-2" />
                <Button v-if="bookingConfirmationMessage && bookingConfirmationMessage.type === 'success'" label="Entendi" icon="pi pi-check" @click="closeConfirmationModal" class="bg-primary hover:bg-primary-600" />
                <Button
                    v-if="bookingConfirmationMessage && bookingConfirmationMessage.type === 'error'"
                    label="Tentar Novamente"
                    icon="pi pi-refresh"
                    @click="
                        () => {
                            bookingConfirmationMessage = null;
                        }
                    "
                    class="bg-primary hover:bg-primary-600 ml-2"
                />
                <Button v-if="bookingConfirmationMessage && bookingConfirmationMessage.type === 'error'" label="Fechar" icon="pi pi-times" @click="closeConfirmationModal" class="bg-gray-500 hover:bg-gray-600" />
            </template>
        </Dialog>
    </div>
</template>

<style scoped>
.schedule-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

@media (max-width: 768px) {
    .schedule-grid {
        grid-template-columns: 1fr;
    }
}
</style>
