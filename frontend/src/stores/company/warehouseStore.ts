import { ref, computed } from 'vue';
import { defineStore } from 'pinia';
import { WarehouseService } from '@/services/company/warehouseService';
import { errorMessageHttpResquestUtils } from '@/utils/errorMessageHttpResquestUtils';
import type { Warehouse, WarehousePayload, WarehouseResponse } from '@/types/company/warehouse';
import type { WarehouseFilters } from '@/types/company/filters/warehouseFilter';

export const useWarehouseStore = defineStore('warehouse', () => {
    const loading = ref(false);
    const error = ref<string | null>(null);
    const selectedWarehouses = ref<Warehouse[]>([]);
    const warehouses = ref<Warehouse[]>([]);
    const warehouse = ref<Warehouse>({
        id: 0,
        name: '',
        location: null,
        created: '',
        totalStockValue: 0,
        totalStock: 0,
        totalSold: 0,
        totalUnavailable: 0,
        products: []
    });
    const pagination = ref({
        currentPage: 1,
        lastPage: 1,
        perPage: 15,
        total: 0
    });

    const hasWarehouses = computed(() => warehouses.value.length > 0);
    const getWarehouseById = computed(() => (id: number) => {
        return warehouses.value.find((warehouse) => warehouse.id === id);
    });

    async function fetchWarehouses(filters: Partial<WarehouseFilters> = {}) {
        loading.value = true;
        error.value = null;

        try {
            const response: WarehouseResponse = await WarehouseService.getWarehouses(filters);
            warehouses.value = response.data;
            pagination.value = {
                currentPage: response.meta.currentPage,
                lastPage: response.meta.lastPage,
                perPage: response.meta.perPage,
                total: response.meta.total
            };
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao carregar armazéns');
            warehouses.value = [];
        } finally {
            loading.value = false;
        }
    }

    async function fetchWarehouse(id: number) {
        loading.value = true;
        error.value = null;

        try {
            const response = await WarehouseService.getWarehouse(id);
            warehouse.value = response.data;
            return response;
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao carregar armazém');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function createWarehouse(warehouseData: WarehousePayload) {
        loading.value = true;
        error.value = null;

        try {
            const response = await WarehouseService.createWarehouse(warehouseData);
            warehouses.value.unshift(response.data);
            return response;
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao criar armazém');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function updateWarehouse(id: number, warehouseData: Partial<WarehousePayload>) {
        loading.value = true;
        error.value = null;

        try {
            const response = await WarehouseService.updateWarehouse(id, warehouseData);
            const index = warehouses.value.findIndex((warehouse) => warehouse.id === id);
            if (index !== -1) {
                warehouses.value[index] = response.data;
            }
            if (warehouse.value.id === id) {
                warehouse.value = response.data;
            }
            return response;
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao atualizar armazém');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function deleteWarehouse(id: number) {
        loading.value = true;
        error.value = null;

        try {
            await WarehouseService.deleteWarehouse(id);
            warehouses.value = warehouses.value.filter((warehouse) => warehouse.id !== id);
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao deletar armazém');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function deleteSelectedWarehouses() {
        if (selectedWarehouses.value.length === 0) return;

        loading.value = true;
        error.value = null;

        try {
            for (const warehouse of selectedWarehouses.value) {
                await WarehouseService.deleteWarehouse(warehouse.id);
            }
            warehouses.value = warehouses.value.filter((warehouse) => !selectedWarehouses.value.some((selected) => selected.id === warehouse.id));
            selectedWarehouses.value = [];
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao deletar armazéns selecionados');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function selectWarehouses() {
        loading.value = true;
        error.value = null;

        try {
            const response = await WarehouseService.selectWarehouses();
            warehouses.value = response.data;
            return response.data;
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao carregar armazéns para seleção');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    function clearSelectedWarehouses() {
        selectedWarehouses.value = [];
    }

    function clearWarehouse() {
        warehouse.value = {
            id: 0,
            name: '',
            location: null,
            created: '',
            totalStockValue: 0,
            totalStock: 0,
            totalSold: 0,
            totalUnavailable: 0,
            products: []
        };
    }

    function clearError() {
        error.value = null;
    }

    return {
        // State
        warehouses,
        warehouse,
        selectedWarehouses,
        loading,
        error,
        pagination,

        // Getters
        getWarehouseById,
        hasWarehouses,

        // Actions
        fetchWarehouses,
        fetchWarehouse,
        createWarehouse,
        updateWarehouse,
        deleteWarehouse,
        deleteSelectedWarehouses,
        selectWarehouses,
        clearSelectedWarehouses,
        clearWarehouse,
        clearError
    };
});
