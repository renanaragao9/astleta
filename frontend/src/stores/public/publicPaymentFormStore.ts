import api from '@/config/api';
import type { PaymentForm } from '@/types/company/select/paymentForm';

export class PaymentFormService {
    private static readonly BASE_URL = '/company/payment-forms';

    static async getPaymentForms(type?: string): Promise<PaymentForm[]> {
        const params = type ? { type } : {};
        const response = await api.get(this.BASE_URL, { params });
        return response.data.data;
    }
}
