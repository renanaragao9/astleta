<?php

namespace App\Nova\Filters\Booking;

use App\Models\Field;
use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class BookingFieldFilter extends Filter
{
    public function apply(Request $request, $query, $value)
    {
        return $query->where('bookings.field_id', $value);
    }

    public function options(Request $request): array
    {
        $fields = Field::select('id', 'name')->orderBy('name')->get();

        $options = [];
        foreach ($fields as $field) {
            $options[$field->name] = $field->id;
        }

        return $options;
    }

    public function name(): string
    {
        return 'Campo/Arena';
    }
}
