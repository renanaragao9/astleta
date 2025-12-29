import api from '@/config/api';
import type { ExpenseType } from '@/types/company/select/expenseType';

export class ExpenseTypeService {
    private static readonly BASE_URL = '/company/expense-types';

    static async getExpenseTypes(): Promise<ExpenseType[]> {
        const response = await api.get(this.BASE_URL);
        return response.data.data;
    }
}
