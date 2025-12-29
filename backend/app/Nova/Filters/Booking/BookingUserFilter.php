<?php

namespace App\Nova\Filters\Booking;

use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class BookingUserFilter extends Filter
{
    public function apply(Request $request, $query, $value)
    {
        return $query->where('bookings.user_id', $value);
    }

    public function options(Request $request): array
    {
        $users = User::select('id', 'name', 'username')->orderBy('name')->get();

        $options = [];
        foreach ($users as $user) {
            $options[$user->name.' ('.$user->username.')'] = $user->id;
        }

        return $options;
    }

    public function name(): string
    {
        return 'Usuário';
    }
}
