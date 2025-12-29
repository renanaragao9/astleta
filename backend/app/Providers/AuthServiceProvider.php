<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        \App\Models\Sport::class => \App\Policies\SportPolicy::class,
        \App\Models\Statistics::class => \App\Policies\StatisticsPolicy::class,
        \App\Models\Skill::class => \App\Policies\SkillPolicy::class,
        \App\Models\Position::class => \App\Policies\PositionPolicy::class,
        \App\Models\Feature::class => \App\Policies\FeaturePolicy::class,
        \App\Models\PaymentForm::class => \App\Policies\PaymentFormPolicy::class,
        \App\Models\FieldType::class => \App\Policies\FieldTypePolicy::class,
        \App\Models\FieldSurface::class => \App\Policies\FieldSurfacePolicy::class,
        \App\Models\FieldSize::class => \App\Policies\FieldSizePolicy::class,
        \App\Models\FieldItem::class => \App\Policies\FieldItemPolicy::class,
        \App\Models\ContactType::class => \App\Policies\ContactTypePolicy::class,
        \App\Models\DocumentType::class => \App\Policies\DocumentTypePolicy::class,
        \App\Models\ProductType::class => \App\Policies\ProductTypePolicy::class,
        \App\Models\ExpenseType::class => \App\Policies\ExpenseTypePolicy::class,
        \App\Models\TeamType::class => \App\Policies\TeamTypePolicy::class,
        \App\Models\AddressType::class => \App\Policies\AddressTypePolicy::class,
        \App\Models\Contact::class => \App\Policies\ContactPolicy::class,
        \App\Models\Document::class => \App\Policies\DocumentPolicy::class,
        \App\Models\Address::class => \App\Policies\AddressPolicy::class,
        \App\Models\Tag::class => \App\Policies\TagPolicy::class,
        \App\Models\Company::class => \App\Policies\CompanyPolicy::class,
        \App\Models\PreCompaniesRegistration::class => \App\Policies\PreCompaniesRegistrationPolicy::class,
        \App\Models\Field::class => \App\Policies\FieldPolicy::class,
        \App\Models\FieldSchedule::class => \App\Policies\FieldSchedulePolicy::class,
        \App\Models\FieldImage::class => \App\Policies\FieldImagePolicy::class,
        \App\Models\Product::class => \App\Policies\ProductPolicy::class,
        \App\Models\Expense::class => \App\Policies\ExpensePolicy::class,
        \App\Models\Team::class => \App\Policies\TeamPolicy::class,
        \App\Models\TeamPlayer::class => \App\Policies\TeamPlayerPolicy::class,
        \App\Models\Coupon::class => \App\Policies\CouponPolicy::class,
        \App\Models\Booking::class => \App\Policies\BookingPolicy::class,
        \App\Models\BookingParticipant::class => \App\Policies\BookingParticipantPolicy::class,
        \App\Models\Tab::class => \App\Policies\TabPolicy::class,
        \App\Models\TabItem::class => \App\Policies\TabItemPolicy::class,
        \App\Models\AthleteProfile::class => \App\Policies\AthleteProfilePolicy::class,
        \App\Models\PlayerRating::class => \App\Policies\PlayerRatingPolicy::class,
        \App\Models\PlayerStatistic::class => \App\Policies\PlayerStatisticPolicy::class,
        \App\Models\StatisticsTeam::class => \App\Policies\StatisticsTeamPolicy::class,
        \App\Models\TeamBooking::class => \App\Policies\TeamBookingPolicy::class,
        \App\Models\TeamStatisticsBooking::class => \App\Policies\TeamStatisticsBookingPolicy::class,
        \App\Models\MarketingType::class => \App\Policies\MarketingTypePolicy::class,
        \App\Models\Marketing::class => \App\Policies\MarketingPolicy::class,
        \App\Models\Receipt::class => \App\Policies\ReceiptPolicy::class,
        \App\Models\Warehouse::class => \App\Policies\WarehousePolicy::class,
        \App\Models\Stock::class => \App\Policies\StockPolicy::class,
        \App\Models\Supplier::class => \App\Policies\SupplierPolicy::class,
        \App\Models\Purchase::class => \App\Policies\PurchasePolicy::class,
        \App\Models\StockMovement::class => \App\Policies\StockMovementPolicy::class,
        \App\Models\Tournament::class => \App\Policies\TournamentPolicy::class,
        \App\Models\TournamentTeam::class => \App\Policies\TournamentTeamPolicy::class,
    ];

    public function boot(): void
    {
        //
    }
}
