import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import type { ProductType } from '@/types/company/select/productType';
import { ProductTypeService } from '@/services/company/select/ProductTypeService';
import type { SelectOption } from '@/types/global/SelectOption';
import { getErrorMessage } from '@/utils/errorUtils';

export const useProductTypeStore = defineStore('productType', () => {
    const productTypes = ref<ProductType[]>([]);
    const loading = ref(false);
    const error = ref<string | null>(null);

    const productTypeOptions = computed<SelectOption[]>(() =>
        productTypes.value.map((type) => ({
            label: type.name,
            value: type.id
        }))
    );

    async function fetchProductTypes() {
        if (productTypes.value.length > 0) return;

        loading.value = true;
        error.value = null;

        try {
            productTypes.value = await ProductTypeService.getProductTypes();
        } catch (err) {
            error.value = getErrorMessage(err, 'Erro ao carregar tipos de produto');
            productTypes.value = [];
        } finally {
            loading.value = false;
        }
    }

    function clearError() {
        error.value = null;
    }

    function clearCache() {
        productTypes.value = [];
    }

    return {
        // State
        productTypes,
        loading,
        error,

        // Getters
        productTypeOptions,

        // Actions
        fetchProductTypes,
        clearError,
        clearCache
    };
});
