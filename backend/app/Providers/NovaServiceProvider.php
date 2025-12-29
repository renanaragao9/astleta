<?php

namespace App\Providers;

use App\Nova\Address;
use App\Nova\AddressType;
use App\Nova\AthleteProfile;
use App\Nova\Booking;
use App\Nova\BookingParticipant;
use App\Nova\Company;
use App\Nova\Contact;
use App\Nova\ContactType;
use App\Nova\Coupon;
use App\Nova\Document;
use App\Nova\DocumentType;
use App\Nova\Expense;
use App\Nova\ExpenseType;
use App\Nova\Feature;
use App\Nova\Field;
use App\Nova\FieldImage;
use App\Nova\FieldItem;
use App\Nova\FieldSchedule;
use App\Nova\FieldSize;
use App\Nova\FieldSurface;
use App\Nova\FieldType;
use App\Nova\Marketing;
use App\Nova\MarketingType;
use App\Nova\PaymentForm;
use App\Nova\Permission;
use App\Nova\PlayerRating;
use App\Nova\PlayerStatistic;
use App\Nova\Position;
use App\Nova\PreCompaniesRegistration;
use App\Nova\Product;
use App\Nova\ProductType;
use App\Nova\Profile;
use App\Nova\Receipt;
use App\Nova\Skill;
use App\Nova\Sport;
use App\Nova\Statistics;
use App\Nova\StatisticsTeam;
use App\Nova\Tab;
use App\Nova\TabItem;
use App\Nova\Tag;
use App\Nova\Team;
use App\Nova\TeamBooking;
use App\Nova\TeamPlayer;
use App\Nova\TeamStatisticsBooking;
use App\Nova\TeamType;
use App\Nova\User;
use App\Nova\Warehouse;
use App\Nova\Stock;
use App\Nova\Supplier;
use App\Nova\Purchase;
use App\Nova\StockMovement;
use App\Nova\Tournament;
use App\Nova\TournamentTeam;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Laravel\Nova\Menu\MenuItem;
use Laravel\Nova\Menu\MenuSection;
use Laravel\Nova\Nova;
use Laravel\Nova\NovaApplicationServiceProvider;

class NovaServiceProvider extends NovaApplicationServiceProvider
{
    public function boot()
    {
        parent::boot();

        Nova::footer(function ($request) {
            $year = date('Y');

            return Blade::render("<center> © Direitos reservados a <a> Astleta </a> - Painel Admin {$year} </center>");
        });

        Nova::mainMenu(function () {
            return [
                MenuSection::make('Autenticação', [
                    MenuItem::resource(Profile::class),
                    MenuItem::resource(Permission::class),
                    MenuItem::resource(User::class),
                ], 'user')->collapsable(),

                MenuSection::make('Dados', [
                    MenuItem::resource(Contact::class),
                    MenuItem::resource(Document::class),
                    MenuItem::resource(Company::class),
                    MenuItem::resource(Address::class),
                    MenuItem::resource(Team::class),
                    MenuItem::resource(Marketing::class),
                    MenuItem::resource(PreCompaniesRegistration::class),
                    MenuItem::resource(Receipt::class),
                ], 'database')->collapsable(),

                MenuSection::make('Auditoria', [
                    MenuItem::resource(Warehouse::class),
                    MenuItem::resource(PlayerRating::class),
                    MenuItem::resource(Field::class),
                    MenuItem::resource(Tab::class),
                    MenuItem::resource(Purchase::class),
                    MenuItem::resource(Coupon::class),
                    MenuItem::resource(Expense::class),
                    MenuItem::resource(TournamentTeam::class),
                    MenuItem::resource(PlayerStatistic::class),
                    MenuItem::resource(TeamStatisticsBooking::class),
                    MenuItem::resource(Stock::class),
                    MenuItem::resource(Supplier::class),
                    MenuItem::resource(FieldSchedule::class),
                    MenuItem::resource(TabItem::class),
                    MenuItem::resource(TeamPlayer::class),
                    MenuItem::resource(StockMovement::class),
                    MenuItem::resource(BookingParticipant::class),
                    MenuItem::resource(AthleteProfile::class),
                    MenuItem::resource(Product::class),
                    MenuItem::resource(Booking::class),
                    MenuItem::resource(TeamBooking::class),
                    MenuItem::resource(Tournament::class),
                ], 'clipboard-list')->collapsable(),

                MenuSection::make('Configurações', [
                    MenuItem::resource(Feature::class),
                    MenuItem::resource(Sport::class),
                    MenuItem::resource(Statistics::class),
                    MenuItem::resource(StatisticsTeam::class),
                    MenuItem::resource(PaymentForm::class),
                    MenuItem::resource(Skill::class),
                    MenuItem::resource(FieldImage::class),
                    MenuItem::resource(FieldItem::class),
                    MenuItem::resource(Position::class),
                    MenuItem::resource(FieldSurface::class),
                    MenuItem::resource(FieldSize::class),
                    MenuItem::resource(Tag::class),
                    MenuItem::resource(FieldType::class),
                    MenuItem::resource(ContactType::class),
                    MenuItem::resource(ExpenseType::class),
                    MenuItem::resource(DocumentType::class),
                    MenuItem::resource(AddressType::class),
                    MenuItem::resource(TeamType::class),
                    MenuItem::resource(MarketingType::class),
                    MenuItem::resource(ProductType::class),
                ], 'cog')->collapsable(),
            ];
        });
    }

    protected function routes(): void
    {
        Nova::routes()
            ->withAuthenticationRoutes()
            ->withPasswordResetRoutes()
            ->register();
    }

    protected function gate(): void
    {
        Gate::define('viewNova', function ($user) {
            return true;
        });
    }

    protected function dashboards(): array
    {
        return [
            new \App\Nova\Dashboards\Main,
        ];
    }

    public function tools(): array
    {
        return [];
    }

    public function register(): void
    {
        parent::register();
    }
}
