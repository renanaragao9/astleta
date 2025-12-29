<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends BaseModel
{
    protected $fillable = [
        'name',
        'type',
        'amount',
        'description',
        'expense_type_id',
        'company_id',
        'due_date',
        'is_paid',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
        'is_paid' => 'boolean',
    ];

    public function expenseType(): BelongsTo
    {
        return $this->belongsTo(ExpenseType::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function getFormattedAmountAttribute(): string
    {
        return 'R$ '.number_format((float) $this->amount, 2, ',', '.');
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->type === 'entrada' ? 'Entrada' : 'Saída';
    }

    public function scopePaid($query)
    {
        return $query->where('is_paid', true);
    }

    public function scopeUnpaid($query)
    {
        return $query->where('is_paid', false);
    }

    public function scopeIncome($query)
    {
        return $query->where('type', 'entrada');
    }

    public function scopeExpense($query)
    {
        return $query->where('type', 'saida');
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())->where('is_paid', false);
    }
}
