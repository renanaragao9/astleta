import api from '@/config/api';
import type { ProductFilters } from '@/types/company/filters/productFilter';
import type { Product, ProductPayload, ProductResponse } from '@/types/company/product';

export class ProductService {
    private static readonly BASE_URL = '/company/products';

    private static getHeaders(): Record<string, string> {
        return {
            'Content-Type': 'application/json'
        };
    }

    private static preparePayload(productData: ProductPayload | Partial<ProductPayload>): ProductPayload | Partial<ProductPayload> {
        return productData;
    }

    static async getProducts(filters: Partial<ProductFilters> = {}): Promise<ProductResponse> {
        const params = new URLSearchParams();

        if (filters.search) params.append('search', filters.search);
        if (filters.sort) params.append('sort', filters.sort);
        if (filters.direction) params.append('direction', filters.direction);
        if (filters.perPage) params.append('per_page', filters.perPage.toString());
        if (filters.page) params.append('page', filters.page.toString());
        if (filters.productTypeId) params.append('product_type_id', filters.productTypeId.toString());

        const queryString = params.toString();
        const url = queryString ? `${this.BASE_URL}?${queryString}` : this.BASE_URL;

        const response = await api.get(url);
        return response.data;
    }

    static async getProduct(id: number): Promise<{ data: Product; message: string }> {
        const response = await api.get(`${this.BASE_URL}/${id}`);
        return { data: response.data.data, message: response.data.message };
    }

    static async createProduct(productData: ProductPayload): Promise<{ data: Product; message: string }> {
        const payload = this.preparePayload(productData);
        const response = await api.post(this.BASE_URL, payload, {
            headers: this.getHeaders()
        });
        return { data: response.data.data, message: response.data.message };
    }

    static async updateProduct(id: number, productData: Partial<ProductPayload>): Promise<{ data: Product; message: string }> {
        const payload = this.preparePayload(productData);
        const response = await api.put(`${this.BASE_URL}/${id}`, payload, {
            headers: this.getHeaders()
        });
        return { data: response.data.data, message: response.data.message };
    }

    static async deleteProduct(id: number): Promise<{ message: string }> {
        const response = await api.delete(`${this.BASE_URL}/${id}`);
        return { message: response.data.message };
    }

    static async selectProducts(): Promise<{ data: Product[]; message: string }> {
        const response = await api.get(`${this.BASE_URL}/select`);
        return response.data;
    }
}
