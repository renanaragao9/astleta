export interface ExpenseFilters {
    search?: string;
    expenseTypeId?: number;
    type?: 'entrada' | 'saida';
    isPaid?: boolean;
    startDueDate?: string;
    endDueDate?: string;
    sort?: 'id' | 'name' | 'type' | 'amount' | 'dueDate' | 'isPaid' | 'expenseTypeId' | string;
    direction: 'asc' | 'desc';
    perPage: number;
    page: number;
}
