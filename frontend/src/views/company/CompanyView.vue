<script lang="ts" setup>
import { ref, onMounted, watch } from 'vue';
import { useCompanyStore } from '@/stores/company/companyStore';
import type { Company } from '@/types/company/company';
import type { SimpleBooking } from '@/types/company/booking';
import { useToast } from 'primevue/usetoast';
import type { FileUploadSelectEvent } from 'primevue/fileupload';
import AddressCard from '@/components/company/detail/AddressCard.vue';
import ContactCard from '@/components/company/detail/ContactCard.vue';
import DocumentsCard from '@/components/company/detail/DocumentsCard.vue';
import InfoCard from '@/components/company/detail/InfoCard.vue';
import FinancialCard from '@/components/company/detail/FinancialCard.vue';
import QRCodeGenerator from '@/components/company/QRCodeGenerator.vue';
import BookingCalendar from '@/components/company/detail/BookingCalendar.vue';

const companyStore = useCompanyStore();
const toast = useToast();

const getMonthlyPeriod = () => {
    const now = new Date();
    const year = now.getFullYear();
    const month = now.getMonth() + 1;
    const lastDay = new Date(year, month, 0).getDate();
    return `01/${month.toString().padStart(2, '0')}/${year} até ${lastDay.toString().padStart(2, '0')}/${month.toString().padStart(2, '0')}/${year}`;
};

const isLoading = ref(true);
const company = ref<Company | null>(null);
const isUploading = ref(false);
const fileUploadRef = ref();

const currentDate = ref(new Date());
const calendarBookings = ref<SimpleBooking[]>([]);
const isLoadingBookings = ref(false);

const loadCompanyDetails = async () => {
    try {
        isLoading.value = true;
        await companyStore.fetchCompany();
        company.value = companyStore.company;
    } catch {
        toast.add({ severity: 'error', summary: 'Erro', detail: 'Erro ao carregar detalhes da empresa', life: 3000 });
    } finally {
        isLoading.value = false;
    }
};

const onFileSelect = async (event: FileUploadSelectEvent) => {
    const file = Array.isArray(event.files) ? event.files[0] : event.files;
    if (file) {
        isUploading.value = true;
        try {
            await companyStore.updateImage(file);
            await loadCompanyDetails();
            toast.add({ severity: 'success', summary: 'Sucesso', detail: 'Imagem atualizada com sucesso', life: 3000 });
        } catch {
            if (companyStore.error) {
                toast.add({
                    severity: 'error',
                    summary: 'Erro',
                    detail: companyStore.error,
                    life: 3000
                });
            }
        } finally {
            isUploading.value = false;
        }
    }
};

const loadBookingData = async (year: number, month: number) => {
    try {
        isLoadingBookings.value = true;
        const monthBookings = await companyStore.fetchBookingsByMonth(year, month);
        calendarBookings.value = monthBookings;
    } catch {
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: 'Erro ao carregar dados do calendário',
            life: 3000
        });
        calendarBookings.value = [];
    } finally {
        isLoadingBookings.value = false;
    }
};

const initializePage = async () => {
    await loadCompanyDetails();
};

watch(
    currentDate,
    (newDate) => {
        const year = newDate.getFullYear();
        const month = newDate.getMonth() + 1;
        loadBookingData(year, month);
    },
    { immediate: true }
);

onMounted(initializePage);
</script>

<template>
    <div v-if="isLoading" class="flex items-center justify-center min-h-[400px]">
        <ProgressSpinner strokeWidth="4" class="w-12 h-12" />
    </div>

    <div v-else-if="company" class="space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
            <FinancialCard title="Receitas Totais" :value="Number(company.monthlyRealizedTotals.totalRevenue) || 0" subtitle="Valor" color="green" :tooltip="`Receitas totais do período mensal de ${getMonthlyPeriod()}`" icon="pi pi-arrow-up" />
            <FinancialCard title="Despesas" :value="Number(company.monthlyRealizedTotals.totalExpenses) || 0" subtitle="Valor" color="red" :tooltip="`Despesas do período mensal de ${getMonthlyPeriod()}`" icon="pi pi-arrow-down" />
            <FinancialCard
                title="Taxas do Sistema"
                :value="Number(company.monthlyRealizedTotals.totalTransferFees) || 0"
                subtitle="Valor"
                color="blue"
                :tooltip="`Taxas do sistema do período mensal de ${getMonthlyPeriod()}`"
                icon="pi pi-percentage"
            />
            <FinancialCard
                title="Saldo"
                :value="Number(company.monthlyRealizedTotals.totalBalance) || 0"
                subtitle="Balanço mensal"
                :color="(Number(company.monthlyRealizedTotals.totalBalance) || 0) >= 0 ? 'green' : 'red'"
                :tooltip="`Balanço mensal do período de ${getMonthlyPeriod()}`"
                icon="pi pi-chart-line"
            />
        </div>

        <div class="grid grid-cols-12 gap-6">
            <div class="col-span-full lg:col-span-4 space-y-6">
                <Card class="group hover:shadow-lg transition-all duration-300 border border-gray-200 dark:border-gray-700">
                    <template #header>
                        <div class="p-4 pb-0 flex justify-center">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                                <span class="bg-green-100 text-green-600 rounded-full w-8 h-8 flex items-center justify-center">
                                    <i class="pi pi-image"></i>
                                </span>
                                Imagem da Empresa
                            </h3>
                        </div>
                    </template>
                    <template #content>
                        <div class="px-4 pb-4">
                            <div class="relative overflow-hidden rounded-lg mb-4 flex items-center justify-center h-64">
                                <img v-if="company.imagePath" :src="company.imagePath" alt="Imagem da empresa" class="w-full h-full object-contain" />
                                <i v-else class="fa-regular fa-building text-6xl text-gray-400"></i>
                            </div>
                            <div class="flex justify-center gap-2 mb-4">
                                <FileUpload ref="fileUploadRef" mode="basic" name="demo[]" accept="image/*" :auto="false" @select="onFileSelect" :disabled="isUploading" />
                                <ProgressSpinner v-if="isUploading" strokeWidth="1" class="w-0.5 h-0.5" />
                            </div>
                        </div>
                    </template>
                </Card>

                <AddressCard :address="company.address" />
                <ContactCard :contacts="company.contacts" />
                <QRCodeGenerator :company="company" />
            </div>

            <div class="col-span-full lg:col-span-8 space-y-6">
                <InfoCard :company="company" />
                <BookingCalendar v-model:currentDate="currentDate" :bookings="calendarBookings" :loading="isLoadingBookings" />
                <DocumentsCard :documents="company.documents" />
            </div>
        </div>
    </div>

    <div v-else class="flex items-center justify-center min-h-[400px]">
        <div class="text-center">
            <i class="pi pi-exclamation-triangle text-4xl text-gray-400 mb-4"></i>
            <h3 class="text-lg font-semibold text-gray-600 mb-2">Empresa não encontrada</h3>
            <p class="text-gray-500">Não foi possível carregar os detalhes da empresa.</p>
        </div>
    </div>
</template>
