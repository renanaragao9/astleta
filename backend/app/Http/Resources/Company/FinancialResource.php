<?php

namespace App\Http\Resources\Company;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FinancialResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->organizeData(),
        ];
    }

    private function organizeData(): array
    {
        $data = $this->resource;

        return [
            'period' => [
                'startDate' => $data['startDate'],
                'endDate' => $data['endDate'],
            ],
            'actual' => [
                'totalRevenue' => $data['totalRevenue'],
                'totalExpenses' => $data['totalExpenses'],
                'totalBalance' => $data['totalBalance'],
                'expenseReferences' => $data['expenseReferences'],
                'bookingReferences' => $data['bookingReferences'],
                'tabReferences' => $data['tabReferences'],
                'transferReferences' => $data['transferReferences'],
                'purchaseReferences' => $data['purchaseReferences'],
            ],
            'possible' => [
                'revenue' => $data['possibleRevenue'],
                'expenses' => $data['possibleExpenses'],
                'expenseReferences' => $data['possibleExpenseReferences'],
                'bookingReferences' => $data['possibleBookingReferences'],
                'tabReferences' => $data['possibleTabReferences'],
            ],
            'canceled' => [
                'amount' => $data['canceledAmount'],
                'bookingReferences' => $data['canceledBookingReferences'],
                'tabReferences' => $data['canceledTabReferences'],
                'purchaseReferences' => $data['canceledPurchaseReferences'],
            ],
            'totals' => $data['totals'] ?? [],
        ];
    }
}
