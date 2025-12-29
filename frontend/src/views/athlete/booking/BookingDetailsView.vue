<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useBookingStore } from '@/stores/athlete/booking/bookingAthleteStore';
import { useTeamBookingStore } from '@/stores/athlete/team/teamBookingStore';
import { useFormat } from '@/utils/useFormat';
import { useToast } from 'primevue/usetoast';
import BookingParticipants from '@/components/athlete/booking/BookingParticipants.vue';
import BookingStatistics from '@/components/athlete/booking/BookingStatistics.vue';
import BookingRatings from '@/components/athlete/booking/BookingRatings.vue';
import TeamSelection from '@/components/athlete/booking/TeamSelection.vue';
import Tabs from 'primevue/tabs';
import TabList from 'primevue/tablist';
import Tab from 'primevue/tab';
import TabPanels from 'primevue/tabpanels';
import TabPanel from 'primevue/tabpanel';
import Select from 'primevue/select';
import Tag from 'primevue/tag';
import Button from 'primevue/button';
import { formatPhone } from '@/utils/phoneFormatter';

interface Props {
    id: string;
}

const props = defineProps<Props>();
const route = useRoute();
const router = useRouter();
const bookingStore = useBookingStore();
const teamBookingStore = useTeamBookingStore();
const { formatCurrency } = useFormat();
const toast = useToast();

const loading = ref(true);
const activeTab = ref<string>('0');
const isMobile = ref(false);

const tabOptions = [
    { label: 'Participantes', value: '0', icon: 'pi pi-users' },
    { label: 'Times', value: '3', icon: 'pi pi-shield' },
    { label: 'Estatísticas', value: '1', icon: 'pi pi-chart-bar' },
    { label: 'Avaliações', value: '2', icon: 'pi pi-star' }
];

onMounted(() => {
    loadBooking();
    isMobile.value = window.innerWidth < 768;
    window.addEventListener('resize', () => {
        isMobile.value = window.innerWidth < 768;
    });
});

const loadBooking = async (): Promise<void> => {
    try {
        loading.value = true;
        const bookingId = Number(props.id || route.params.id);

        if (!bookingId || isNaN(bookingId)) {
            toast.add({
                severity: 'error',
                summary: 'Erro',
                detail: 'ID da reserva inválido',
                life: 5000
            });
            router.push({ name: 'athleteBookings' });
            return;
        }

        await bookingStore.getBooking(bookingId);
        await teamBookingStore.getTeamBooking(bookingId);

        activeTab.value = '0';

        if (bookingStore.error) {
            toast.add({
                severity: 'error',
                summary: 'Erro',
                detail: bookingStore.error,
                life: 5000
            });
            router.push({ name: 'athleteBookings' });
        }
    } catch {
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: 'Erro ao carregar detalhes da reserva',
            life: 5000
        });
        router.push({ name: 'athleteBookings' });
    } finally {
        loading.value = false;
    }
};

const getStatusLabel = (status: string): string => {
    const statusMap: Record<string, string> = {
        pendente: 'Pendente',
        confirmado: 'Confirmado',
        cancelado: 'Cancelado',
        concluido: 'Concluído'
    };
    return statusMap[status] || status;
};

const getStatusSeverity = (status: string): string => {
    switch (status.toLowerCase()) {
        case 'confirmed':
        case 'ativo':
        case 'confirmado':
            return 'success';
        case 'pending':
        case 'pendente':
            return 'warn';
        case 'cancelled':
        case 'cancelado':
            return 'danger';
        default:
            return 'info';
    }
};

const goBack = (): void => {
    router.push({ name: 'athleteBookings' });
};

