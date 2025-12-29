<?php

namespace App\Services\Api\Company\Booking;

use App\Models\Booking;
use App\Services\Api\Company\Global\BaseService;

class ViewBookingService extends BaseService
{
    public function run(string $id): ?Booking
    {
        $company = $this->getCompany();

        return Booking::whereHas('field', function ($query) use ($company) {
            $query->where('company_id', $company->id);
        })
            ->with([
                'field:id,name,company_id,price_per_hour,extra_hour_price',
                'field.fieldType:id,name',
                'field.fieldSurface:id,name',
                'field.fieldSize:id,name',
                'user:id,name,email,phone',
                'coupon:id,code,discount_amount,discount_type',
                'paymentForm:id,name,type',
            ])
            ->find((int) $id);
    }
}
