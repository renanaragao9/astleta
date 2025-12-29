<?php

namespace App\Services\Api\Company\Booking;

use App\Models\Booking;
use App\Services\Api\Company\Global\BaseService;
use App\Traits\Sortable;
use Illuminate\Pagination\LengthAwarePaginator;

class IndexBookingService extends BaseService
{
    use Sortable;

    public function run(array $data): LengthAwarePaginator
    {
        $company = $this->getCompany();
        $search = $data['search'] ?? null;
        $sort = $data['sort'] ?? null;
        $direction = $data['direction'] ?? 'asc';
        $perPage = $data['per_page'] ?? 15;
        $page = $data['page'] ?? 1;
        $bookingStatus = $data['booking_status'] ?? null;
        $bookingDate = $data['booking_date'] ?? null;
        $fieldId = $data['field_id'] ?? null;
        $startDate = $data['start_date'] ?? null;
        $endDate = $data['end_date'] ?? null;
        $paymentType = $data['payment_type'] ?? null;

        $query = Booking::query()
            ->whereHas('field', function ($query) use ($company) {
                $query->where('company_id', $company->id);
            })
            ->with([
                'field:id,name,price_per_hour,extra_hour_price,company_id',
                'user:id,name,email,phone',
                'coupon:id,code,discount_amount,discount_type',
                'paymentForm:id,name,type',
            ])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('booking_number', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        })
                        ->orWhereHas('field', function ($fieldQuery) use ($search) {
                            $fieldQuery->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->when($bookingStatus, function ($query) use ($bookingStatus) {
                $query->where('booking_status', $bookingStatus);
            })
            ->when($bookingDate, function ($query) use ($bookingDate) {
                $query->whereDate('booking_date', $bookingDate);
            })
            ->when($fieldId, function ($query) use ($fieldId) {
                $query->where('field_id', $fieldId);
            })
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('booking_date', [$startDate, $endDate]);
            })
            ->when($paymentType, function ($query) use ($paymentType) {
                $query->where('payment_type', $paymentType);
            });

        $query = $this->applySorting($query, $sort, $direction);

        return $query->paginate($perPage, ['*'], 'page', $page);
    }
}