const addToCalendar = (): void => {
    if (!bookingStore.booking) return;

    const booking = bookingStore.booking;
    const title = `Reserva ${booking.bookingNumber} - ${booking.field?.name}`;

    const [day, month, year] = booking.bookingDate.split('/');
    const dateStr = `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;

    const startDate = new Date(`${dateStr}T${booking.startTime}`);
    const endDate = new Date(`${dateStr}T${booking.endTime}`);

    if (isNaN(startDate.getTime()) || isNaN(endDate.getTime())) {
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: 'Datas da reserva são inválidas para adicionar ao calendário',
            life: 5000
        });
        return;
    }

    const companyName = booking.company?.name ? ` - ${booking.company.name}` : '';
    const companyAddress = booking.company?.address ? `\n📍 ${booking.company.address}` : '';
    const details = `Reserva confirmada para ${booking.field?.name}${companyName}.\nHorário Início: ${booking.startTime}\nHorário Fim: ${booking.endTime}\nDuração: ${booking.durationMinutes}\nValor: ${formatCurrency(booking.totalAmount)}${companyAddress}`;
    const location = `${booking.field?.name}${companyName}` || '';

    const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent);

    if (isIOS) {
        const startISO = startDate.toISOString().replace(/[-:]/g, '').split('.')[0] + 'Z';
        const endISO = endDate.toISOString().replace(/[-:]/g, '').split('.')[0] + 'Z';

        const icsContent = `BEGIN:VCALENDAR
            VERSION:2.0
            BEGIN:VEVENT
            SUMMARY:${title}
            DTSTART:${startISO}
            DTEND:${endISO}
            LOCATION:${location}
            DESCRIPTION:${details}
            END:VEVENT
            END:VCALENDAR`;

        const blob = new Blob([icsContent], { type: 'text/calendar;charset=utf-8' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = 'reserva.ics';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
    } else {
        const formatDate = (date: Date) => {
            return date.toISOString().replace(/[-:]/g, '').split('.')[0] + 'Z';
        };

        const start = formatDate(startDate);
        const end = formatDate(endDate);

        const googleUrl = `https://calendar.google.com/calendar/render?action=TEMPLATE&text=${encodeURIComponent(title)}&dates=${start}/${end}&details=${encodeURIComponent(details)}&location=${encodeURIComponent(location)}`;

        window.open(googleUrl, '_blank');
    }
};
</script>

