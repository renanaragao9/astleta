import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import type { Expense, ExpenseResponse, ExpensePayload } from '@/types/company/expense';
import type { ExpenseType } from '@/types/company/select/expenseType';
import type { ExpenseFilters } from '@/types/company/filters/expenseFilter';
import { ExpenseService } from '@/services/company/expenseService';
import { getErrorMessage } from '@/utils/errorUtils';

export const useExpenseStore = defineStore('expense', () => {
    const loading = ref(false);
    const error = ref<string | null>(null);
    const selectedExpenses = ref<Expense[]>([]);
    const expenses = ref<Expense[]>([]);
    const expense = ref<Expense>({
        id: 0,
        name: '',
        type: 'saida',
        amount: 0,
        description: '',
        expenseTypeId: 0,
        dueDate: null,
        isPaid: false,
        created: '',
        expenseType: {} as ExpenseType
    });
    const pagination = ref({
        currentPage: 1,
        lastPage: 1,
        perPage: 15,
        total: 0
    });

    const hasExpenses = computed(() => expenses.value.length > 0);
    const getExpenseById = computed(() => (id: number) => {
        return expenses.value.find((expense) => expense.id === id);
    });

    async function fetchExpenses(filters: Partial<ExpenseFilters> = {}) {
        loading.value = true;
        error.value = null;

        try {
            const response: ExpenseResponse = await ExpenseService.getExpenses(filters);
            expenses.value = response.data;
            pagination.value = {
                currentPage: response.meta.currentPage,
                lastPage: response.meta.lastPage,
                perPage: response.meta.perPage,
                total: response.meta.total
            };
        } catch (err) {
            error.value = getErrorMessage(err, 'Erro ao carregar despesas');
            expenses.value = [];
        } finally {
            loading.value = false;
        }
    }

    async function fetchExpense(id: number) {
        loading.value = true;
        error.value = null;

        try {
            const response = await ExpenseService.getExpense(id);
            expense.value = response.data;
            return response;
        } catch (err) {
            error.value = getErrorMessage(err, 'Erro ao carregar despesa');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function createExpense(expenseData: ExpensePayload) {
        loading.value = true;
        error.value = null;

        try {
            const response = await ExpenseService.createExpense(expenseData);
            expenses.value.unshift(response.data);
            return response;
        } catch (err) {
            error.value = getErrorMessage(err, 'Erro ao criar despesa');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function updateExpense(id: number, expenseData: Partial<ExpensePayload>) {
        loading.value = true;
        error.value = null;

        try {
            const response = await ExpenseService.updateExpense(id, expenseData);
            const index = expenses.value.findIndex((expense) => expense.id === id);
            if (index !== -1) {
                expenses.value[index] = response.data;
            }
            if (expense.value.id === id) {
                expense.value = response.data;
            }
            return response;
        } catch (err) {
            error.value = getErrorMessage(err, 'Erro ao atualizar despesa');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function deleteExpense(id: number) {
        loading.value = true;
        error.value = null;

        try {
            await ExpenseService.deleteExpense(id);
            expenses.value = expenses.value.filter((expense) => expense.id !== id);
            selectedExpenses.value = selectedExpenses.value.filter((expense) => expense.id !== id);
        } catch (err) {
            error.value = getErrorMessage(err, 'Erro ao deletar despesa');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function deleteSelectedExpenses() {
        if (selectedExpenses.value.length === 0) return;

        loading.value = true;
        error.value = null;

        try {
            const deletePromises = selectedExpenses.value.map((expense) => (expense.id ? ExpenseService.deleteExpense(expense.id) : Promise.resolve()));

            await Promise.all(deletePromises);

            const selectedIds = selectedExpenses.value.map((expense) => expense.id);
            expenses.value = expenses.value.filter((expense) => !selectedIds.includes(expense.id));
            selectedExpenses.value = [];
        } catch (err) {
            error.value = getErrorMessage(err, 'Erro ao deletar despesas selecionadas');
            throw err;
        } finally {
            loading.value = false;
        }
    }

    function clearSelectedExpenses() {
        selectedExpenses.value = [];
    }

    function clearExpense() {
        expense.value = {
            id: 0,
            name: '',
            type: 'saida',
            amount: 0,
            description: '',
            expenseTypeId: 0,
            dueDate: null,
            isPaid: false,
            created: '',
            expenseType: {} as ExpenseType
        };
    }

    function clearError() {
        error.value = null;
    }

    return {
        // State
        expenses,
        expense,
        selectedExpenses,
        loading,
        error,
        pagination,

        // Getters
        getExpenseById,
        hasExpenses,

        // Actions
        fetchExpenses,
        fetchExpense,
        createExpense,
        updateExpense,
        deleteExpense,
        deleteSelectedExpenses,
        clearSelectedExpenses,
        clearExpense,
        clearError
    };
});
