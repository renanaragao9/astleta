import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import type { Tab, TabResponse } from '@/types/athlete/tabAthlete';
import type { TabFilters } from '@/types/athlete/filters/tabFilter';
import { TabService } from '@/services/athlete/tabAthleteService';
import { errorMessageHttpResquestUtils } from '@/utils/errorMessageHttpResquestUtils';

export const useTabStore = defineStore('athleteTab', () => {
    const loading = ref(false);
    const error = ref<string | null>(null);
    const expandedRows = ref<Record<string, boolean>>({});
    const tabs = ref<Tab[]>([]);
    const tab = ref<Tab>({
        id: 0,
        code: '',
        customerName: '',
        status: 'aberto',
        totalAmount: 0,
        openedAt: '',
        userId: 0,
        created: ''
    });
    const pagination = ref({
        currentPage: 1,
        lastPage: 1,
        perPage: 15,
        total: 0
    });

    const hasTabs = computed(() => tabs.value.length > 0);
    const getTabById = computed(() => (id: number) => {
        return tabs.value.find((tab) => tab.id === id);
    });

    async function fetchTabs(filters: Partial<TabFilters> = {}) {
        loading.value = true;
        error.value = null;

        try {
            const response: TabResponse = await TabService.getTabs(filters);
            tabs.value = response.data;
            pagination.value = {
                currentPage: response.meta.currentPage,
                lastPage: response.meta.lastPage,
                perPage: response.meta.perPage,
                total: response.meta.total
            };
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao carregar comandas');
            tabs.value = [];
        } finally {
            loading.value = false;
        }
    }

    async function fetchTab(id: number) {
        loading.value = true;
        error.value = null;

        try {
            const response = await TabService.getTab(id);
            tab.value = response.data;
            return response;
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao carregar comanda');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function downloadReceipt(id: number): Promise<Blob> {
        error.value = null;
        try {
            return await TabService.downloadReceipt(id);
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao baixar recibo');
            throw err;
        }
    }

    function clearTab() {
        tab.value = {
            id: 0,
            code: '',
            customerName: '',
            status: 'aberto',
            totalAmount: 0,
            openedAt: '',
            userId: 0,
            created: ''
        };
    }

    function toggleRowExpansion(tabId: number) {
        expandedRows.value[tabId] = !expandedRows.value[tabId];
    }

    function clearError() {
        error.value = null;
    }

    return {
        // State
        tabs,
        tab,
        loading,
        error,
        pagination,
        expandedRows,

        // Getters
        getTabById,
        hasTabs,

        // Actions
        fetchTabs,
        fetchTab,
        clearTab,
        toggleRowExpansion,
        clearError,
        downloadReceipt
    };
});
