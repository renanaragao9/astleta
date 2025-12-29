export interface FinancialPeriod {
    startDate: string;
    endDate: string;
}

export interface FinancialReference {
    name: string;
    value: number;
    type?: 'entrada' | 'saida';
    createdAt?: string;
    dueDate?: string;
    bookingDate?: string;
    openedAt?: string;
    closedAt?: string;
    status?: string;
    fieldName?: string;
}

export interface PurchaseReference {
    number: string;
    value: number;
    created?: string;
    date?: string;
    status?: string;
}

export interface FinancialActual {
    totalRevenue: number;
    totalExpenses: number;
    totalBalance: number;
    expenseReferences: FinancialReference[];
    bookingReferences: FinancialReference[];
    tabReferences: FinancialReference[];
    transferReferences: FinancialReference[];
    purchaseReferences: PurchaseReference[];
}

export interface FinancialPossible {
    revenue: number;
    expenses: number;
    expenseReferences: FinancialReference[];
    bookingReferences: FinancialReference[];
    tabReferences: FinancialReference[];
}

export interface FinancialCanceled {
    amount: number;
    bookingReferences: FinancialReference[];
    tabReferences: FinancialReference[];
    purchaseReferences: PurchaseReference[];
}

export interface FinancialTotals {
    expensesCount: number;
    bookingsCount: number;
    tabsCount: number;
    transfersCount: number;
    possibleExpensesCount: number;
    possibleBookingsCount: number;
    possibleTabsCount: number;
    canceledBookingsCount: number;
    canceledTabsCount: number;
    canceledPurchasesCount: number;
    bookingRevenue: number;
    tabRevenue: number;
    expenseInputs: number;
    expenseOutputs: number;
    transferFees: number;
    purchasesCount: number;
    purchases: number;
    canceledPurchases: number;
}

export interface FinancialData {
    period: FinancialPeriod;
    actual: FinancialActual;
    possible: FinancialPossible;
    canceled: FinancialCanceled;
    totals: FinancialTotals;
}

export interface FinancialFilters {
    start_date?: string;
    end_date?: string;
}
