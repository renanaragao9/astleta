import type { ExpenseType } from '@/types/company/select/expenseType';

export interface Expense {
    id: number;
    name: string;
    type: 'entrada' | 'saida';
    amount: number | null;
    description: string;
    expenseTypeId: number;
    dueDate: Date | null;
    isPaid: boolean;
    created: string;
    expenseType: ExpenseType;
}

export interface ExpensePayload {
    name: string;
    type: 'entrada' | 'saida';
    amount: number;
    description?: string;
    expense_type_id: number;
    due_date: string;
    is_paid: boolean;
}

export interface ExpenseResponse {
    data: Expense[];
    links: {
        first: string | null;
        last: string | null;
        prev: string | null;
        next: string | null;
    };
    meta: {
        currentPage: number;
        from: number;
        lastPage: number;
        links: {
            url: string | null;
            label: string;
            page: number | null;
            active: boolean;
        }[];
        path: string;
        perPage: number;
        to: number;
        total: number;
    };
}
