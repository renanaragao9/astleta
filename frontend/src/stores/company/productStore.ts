import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import type { Product, ProductResponse, ProductPayload } from '@/types/company/product';
import type { ProductType } from '@/types/company/select/productType';
import type { ProductFilters } from '@/types/company/filters/productFilter';
import { ProductService } from '@/services/company/productService';
import { errorMessageHttpResquestUtils } from '@/utils/errorMessageHttpResquestUtils';

export const useProductStore = defineStore('product', () => {
    const loading = ref(false);
    const error = ref<string | null>(null);
    const selectedProducts = ref<Product[]>([]);
    const products = ref<Product[]>([]);
    const product = ref<Product>({
        id: 0,
        name: '',
        description: '',
        price: 0,
        productTypeId: 0,
        isActive: true,
        created: '',
        productType: {} as ProductType
    });
    const pagination = ref({
        currentPage: 1,
        lastPage: 1,
        perPage: 15,
        total: 0
    });

    const hasProducts = computed(() => products.value.length > 0);
    const getProductById = computed(() => (id: number) => {
        return products.value.find((product) => product.id === id);
    });

    async function fetchProducts(filters: Partial<ProductFilters> = {}) {
        loading.value = true;
        error.value = null;

        try {
            const response: ProductResponse = await ProductService.getProducts(filters);
            products.value = response.data;
            pagination.value = {
                currentPage: response.meta.currentPage,
                lastPage: response.meta.lastPage,
                perPage: response.meta.perPage,
                total: response.meta.total
            };
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao carregar produtos');
            products.value = [];
        } finally {
            loading.value = false;
        }
    }

    async function fetchProduct(id: number) {
        loading.value = true;
        error.value = null;

        try {
            const response = await ProductService.getProduct(id);
            product.value = response.data;
            return response;
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao carregar produto');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function createProduct(productData: ProductPayload) {
        loading.value = true;
        error.value = null;

        try {
            const response = await ProductService.createProduct(productData);
            products.value.unshift(response.data);
            return response;
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao criar produto');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function updateProduct(id: number, productData: Partial<ProductPayload>) {
        loading.value = true;
        error.value = null;

        try {
            const response = await ProductService.updateProduct(id, productData);
            const index = products.value.findIndex((product) => product.id === id);
            if (index !== -1) {
                products.value[index] = response.data;
            }
            if (product.value.id === id) {
                product.value = response.data;
            }
            return response;
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao atualizar produto');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function deleteProduct(id: number) {
        loading.value = true;
        error.value = null;

        try {
            await ProductService.deleteProduct(id);
            products.value = products.value.filter((product) => product.id !== id);
            selectedProducts.value = selectedProducts.value.filter((product) => product.id !== id);
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao deletar produto');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function deleteSelectedProducts() {
        if (selectedProducts.value.length === 0) return;

        loading.value = true;
        error.value = null;

        try {
            const deletePromises = selectedProducts.value.map((product) => (product.id ? ProductService.deleteProduct(product.id) : Promise.resolve()));

            await Promise.all(deletePromises);

            const selectedIds = selectedProducts.value.map((product) => product.id);
            products.value = products.value.filter((product) => !selectedIds.includes(product.id));
            selectedProducts.value = [];
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao deletar produtos selecionados');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function selectProducts() {
        loading.value = true;
        error.value = null;

        try {
            const response = await ProductService.selectProducts();
            products.value = response.data;
            return response.data;
        } catch (err) {
            error.value = errorMessageHttpResquestUtils(err, 'Erro ao carregar produtos para seleção');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    function clearSelectedProducts() {
        selectedProducts.value = [];
    }

    function clearProduct() {
        product.value = {
            id: 0,
            name: '',
            description: '',
            price: 0,
            productTypeId: 0,
            isActive: true,
            created: '',
            productType: {} as ProductType
        };
    }

    function clearError() {
        error.value = null;
    }

    return {
        // State
        products,
        product,
        selectedProducts,
        loading,
        error,
        pagination,

        // Getters
        getProductById,
        hasProducts,

        // Actions
        fetchProducts,
        fetchProduct,
        createProduct,
        updateProduct,
        deleteProduct,
        deleteSelectedProducts,
        selectProducts,
        clearSelectedProducts,
        clearProduct,
        clearError
    };
});
