import api from '@/config/api';
import type { Expense, ExpenseResponse, ExpensePayload } from '@/types/company/expense';
import type { ExpenseFilters } from '@/types/company/filters/expenseFilter';

export class ExpenseService {
    private static readonly BASE_URL = '/company/expenses';

    private static preparePayload(expenseData: ExpensePayload | Partial<ExpensePayload>): ExpensePayload | Partial<ExpensePayload> {
        return expenseData;
    }

    private static getHeaders(): Record<string, string> {
        return {
            'Content-Type': 'application/json'
        };
    }

    static async getExpenses(filters: Partial<ExpenseFilters> = {}): Promise<ExpenseResponse> {
        const params = new URLSearchParams();

        if (filters.search) params.append('search', filters.search);
        if (filters.sort) params.append('sort', filters.sort);
        if (filters.direction) params.append('direction', filters.direction);
        if (filters.perPage) params.append('per_page', filters.perPage.toString());
        if (filters.page) params.append('page', filters.page.toString());
        if (filters.expenseTypeId) params.append('expense_type_id', filters.expenseTypeId.toString());
        if (filters.type) params.append('type', filters.type);
        if (filters.isPaid !== undefined && filters.isPaid !== null) params.append('is_paid', filters.isPaid.toString());
        if (filters.startDueDate) params.append('start_due_date', filters.startDueDate);
        if (filters.endDueDate) params.append('end_due_date', filters.endDueDate);

        const queryString = params.toString();
        const url = queryString ? `${this.BASE_URL}?${queryString}` : this.BASE_URL;

        const response = await api.get(url);
        return response.data;
    }

    static async getExpense(id: number): Promise<{ data: Expense; message: string }> {
        const response = await api.get(`${this.BASE_URL}/${id}`);
        return { data: response.data.data, message: response.data.message };
    }

    static async createExpense(expenseData: ExpensePayload): Promise<{ data: Expense; message: string }> {
        const payload = this.preparePayload(expenseData);
        const response = await api.post(this.BASE_URL, payload, {
            headers: this.getHeaders()
        });
        return { data: response.data.data, message: response.data.message };
    }

    static async updateExpense(id: number, expenseData: Partial<ExpensePayload>): Promise<{ data: Expense; message: string }> {
        const payload = this.preparePayload(expenseData);
        const response = await api.put(`${this.BASE_URL}/${id}`, payload, {
            headers: this.getHeaders()
        });
        return { data: response.data.data, message: response.data.message };
    }

    static async deleteExpense(id: number): Promise<{ message: string }> {
        const response = await api.delete(`${this.BASE_URL}/${id}`);
        return { message: response.data.message };
    }
}