<template>
    <div class="space-y-6 lg:space-y-8 min-h-screen pb-10">
        <div class="lg:grid-cols-12 gap-6">
            <div class="lg:col-span-4 -mx-7 sm:mx-0">
                <div class="bg-white dark:bg-black rounded-lg border border-gray-200 dark:border-gray-700 p-2 lg:p-6 lg:sticky lg:top-6">
                    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-6">
                        <div class="flex items-center gap-3">
                            <Button icon="pi pi-arrow-left" severity="secondary" text @click="goBack" v-tooltip.top="'Voltar'" />
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Detalhes da Reserva</h1>
                        </div>

                        <div v-if="!loading && bookingStore.booking">
                            <div class="flex items-center gap-4 justify-between">
                                <Button v-if="bookingStore.booking.bookingStatus === 'confirmado'" label="Agendar Reserva" icon="pi pi-calendar" severity="primary" @click="addToCalendar" size="small" />
                            </div>
                        </div>
                    </div>

                    <div v-if="loading" class="flex justify-center items-center py-16">
                        <ProgressSpinner />
                        <span class="ml-3 text-lg dark:text-gray-300">Carregando detalhes da reserva...</span>
                    </div>

                    <div v-else-if="bookingStore.booking && bookingStore.booking.id" class="space-y-6">
                        <div class="p-4 rounded-lg border dark:border-gray-700">
                            <div class="flex items-center mb-3">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200"><i class="pi pi-info-circle text-primary mr-2"></i> Informações da Reserva</h3>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-md shadow-sm dark:shadow-lg">
                                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Número da Reserva</label>
                                    <p class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ bookingStore.booking.bookingNumber }}</p>
                                </div>
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-md shadow-sm dark:shadow-lg">
                                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Data da Reserva</label>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ bookingStore.booking.bookingDate }}</p>
                                </div>
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-md shadow-sm dark:shadow-lg">
                                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Status</label>
                                    <div class="mt-1">
                                        <Tag :value="getStatusLabel(bookingStore.booking.bookingStatus)" :severity="getStatusSeverity(bookingStore.booking.bookingStatus)" class="text-sm" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="p-4 rounded-lg border dark:border-gray-700">
                            <div class="flex items-center mb-3">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200"><i class="pi pi-clock text-primary mr-2"></i> Horários e Duração</h3>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-md shadow-sm dark:shadow-lg">
                                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Horário Início</label>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ bookingStore.booking.startTime }}</p>
                                </div>
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-md shadow-sm dark:shadow-lg">
                                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Horário Fim</label>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ bookingStore.booking.endTime }}</p>
                                </div>
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-md shadow-sm dark:shadow-lg">
                                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Duração</label>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ bookingStore.booking.durationMinutes }}</p>
                                </div>
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-md shadow-sm dark:shadow-lg">
                                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Data de Criação</label>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ bookingStore.booking.createdAt }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-4 rounded-lg border dark:border-gray-700">
                            <div class="flex items-center mb-3">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200"><i class="pi pi-map-marker text-primary mr-2"></i> Arena e Participante</h3>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-md shadow-sm dark:shadow-lg">
                                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Arena</label>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ bookingStore.booking.field?.name }}</p>
                                </div>
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-md shadow-sm dark:shadow-lg">
                                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Usuário</label>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ bookingStore.booking.user?.name || '-' }}</p>
                                </div>
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-md shadow-sm dark:shadow-lg">
                                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Telefone</label>
                                    <div class="flex items-center space-x-2">
                                        <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ bookingStore.booking.user?.phone ? formatPhone(bookingStore.booking.user.phone) : '-' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="p-4 rounded-lg border dark:border-gray-700">
                            <div class="flex items-center mb-3">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200"><i class="pi pi-credit-card text-primary mr-2"></i> Informações de Pagamento</h3>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-md shadow-sm dark:shadow-lg">
                                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Preço Total</label>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">30 Min Extra: {{ bookingStore.booking.isExtraHour ? 'Sim' : 'Não' }}</p>
                                    <div v-if="bookingStore.booking.isExtraHour" class="mb-2">
                                        <p class="text-sm text-gray-700 dark:text-gray-300">
                                            Valor por hora: <strong>{{ formatCurrency(parseFloat(bookingStore.booking.field?.pricePerHour)) }}</strong>
                                        </p>
                                        <p class="text-sm text-gray-700 dark:text-gray-300">
                                            + 30 Min Extra: <strong>{{ formatCurrency(parseFloat(bookingStore.booking.field?.extraHourPrice)) }}</strong>
                                        </p>
                                    </div>
                                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ formatCurrency(bookingStore.booking.totalAmount) }}</p>
                                </div>
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-md shadow-sm dark:shadow-lg">
                                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Forma de Pagamento</label>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ bookingStore.booking.paymentForm?.name }}</p>
                                </div>
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-md shadow-sm dark:shadow-lg">
                                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Tipo</label>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ bookingStore.booking.paymentForm?.type }}</p>
                                </div>
                            </div>
                            <div v-if="bookingStore.booking.coupon" class="mt-4 bg-white dark:bg-gray-800 p-3 rounded-md shadow-sm dark:shadow-lg">
                                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Cupom Utilizado</label>
                                <p class="text-lg font-semibold text-blue-600 dark:text-blue-400">{{ bookingStore.booking.coupon.code }}</p>
                            </div>
                        </div>

                        <div v-if="bookingStore.booking.bookingStatus === 'cancelado' && bookingStore.booking.cancellation_reason" class="p-4 rounded-lg border dark:border-gray-700">
                            <div class="flex items-center mb-3">
                                <h3 class="text-lg font-semibold text-red-800 dark:text-red-200">
                                    <i class="pi pi-exclamation-triangle text-primary mr-2"></i>
                                    Motivo do Cancelamento
                                </h3>
                            </div>
                            <div class="bg-white dark:bg-gray-800 p-3 rounded-md shadow-sm dark:shadow-lg">
                                <p class="text-gray-900 dark:text-gray-100">{{ bookingStore.booking.cancellation_reason }}</p>
                            </div>
                        </div>

                        <div class="mt-8">
                            <div v-if="isMobile" class="mb-4">
                                <Select v-model="activeTab" :options="tabOptions" optionLabel="label" optionValue="value" placeholder="Selecione uma aba" class="w-full" />
                            </div>

                            <Tabs v-model:value="activeTab" scrollable :class="{ 'hidden md:block': isMobile }">
                                <TabList>
                                    <Tab value="0" class="font-semibold">
                                        <i class="pi pi-users text-primary mr-2"></i>
                                        Participantes
                                    </Tab>

                                    <Tab value="3" class="font-semibold">
                                        <i class="pi pi-shield text-primary mr-2"></i>
                                        Times
                                    </Tab>

                                    <Tab value="1" class="font-semibold">
                                        <i class="pi pi-chart-bar text-primary mr-2"></i>
                                        Estatísticas
                                    </Tab>

                                    <Tab value="2" class="font-semibold">
                                        <i class="pi pi-star text-primary mr-2"></i>
                                        Avaliações
                                    </Tab>
                                </TabList>

                                <TabPanels>
                                    <TabPanel value="0">
                                        <div class="space-y-6 pt-6">
                                            <BookingParticipants v-if="['confirmado', 'concluido'].includes(bookingStore.booking.bookingStatus)" :booking="bookingStore.booking" />
                                            <div v-else class="text-center py-8">
                                                <i class="pi pi-info-circle text-4xl text-primary mb-4"></i>
                                                <p class="text-gray-600 dark:text-gray-400 text-lg">Os participantes só podem ser adicionados após a confirmação da reserva.</p>
                                                <p class="text-gray-500 dark:text-gray-500 text-sm mt-2">Aguarde a confirmação para gerenciar os participantes do racha.</p>
                                            </div>
                                        </div>
                                    </TabPanel>

                                    <TabPanel value="3">
                                        <div class="space-y-6 pt-6">
                                            <TeamSelection v-if="['confirmado', 'concluido'].includes(bookingStore.booking.bookingStatus)" :booking-id="bookingStore.booking.id" :booking-status="bookingStore.booking.bookingStatus" />
                                            <div v-else class="text-center py-8">
                                                <i class="pi pi-info-circle text-4xl text-primary mb-4"></i>
                                                <p class="text-gray-600 dark:text-gray-400 text-lg">Os times só podem ser gerenciados após a confirmação da reserva.</p>
                                                <p class="text-gray-500 dark:text-gray-500 text-sm mt-2">Aguarde a confirmação para gerenciar os times.</p>
                                            </div>
                                        </div>
                                    </TabPanel>

                                    <TabPanel value="1">
                                        <div class="space-y-6 pt-6">
                                            <BookingStatistics v-if="bookingStore.booking.bookingStatus === 'concluido'" :booking-id="bookingStore.booking.id" :booking-status="bookingStore.booking.bookingStatus" />
                                            <div v-else class="text-center py-8">
                                                <i class="pi pi-info-circle text-4xl text-primary mb-4"></i>
                                                <p class="text-gray-600 dark:text-gray-400 text-lg">As estatísticas estarão disponíveis após a conclusão da reserva.</p>
                                                <p class="text-gray-500 dark:text-gray-500 text-sm mt-2">Aguarde a conclusão para visualizar as estatísticas.</p>
                                            </div>
                                        </div>
                                    </TabPanel>

                                    <TabPanel value="2">
                                        <div class="space-y-6 pt-6">
                                            <BookingRatings
                                                v-if="bookingStore.booking.bookingStatus === 'concluido'"
                                                :booking-id="bookingStore.booking.id"
                                                :booking-status="bookingStore.booking.bookingStatus"
                                                :current-user-id="bookingStore.booking.user?.id || 0"
                                            />
                                            <div v-else class="text-center py-8">
                                                <i class="pi pi-info-circle text-4xl text-primary mb-4"></i>
                                                <p class="text-gray-600 dark:text-gray-400 text-lg">As avaliações estarão disponíveis após a conclusão da reserva.</p>
                                                <p class="text-gray-500 dark:text-gray-500 text-sm mt-2">Aguarde a conclusão para deixar sua avaliação.</p>
                                            </div>
                                        </div>
                                    </TabPanel>
                                </TabPanels>
                            </Tabs>
                        </div>

                        <div class="flex justify-center pt-6 border-t border-gray-200 dark:border-gray-700">
                            <Button label="Voltar às Reservas" icon="pi pi-arrow-left" severity="secondary" @click="goBack" class="px-6 py-3" />
                        </div>
                    </div>

                    <div v-else class="text-center py-16">
                        <i class="pi pi-exclamation-triangle text-6xl text-primary mb-4"></i>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">Reserva não encontrada</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-6">A reserva que você está procurando não existe ou você não tem permissão para acessá-la.</p>
                        <Button label="Voltar às Reservas" icon="pi pi-arrow-left" @click="goBack" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped></style>
