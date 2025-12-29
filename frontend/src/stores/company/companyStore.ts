import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import type { Company } from '@/types/company/company';
import type { SimpleBooking } from '@/types/company/booking';
import { CompanyService } from '@/services/company/companyService';
import { BookingService } from '@/services/company/bookingService';
import { getErrorMessage } from '@/utils/errorUtils';

export const useCompanyStore = defineStore('company', () => {
    const company = ref<Company | null>(null);
    const bookings = ref<SimpleBooking[]>([]);
    const loading = ref(false);
    const loadingBookings = ref(false);
    const error = ref<string | null>(null);

    const hasCompany = computed(() => company.value !== null);

    async function fetchCompany() {
        loading.value = true;
        error.value = null;

        try {
            const response = await CompanyService.getCompany();
            company.value = response.data;
        } catch (err: unknown) {
            error.value = getErrorMessage(err, 'Erro ao carregar dados da empresa');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    function clearCompany() {
        company.value = null;
        error.value = null;
    }

    async function updateImage(image: File): Promise<void> {
        loading.value = true;
        error.value = null;

        try {
            const response = await CompanyService.updateImage(image);
            if (company.value) {
                company.value.imagePath = response.data.image_path;
            }
        } catch (err) {
            error.value = getErrorMessage(err, 'Erro ao atualizar imagem');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function fetchBookingsByMonth(year: number, month: number): Promise<SimpleBooking[]> {
        try {
            const response = await BookingService.getBookingsByMonth(year, month);
            return response.data;
        } catch (err: unknown) {
            error.value = getErrorMessage(err, 'Erro ao carregar reservas do mês');
            throw err;
        }
    }

    return {
        company,
        bookings,
        loading,
        loadingBookings,
        error,
        hasCompany,
        fetchCompany,
        clearCompany,
        updateImage,
        fetchBookingsByMonth
    };
});
