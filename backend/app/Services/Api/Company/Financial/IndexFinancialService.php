<?php

namespace App\Services\Api\Company\Financial;

use App\Models\Booking;
use App\Models\CompanyTransfer;
use App\Models\Expense;
use App\Models\Purchase;
use App\Models\Tab;
use Illuminate\Http\Request;

class IndexFinancialService extends BaseService
{
    public function run(Request $request): array
    {
        $company = $this->getCompany();

        if (!$company) {
            throw new \Exception('Empresa não encontrada.');
        }

        $companyId = $company->id;
        $startDate = $request->input('start_date') . ' 00:00:00';
        $endDate = $request->input('end_date') . ' 23:59:59';

        $expenses = Expense::query()
            ->where('company_id', $companyId)
            ->whereBetween('due_date', [$startDate, $endDate])
            ->where('is_paid', true)
            ->get();

        $purchases = Purchase::query()
            ->with('supplier')
            ->where('company_id', $companyId)
            ->whereBetween('purchase_date', [$startDate, $endDate])
            ->where('status', 'concluido')
            ->get();

        $expenseOutputs = $expenses->where('type', 'saida');
        $expenseInputs = $expenses->where('type', 'entrada');

        $bookings = Booking::query()
            ->with('field')
            ->whereHas('field', function ($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })
            ->whereBetween('booking_date', [$startDate, $endDate])
            ->whereIn('booking_status', ['confirmado', 'concluido'])
            ->get();

        $tabs = Tab::query()
            ->where('company_id', $companyId)
            ->whereBetween('closed_at', [$startDate, $endDate])
            ->where('status', 'pago')
            ->get();

        $transfers = CompanyTransfer::query()
            ->with('booking')
            ->where('company_id', $companyId)
            ->where('is_free', false)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $totalTransferFees = $transfers->sum('fee_amount');
        $totalPurchases = $purchases->sum('total_amount');
        $totalExpenseOutputs = $expenseOutputs->sum('amount');
        $totalExpenseInputs = $expenseInputs->sum('amount');
        $totalBookingRevenue = $bookings->sum('total_amount');
        $totalTabRevenue = $tabs->sum('total_amount');

        $totalRevenue = $totalBookingRevenue + $totalTabRevenue + $totalExpenseInputs;
        $totalExpenses = $totalExpenseOutputs + $totalPurchases + $totalTransferFees;
        $totalBalance = $totalRevenue - $totalExpenses;

        $possibleExpensesRecords = Expense::query()
            ->where('company_id', $companyId)
            ->whereBetween('due_date', [$startDate, $endDate])
            ->where('is_paid', false)
            ->get();

        $possibleExpenseOutputs = $possibleExpensesRecords->where('type', 'saida');
        $possibleExpenseInputs = $possibleExpensesRecords->where('type', 'entrada');

        $possibleExpenses = $possibleExpenseOutputs->sum('amount');
        $possibleExpenseInputsTotal = $possibleExpenseInputs->sum('amount');

        $possibleBookings = Booking::query()
            ->with('field')
            ->whereHas('field', function ($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })
            ->whereBetween('booking_date', [$startDate, $endDate])
            ->where('booking_status', 'pendente')
            ->get();

        $possibleBookingRevenue = $possibleBookings->sum('total_amount');

        $possibleTabs = Tab::query()
            ->where('company_id', $companyId)
            ->whereBetween('opened_at', [$startDate, $endDate])
            ->where('status', 'aberto')
            ->get();

        $possibleTabRevenue = $possibleTabs->sum('total_amount');

        $totalPossibleRevenue = $possibleBookingRevenue + $possibleTabRevenue + $possibleExpenseInputsTotal;

        $canceledBookings = Booking::query()
            ->with('field')
            ->whereHas('field', function ($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })
            ->whereBetween('booking_date', [$startDate, $endDate])
            ->where('booking_status', 'cancelado')
            ->get();

        $canceledBookingAmount = $canceledBookings->sum('total_amount');

        $canceledTabs = Tab::query()
            ->where('company_id', $companyId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'cancelado')
            ->get();

        $canceledTabAmount = $canceledTabs->sum('total_amount');

        $canceledPurchases = Purchase::query()
            ->with('supplier')
            ->where('company_id', $companyId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'cancelado')
            ->get();

        $canceledPurchaseAmount = $canceledPurchases->sum('total_amount');

        $totalCanceledAmount = $canceledBookingAmount + $canceledTabAmount + $canceledPurchaseAmount;

        $expenseReferences = $expenses->map(function ($expense) {
            return [
                'name' => $expense->name,
                'value' => (float) $expense->amount,
                'type' => $expense->type,
                'createdAt' => $expense->created_at ? $expense->created_at->format('Y-m-d H:i:s') : null,
                'dueDate' => $expense->due_date ? \Carbon\Carbon::parse($expense->due_date)->format('Y-m-d H:i:s') : null,
            ];
        });

        $bookingReferences = $bookings->map(function ($booking) {
            return [
                'name' => $booking->booking_number,
                'value' => (float) $booking->total_amount,
                'createdAt' => $booking->created_at ? $booking->created_at->format('Y-m-d H:i:s') : null,
                'bookingDate' => $booking->booking_date ? \Carbon\Carbon::parse($booking->booking_date)->format('Y-m-d H:i:s') : null,
                'status' => $booking->booking_status,
                'fieldName' => $booking->field ? $booking->field->name : null,
            ];
        });

        $tabReferences = $tabs->map(function ($tab) {
            return [
                'name' => $tab->code,
                'value' => (float) $tab->total_amount,
                'createdAt' => $tab->created_at ? $tab->created_at->format('Y-m-d H:i:s') : null,
                'openedAt' => $tab->opened_at ? $tab->opened_at->format('Y-m-d H:i:s') : null,
                'closedAt' => $tab->closed_at ? $tab->closed_at->format('Y-m-d H:i:s') : null,
                'status' => $tab->status,
            ];
        });

        $transferReferences = $transfers->map(function ($transfer) {
            return [
                'name' => 'Taxa de Transferência - Reserva ' . $transfer->booking->booking_number,
                'value' => (float) $transfer->fee_amount,
                'createdAt' => $transfer->created_at ? $transfer->created_at->format('Y-m-d H:i:s') : null,
                'type' => 'saida',
            ];
        });

        $purchaseReferences = $purchases->map(function ($purchase) {
            return [
                'number' => $purchase->invoice_number,
                'value' => (float) $purchase->total_amount,
                'created' => $purchase->created_at ? $purchase->created_at->format('Y-m-d H:i:s') : null,
                'date' => $purchase->purchase_date ? \Carbon\Carbon::parse($purchase->purchase_date)->format('Y-m-d H:i:s') : null,
            ];
        });

        $possibleExpenseReferences = $possibleExpensesRecords->map(function ($expense) {
            return [
                'name' => $expense->name,
                'value' => (float) $expense->amount,
                'type' => $expense->type,
                'createdAt' => $expense->created_at ? $expense->created_at->format('Y-m-d H:i:s') : null,
                'dueDate' => $expense->due_date ? \Carbon\Carbon::parse($expense->due_date)->format('Y-m-d H:i:s') : null,
            ];
        });

        $possibleBookingReferences = $possibleBookings->map(function ($booking) {
            return [
                'name' => $booking->booking_number,
                'value' => (float) $booking->total_amount,
                'createdAt' => $booking->created_at ? $booking->created_at->format('Y-m-d H:i:s') : null,
                'bookingDate' => $booking->booking_date ? \Carbon\Carbon::parse($booking->booking_date)->format('Y-m-d H:i:s') : null,
                'status' => $booking->booking_status,
                'fieldName' => $booking->field ? $booking->field->name : null,
            ];
        });

        $possibleTabReferences = $possibleTabs->map(function ($tab) {
            return [
                'name' => $tab->code,
                'value' => (float) $tab->total_amount,
                'createdAt' => $tab->created_at ? $tab->created_at->format('Y-m-d H:i:s') : null,
                'openedAt' => $tab->opened_at ? \Carbon\Carbon::parse($tab->opened_at)->format('Y-m-d H:i:s') : null,
                'status' => $tab->status,
            ];
        });

        $canceledBookingReferences = $canceledBookings->map(function ($booking) {
            return [
                'name' => $booking->booking_number,
                'value' => (float) $booking->total_amount,
                'createdAt' => $booking->created_at ? $booking->created_at->format('Y-m-d H:i:s') : null,
                'bookingDate' => $booking->booking_date ? \Carbon\Carbon::parse($booking->booking_date)->format('Y-m-d H:i:s') : null,
                'status' => $booking->booking_status,
                'fieldName' => $booking->field ? $booking->field->name : null,
            ];
        });

        $canceledTabReferences = $canceledTabs->map(function ($tab) {
            return [
                'name' => $tab->code,
                'value' => (float) $tab->total_amount,
                'createdAt' => $tab->created_at ? $tab->created_at->format('Y-m-d H:i:s') : null,
                'closedAt' => $tab->closed_at ? \Carbon\Carbon::parse($tab->closed_at)->format('Y-m-d H:i:s') : null,
                'status' => $tab->status,
            ];
        });

        $canceledPurchaseReferences = $canceledPurchases->map(function ($purchase) {
            return [
                'number' => $purchase->invoice_number,
                'value' => (float) $purchase->total_amount,
                'created' => $purchase->created_at ? $purchase->created_at->format('Y-m-d H:i:s') : null,
                'date' => $purchase->purchase_date ? \Carbon\Carbon::parse($purchase->purchase_date)->format('Y-m-d H:i:s') : null,
                'status' => $purchase->status,
            ];
        });

        $totalBalance = $totalRevenue - $totalExpenses;

        return [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalRevenue' => $totalRevenue,
            'totalExpenses' => $totalExpenses,
            'totalBalance' => $totalBalance,
            'possibleRevenue' => $totalPossibleRevenue,
            'possibleExpenses' => $possibleExpenses,
            'canceledAmount' => $totalCanceledAmount,
            'expenseReferences' => $expenseReferences,
            'bookingReferences' => $bookingReferences,
            'tabReferences' => $tabReferences,
            'transferReferences' => $transferReferences,
            'purchaseReferences' => $purchaseReferences,
            'possibleExpenseReferences' => $possibleExpenseReferences,
            'possibleBookingReferences' => $possibleBookingReferences,
            'possibleTabReferences' => $possibleTabReferences,
            'canceledBookingReferences' => $canceledBookingReferences,
            'canceledTabReferences' => $canceledTabReferences,
            'canceledPurchaseReferences' => $canceledPurchaseReferences,
            'totals' => [
                'expensesCount' => $expenses->count(),
                'bookingsCount' => $bookings->count(),
                'tabsCount' => $tabs->count(),
                'transfersCount' => $transfers->count(),
                'purchasesCount' => $purchases->count(),
                'possibleExpensesCount' => $possibleExpensesRecords->count(),
                'possibleBookingsCount' => $possibleBookings->count(),
                'possibleTabsCount' => $possibleTabs->count(),
                'canceledBookingsCount' => $canceledBookings->count(),
                'canceledTabsCount' => $canceledTabs->count(),
                'canceledPurchasesCount' => $canceledPurchases->count(),
                'bookingRevenue' => $totalBookingRevenue,
                'tabRevenue' => $totalTabRevenue,
                'expenseInputs' => $totalExpenseInputs,
                'expenseOutputs' => $totalExpenseOutputs,
                'transferFees' => $totalTransferFees,
                'purchases' => $totalPurchases,
                'canceledPurchases' => $canceledPurchaseAmount,
            ],
        ];
    }
}
