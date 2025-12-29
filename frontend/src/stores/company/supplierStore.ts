import { ref, computed } from 'vue';
import { defineStore } from 'pinia';
import { SupplierService } from '@/services/company/supplierService';
import { errorMessageHttpResquestUtils } from '@/utils/errorMessageHttpResquestUtils';
import type { Supplier, SupplierPayload, SupplierResponse } from '@/types/company/supplier';
import type { SupplierFilters } from '@/types/company/filters/supplierFilter';

export const useSupplierStore = defineStore('supplier', () => {
    const loading = ref(false);
    const error = ref<string | null>(null);
    const selectedSuppliers = ref<Supplier[]>([]);
    const suppliers = ref<Supplier[]>([]);
    const supplier = ref<Supplier>({
        id: 0,
        name: '',
        email: '',
        phone: '',
        address: '',
        created: ''
    });
    const pagination = ref({
        currentPage: 1,
        lastPage: 1,
        perPage: 15,
        total: 0
    });

    const hasSuppliers = computed(() => suppliers.value.length > 0);
    const getSupplierById = computed(() => (id: number) => {
        return suppliers.value.find((supplier) => supplier.id === id);
    });

    async function fetchSuppliers(filters: Partial<SupplierFilters> = {}) {
        loading.value = true;
        error.value = null;

        try {
            const response: SupplierResponse = await SupplierService.getSuppliers(filters);
            suppliers.value = response.data;
            pagination.value = {
                currentPage: response.meta.currentPage,
                lastPage: response.meta.lastPage,
                perPage: response.meta.perPage,
                total: response.meta.total
            };
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao carregar fornecedores');
            suppliers.value = [];
        } finally {
            loading.value = false;
        }
    }

    async function fetchSupplier(id: number) {
        loading.value = true;
        error.value = null;

        try {
            const response = await SupplierService.getSupplier(id);
            supplier.value = response.data;
            return response;
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao carregar fornecedor');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function createSupplier(supplierData: SupplierPayload) {
        loading.value = true;
        error.value = null;

        try {
            const response = await SupplierService.createSupplier(supplierData);
            suppliers.value.unshift(response.data);
            return response;
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao criar fornecedor');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function updateSupplier(id: number, supplierData: Partial<SupplierPayload>) {
        loading.value = true;
        error.value = null;

        try {
            const response = await SupplierService.updateSupplier(id, supplierData);
            const index = suppliers.value.findIndex((supplier) => supplier.id === id);
            if (index !== -1) {
                suppliers.value[index] = response.data;
            }
            if (supplier.value.id === id) {
                supplier.value = response.data;
            }
            return response;
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao atualizar fornecedor');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function deleteSupplier(id: number) {
        loading.value = true;
        error.value = null;

        try {
            await SupplierService.deleteSupplier(id);
            suppliers.value = suppliers.value.filter((supplier) => supplier.id !== id);
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao deletar fornecedor');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function deleteSelectedSuppliers() {
        if (selectedSuppliers.value.length === 0) return;

        loading.value = true;
        error.value = null;

        try {
            for (const supplier of selectedSuppliers.value) {
                await SupplierService.deleteSupplier(supplier.id);
            }
            suppliers.value = suppliers.value.filter((supplier) => !selectedSuppliers.value.some((selected) => selected.id === supplier.id));
            selectedSuppliers.value = [];
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao deletar fornecedores selecionados');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function selectSuppliers() {
        loading.value = true;
        error.value = null;

        try {
            const response = await SupplierService.selectSuppliers();
            suppliers.value = response.data;
            return response.data;
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao carregar fornecedores para seleção');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    function clearSelectedSuppliers() {
        selectedSuppliers.value = [];
    }

    function clearSupplier() {
        supplier.value = {
            id: 0,
            name: '',
            email: '',
            phone: '',
            address: '',
            created: ''
        };
    }

    function clearError() {
        error.value = null;
    }

    return {
        // State
        suppliers,
        supplier,
        selectedSuppliers,
        loading,
        error,
        pagination,

        // Getters
        getSupplierById,
        hasSuppliers,

        // Actions
        fetchSuppliers,
        fetchSupplier,
        createSupplier,
        updateSupplier,
        deleteSupplier,
        deleteSelectedSuppliers,
        selectSuppliers,
        clearSelectedSuppliers,
        clearSupplier,
        clearError
    };
});
