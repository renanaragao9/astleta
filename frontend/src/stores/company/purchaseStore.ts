import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import type { Purchase, PurchaseResponse, PurchasePayload } from '@/types/company/purchase';
import type { PurchaseFilters } from '@/types/company/filters/purchaseFilter';
import { PurchaseService } from '@/services/company/purchaseService';
import { errorMessageHttpResquestUtils } from '@/utils/errorMessageHttpResquestUtils';

export const usePurchaseStore = defineStore('purchase', () => {
    const loading = ref(false);
    const error = ref<string | null>(null);
    const selectedPurchases = ref<Purchase[]>([]);
    const purchases = ref<Purchase[]>([]);
    const purchase = ref<Purchase>({
        id: 0,
        invoiceNumber: '',
        purchaseDate: null,
        status: null,
        totalAmount: 0,
        supplierId: 0,
        created: '',
        supplier: undefined
    });
    const pagination = ref({
        currentPage: 1,
        lastPage: 1,
        perPage: 15,
        total: 0
    });

    const hasPurchases = computed(() => purchases.value.length > 0);
    const getPurchaseById = computed(() => (id: number) => {
        return purchases.value.find((purchase) => purchase.id === id);
    });

    async function fetchPurchases(filters: Partial<PurchaseFilters> = {}) {
        loading.value = true;
        error.value = null;

        try {
            const response: PurchaseResponse = await PurchaseService.getPurchases(filters);
            purchases.value = response.data;
            pagination.value = {
                currentPage: response.meta.currentPage,
                lastPage: response.meta.lastPage,
                perPage: response.meta.perPage,
                total: response.meta.total
            };
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao carregar compras');
            purchases.value = [];
        } finally {
            loading.value = false;
        }
    }

    async function fetchPurchase(id: number) {
        loading.value = true;
        error.value = null;

        try {
            const response = await PurchaseService.getPurchase(id);
            purchase.value = response.data;
            return response;
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao carregar compra');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function createPurchase(purchaseData: PurchasePayload) {
        loading.value = true;
        error.value = null;

        try {
            const response = await PurchaseService.createPurchase(purchaseData);
            purchases.value.unshift(response.data);
            return response;
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao criar compra');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function updatePurchase(id: number, purchaseData: Partial<PurchasePayload>) {
        loading.value = true;
        error.value = null;

        try {
            const response = await PurchaseService.updatePurchase(id, purchaseData);
            const index = purchases.value.findIndex((purchase) => purchase.id === id);
            if (index !== -1) {
                purchases.value[index] = response.data;
            }
            if (purchase.value.id === id) {
                purchase.value = response.data;
            }
            return response;
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao atualizar compra');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function deletePurchase(id: number) {
        loading.value = true;
        error.value = null;

        try {
            await PurchaseService.deletePurchase(id);
            purchases.value = purchases.value.filter((purchase) => purchase.id !== id);
            selectedPurchases.value = selectedPurchases.value.filter((purchase) => purchase.id !== id);
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao deletar compra');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function deleteSelectedPurchases() {
        if (selectedPurchases.value.length === 0) return;

        loading.value = true;
        error.value = null;

        try {
            const deletePromises = selectedPurchases.value.map((purchase) => (purchase.id ? PurchaseService.deletePurchase(purchase.id) : Promise.resolve()));

            await Promise.all(deletePromises);

            const selectedIds = selectedPurchases.value.map((purchase) => purchase.id);
            purchases.value = purchases.value.filter((purchase) => !selectedIds.includes(purchase.id));
            selectedPurchases.value = [];
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao deletar compras selecionadas');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    function clearSelectedPurchases() {
        selectedPurchases.value = [];
    }

    function clearPurchase() {
        purchase.value = {
            id: 0,
            invoiceNumber: '',
            purchaseDate: null,
            status: null,
            totalAmount: 0,
            supplierId: 0,
            created: '',
            supplier: undefined
        };
    }

    function clearError() {
        error.value = null;
    }

    return {
        // State
        purchases,
        purchase,
        selectedPurchases,
        loading,
        error,
        pagination,

        // Getters
        getPurchaseById,
        hasPurchases,

        // Actions
        fetchPurchases,
        fetchPurchase,
        createPurchase,
        updatePurchase,
        deletePurchase,
        deleteSelectedPurchases,
        clearSelectedPurchases,
        clearPurchase,
        clearError
    };
});
