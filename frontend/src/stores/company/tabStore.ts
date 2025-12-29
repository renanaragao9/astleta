import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import type { Tab, TabResponse, TabPayload, TabItem, TabItemPayload } from '@/types/company/tab';
import type { TabFilters } from '@/types/company/filters/tabFilter';
import type { SendTabDataPayload } from '@/types/company/tab';
import { TabService } from '@/services/company/tabService';
import { TabItemService } from '@/services/company/tabItemService';
import { getErrorMessage } from '@/utils/errorUtils';

export const useTabStore = defineStore('tab', () => {
    const loading = ref(false);
    const error = ref<string | null>(null);
    const expandedRows = ref<Record<string, boolean>>({});
    const selectedTabs = ref<Tab[]>([]);
    const tabs = ref<Tab[]>([]);
    const tabItems = ref<TabItem[]>([]);
    const tab = ref<Tab>({
        id: 0,
        code: '',
        customerName: '',
        status: 'aberto',
        totalAmount: 0,
        openedAt: '',
        companyId: 0,
        created: ''
    });
    const tabItem = ref<TabItem>({
        id: 0,
        quantity: 1,
        total: 0,
        tabId: 0,
        productId: 0,
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

    const canEditTab = computed(() => (status: string) => {
        return status === 'aberto';
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
            error.value = getErrorMessage(err, 'Erro ao carregar comandas');
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
            error.value = getErrorMessage(err, 'Erro ao carregar comanda');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function createTab(tabData: TabPayload) {
        loading.value = true;
        error.value = null;

        try {
            const response = await TabService.createTab(tabData);
            tabs.value.unshift(response.data);
            return response;
        } catch (err) {
            error.value = getErrorMessage(err, 'Erro ao criar comanda');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function updateTab(id: number, tabData: Partial<TabPayload>) {
        loading.value = true;
        error.value = null;

        try {
            const response = await TabService.updateTab(id, tabData);
            const index = tabs.value.findIndex((tab) => tab.id === id);
            if (index !== -1) {
                tabs.value[index] = response.data;
            }
            if (tab.value.id === id) {
                tab.value = response.data;
            }
            return response;
        } catch (err) {
            error.value = getErrorMessage(err, 'Erro ao atualizar comanda');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function cancelTab(id: number) {
        loading.value = true;
        error.value = null;

        try {
            await TabService.cancelTab(id);
            const index = tabs.value.findIndex((tab) => tab.id === id);
            if (index !== -1) {
                tabs.value[index].status = 'cancelado';
            }
            if (tab.value.id === id) {
                tab.value.status = 'cancelado';
            }
        } catch (err) {
            error.value = getErrorMessage(err, 'Erro ao cancelar comanda');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function deleteTab(id: number) {
        loading.value = true;
        error.value = null;

        try {
            await TabService.cancelTab(id);
            tabs.value = tabs.value.filter((tab) => tab.id !== id);
            selectedTabs.value = selectedTabs.value.filter((tab) => tab.id !== id);
        } catch (err) {
            error.value = getErrorMessage(err, 'Erro ao deletar comanda');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function createTabItem(tabItemData: TabItemPayload) {
        loading.value = true;
        error.value = null;

        try {
            const response = await TabItemService.createTabItem(tabItemData);

            const tabIndex = tabs.value.findIndex((t) => t.id === tabItemData.tab_id);
            if (tabIndex !== -1 && tabs.value[tabIndex].tabItems) {
                tabs.value[tabIndex].tabItems!.push(response.data);
            }

            return response;
        } catch (err) {
            error.value = getErrorMessage(err, 'Erro ao adicionar item à comanda');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function deleteTabItem(id: number) {
        loading.value = true;
        error.value = null;

        try {
            await TabItemService.deleteTabItem(id);

            const tabIndex = tabs.value.findIndex((t) => t.tabItems?.some((item) => item.id === id));
            if (tabIndex !== -1 && tabs.value[tabIndex].tabItems) {
                tabs.value[tabIndex].tabItems = tabs.value[tabIndex].tabItems!.filter((item) => item.id !== id);
            }
        } catch (err) {
            error.value = getErrorMessage(err, 'Erro ao remover item da comanda');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function deleteSelectedTabs() {
        loading.value = true;
        error.value = null;

        try {
            const deletePromises = selectedTabs.value.map((tab) => TabService.cancelTab(tab.id));
            await Promise.all(deletePromises);

            const deletedIds = selectedTabs.value.map((tab) => tab.id);
            tabs.value = tabs.value.filter((tab) => !deletedIds.includes(tab.id));

            clearSelectedTabs();
        } catch (err) {
            error.value = getErrorMessage(err, 'Erro ao deletar comandas selecionadas');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function closeTab(id: number) {
        const closeData = {
            status: 'pago' as const,
            closed_at: new Date().toISOString()
        };
        return await updateTab(id, closeData);
    }

    function clearSelectedTabs() {
        selectedTabs.value = [];
    }

    function clearTab() {
        tab.value = {
            id: 0,
            code: '',
            customerName: '',
            status: 'aberto',
            totalAmount: 0,
            openedAt: '',
            companyId: 0,
            created: ''
        };
    }

    function clearTabItem() {
        tabItem.value = {
            id: 0,
            quantity: 1,
            total: 0,
            tabId: 0,
            productId: 0,
            created: ''
        };
    }

    function clearError() {
        error.value = null;
    }

    function expandAll() {
        tabs.value.forEach((tab) => {
            expandedRows.value[tab.id.toString()] = true;
        });
    }

    function collapseAll() {
        expandedRows.value = {};
    }

    async function sendTab(sendData: SendTabDataPayload) {
        loading.value = true;
        error.value = null;

        try {
            const response = await TabService.sendTab(sendData);
            return response;
        } catch (err) {
            error.value = getErrorMessage(err, 'Erro ao enviar comanda');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    return {
        // State
        tabs,
        tab,
        selectedTabs,
        loading,
        error,
        pagination,
        tabItems,
        tabItem,
        expandedRows,

        // Getters
        getTabById,
        hasTabs,
        canEditTab,

        // Actions
        fetchTabs,
        fetchTab,
        createTab,
        updateTab,
        cancelTab,
        deleteTab,
        closeTab,
        createTabItem,
        deleteTabItem,
        clearSelectedTabs,
        deleteSelectedTabs,
        clearTab,
        clearTabItem,
        clearError,
        expandAll,
        collapseAll,
        sendTab
    };
});
