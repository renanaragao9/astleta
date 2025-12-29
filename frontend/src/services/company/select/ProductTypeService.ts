import api from '@/config/api';
import type { ProductType } from '@/types/company/select/productType';

export class ProductTypeService {
    private static readonly BASE_URL = '/company/product-types';

    static async getProductTypes(): Promise<ProductType[]> {
        const response = await api.get(this.BASE_URL);
        return response.data.data;
    }
}
