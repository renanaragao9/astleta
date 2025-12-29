<?php

namespace App\Nova\Filters\Tab;

use App\Models\PaymentForm;
use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class TabPaymentFormFilter extends Filter
{
    public function apply(Request $request, $query, $value)
    {
        return $query->where('tabs.payment_form_id', $value);
    }

    public function options(Request $request): array
    {
        $paymentForms = PaymentForm::select('id', 'name')->orderBy('name')->get();

        $options = [];
        foreach ($paymentForms as $paymentForm) {
            $options[$paymentForm->name] = $paymentForm->id;
        }

        return $options;
    }

    public function name(): string
    {
        return 'Forma de Pagamento';
    }
}
