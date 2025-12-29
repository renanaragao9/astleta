<?php

namespace App\Services\Api\Company\Booking;

use App\Models\Booking;
use App\Services\Api\Company\Global\BaseService;
use Illuminate\Database\Eloquent\Collection;

class GetByMonthBookingService extends BaseService
{
    public function run(array $data): Collection
    {
        $company = $this->getCompany();

        $year = $data['year'] ?? now()->year;
        $month = $data['month'] ?? now()->month;

        $bookings = Booking::query()
            ->with(['field', 'user', 'coupon', 'paymentForm'])
            ->whereYear('booking_date', $year)
            ->whereMonth('booking_date', $month)
            ->whereHas('field', function ($query) use ($company) {
                $query->where('company_id', $company->id);
            })
            ->orderBy('booking_date')
            ->orderBy('start_time')
            ->get();

        return $bookings;
    }
}
