<?php

namespace App\Services\Api\Company\Resource;

use App\Models\Booking;
use App\Models\CompanyTransfer;
use App\Models\Expense;
use App\Models\Tab;
use App\Models\Purchase;
use Carbon\Carbon;

class CompanyFinancialService
{
    public static function run(int $companyId): array
    {
        $startDate = Carbon::now()->startOfMonth()->format('Y-m-d H:i:s');
        $endDate = Carbon::now()->endOfMonth()->format('Y-m-d H:i:s');

        $expenses = Expense::query()
            ->where('company_id', $companyId)
            ->whereBetween('due_date', [$startDate, $endDate])
            ->where('is_paid', true)
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

        $purchases = Purchase::query()
            ->with('supplier')
            ->where('company_id', $companyId)
            ->whereBetween('purchase_date', [$startDate, $endDate])
            ->where('status', 'concluido')
            ->get();

        $totalExpenseOutputs = $expenseOutputs->sum('amount');
        $totalExpenseInputs = $expenseInputs->sum('amount');
        $totalBookingRevenue = $bookings->sum('total_amount');
        $totalTabRevenue = $tabs->sum('total_amount');
        $totalPurchases = $purchases->sum('total_amount');

        $totalRevenue = $totalBookingRevenue + $totalTabRevenue + $totalExpenseInputs;

        $totalTransferFees = CompanyTransfer::query()
            ->where('company_id', $companyId)
            ->where('is_free', false)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('fee_amount');

        $totalExpenses = $totalExpenseOutputs + $totalTransferFees + $totalPurchases;
        $totalBalance = $totalRevenue - $totalExpenses;

        return [
            'totalRevenue' => $totalRevenue,
            'totalExpenses' => $totalExpenses,
            'totalBalance' => $totalBalance,
            'totalTransferFees' => $totalTransferFees,
        ];
    }
}
