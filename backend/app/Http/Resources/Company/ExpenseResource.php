<?php

namespace App\Http\Resources\Company;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'amount' => $this->amount,
            'description' => $this->description,
            'expenseTypeId' => $this->expense_type_id,
            'dueDate' => $this->due_date?->format('d/m/Y'),
            'isPaid' => $this->is_paid,
            'created' => $this->created_at?->format('d/m/Y H:i:s'),

            'expenseType' => $this->whenLoaded('expenseType', function () {
                return [
                    'id' => $this->expenseType->id,
                    'name' => $this->expenseType->name,
                ];
            }),
        ];
    }
}
