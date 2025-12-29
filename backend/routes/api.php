<?php

use App\Http\Controllers\Api\Athlete\AthleteProfileController;
use App\Http\Controllers\Api\Athlete\AthleteRachaController;
use App\Http\Controllers\Api\Athlete\Booking\BookingAthleteController;
use App\Http\Controllers\Api\Athlete\Booking\BookingParticipantController;
use App\Http\Controllers\Api\Athlete\Booking\BookingRatingController;
use App\Http\Controllers\Api\Athlete\Booking\BookingStatisticController;
use App\Http\Controllers\Api\Athlete\Booking\BookingTeamController;
use App\Http\Controllers\Api\Athlete\Select\FeaturesController;
use App\Http\Controllers\Api\Athlete\Select\PositionController;
use App\Http\Controllers\Api\Athlete\Select\SkillController;
use App\Http\Controllers\Api\Athlete\Select\SportController;
use App\Http\Controllers\Api\Athlete\Select\StatisticsController;
use App\Http\Controllers\Api\Athlete\Select\TeamSelectController;
use App\Http\Controllers\Api\Athlete\Select\TeamTypesController;
use App\Http\Controllers\Api\Athlete\TabAthleteController;
use App\Http\Controllers\Api\Athlete\Team\TeamController;
use App\Http\Controllers\Api\Athlete\Team\TeamPlayerController;
use App\Http\Controllers\Api\Athlete\Team\TeamStatisticsBookingController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\PasswordResetController;
use App\Http\Controllers\Api\Company\BookingController;
use App\Http\Controllers\Api\Company\CompanyController;
use App\Http\Controllers\Api\Company\ExpenseController;
use App\Http\Controllers\Api\Company\FieldController;
use App\Http\Controllers\Api\Company\FinancialController;
use App\Http\Controllers\Api\Company\ProductController;
use App\Http\Controllers\Api\Company\PurchaseController;
use App\Http\Controllers\Api\Company\StockController;
use App\Http\Controllers\Api\Company\SupplierController;
use App\Http\Controllers\Api\Company\WarehouseController;
use App\Http\Controllers\Api\Company\Select\ExpenseTypeController;
use App\Http\Controllers\Api\Company\Select\FieldItemController;
use App\Http\Controllers\Api\Company\Select\FieldSizeController;
use App\Http\Controllers\Api\Company\Select\FieldSurfaceController;
use App\Http\Controllers\Api\Company\Select\FieldTypeController;
use App\Http\Controllers\Api\Company\Select\PaymentFormController;
use App\Http\Controllers\Api\Company\Select\ProductTypeController;
use App\Http\Controllers\Api\Company\TabController;
use App\Http\Controllers\Api\Company\TabItemController;
use App\Http\Controllers\Api\Company\TournamentController;
use App\Http\Controllers\Api\Company\TournamentTeamController;
use App\Http\Controllers\Api\Global\NotificationMessageController;
use App\Http\Controllers\Api\Global\PreCompaniesRegistrationController;
use App\Http\Controllers\Api\Global\PublicAthleteProfileController;
use App\Http\Controllers\Api\Public\PublicBookingController;
use App\Http\Controllers\Api\Public\PublicFieldController;
use App\Http\Controllers\Api\Public\PublicMarketingController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function (): void {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('verify-email', [AuthController::class, 'verifyEmail']);
    Route::post('resend-verification-code', [AuthController::class, 'resendVerificationCode']);
    Route::post('forgot-password', [PasswordResetController::class, 'forgotPassword']);
    Route::post('reset-password', [PasswordResetController::class, 'resetPassword']);
});

Route::prefix('public_field')->group(function (): void {
    Route::get('fields', [PublicFieldController::class, 'index']);
    Route::get('fields/{field}', [PublicFieldController::class, 'show']);
    Route::get('companies/{company}', [PublicFieldController::class, 'showCompanyProfile']);
});

Route::prefix('public_marketing')->group(function (): void {
    Route::get('marketing', [PublicMarketingController::class, 'index']);
});

Route::post('pre-companies-registration', [PreCompaniesRegistrationController::class, 'store']);

Route::middleware('auth:sanctum')->group(function (): void {
    Route::get('me', [AuthController::class, 'me']);

    Route::prefix('auth')->group(function (): void {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('logout-all-devices', [AuthController::class, 'logoutFromAllDevices']);
    });

    Route::prefix('public_booking')->group(function (): void {
        Route::get('availability', [PublicBookingController::class, 'availability']);
        Route::post('calculate-price', [PublicBookingController::class, 'calculatePrice']);
        Route::post('bookings', [PublicBookingController::class, 'store']);
    });

    Route::prefix('baa')->group(function (): void {
        Route::get('athletes', [PublicAthleteProfileController::class, 'index']);
    });

    Route::prefix('company')->group(function (): void {
        Route::post('company/update-image', [CompanyController::class, 'updateImage']);
        Route::get('company', [CompanyController::class, 'show']);
        Route::post('fields/{field}/update-image', [FieldController::class, 'updateImage']);
        Route::apiResource('fields', FieldController::class);

        Route::get('bookings/availability', [BookingController::class, 'availability']);
        Route::post('bookings/calculate-price', [BookingController::class, 'calculatePrice']);
        Route::patch('bookings/{booking}/status', [BookingController::class, 'updateStatus']);
        Route::post('bookings/send', [BookingController::class, 'send']);
        Route::get('bookings/by-month', [BookingController::class, 'getByMonth']);
        Route::apiResource('bookings', BookingController::class);

        Route::get('products/select', [ProductController::class, 'selectProducts']);
        Route::apiResource('products', ProductController::class);
        Route::post('tabs/send', [TabController::class, 'send']);
        Route::apiResource('tabs', TabController::class);
        Route::apiResource('tab-items', TabItemController::class);
        Route::apiResource('expenses', ExpenseController::class);

        Route::get('warehouses-select', [WarehouseController::class, 'select']);
        Route::apiResource('warehouses', WarehouseController::class);
        Route::get('suppliers-select', [SupplierController::class, 'select']);
        Route::apiResource('suppliers', SupplierController::class);

        Route::apiResource('stocks', StockController::class);
        Route::apiResource('purchases', PurchaseController::class);
        Route::apiResource('tournaments', TournamentController::class);
        Route::apiResource('tournament-teams', TournamentTeamController::class);

        Route::apiResource('financial', FinancialController::class);

        Route::get('field-types', [FieldTypeController::class, 'index']);
        Route::get('field-surfaces', [FieldSurfaceController::class, 'index']);
        Route::get('field-sizes', [FieldSizeController::class, 'index']);
        Route::get('field-items', [FieldItemController::class, 'index']);
        Route::get('product-types', [ProductTypeController::class, 'index']);
        Route::get('expense-types', [ExpenseTypeController::class, 'index']);
        Route::get('payment-forms', [PaymentFormController::class, 'index']);
    });

    Route::prefix('athlete')->group(function (): void {
        Route::get('profile', [AthleteProfileController::class, 'show']);
        Route::post('profile', [AthleteProfileController::class, 'store']);
        Route::put('profile', [AthleteProfileController::class, 'update']);
        Route::patch('profile/user', [AthleteProfileController::class, 'updateUser']);
        Route::post('update-image', [AthleteProfileController::class, 'updateImage']);

        Route::get('rachas', [AthleteRachaController::class, 'index']);
        Route::post('rachas/join', [AthleteRachaController::class, 'join']);

        Route::get('tabs', [TabAthleteController::class, 'index']);
        Route::get('tabs/{tab}', [TabAthleteController::class, 'show']);
        Route::get('tabs/{tab}/receipt', [TabAthleteController::class, 'downloadReceipt']);

        Route::get('bookings', [BookingAthleteController::class, 'index']);
        Route::get('bookings/{booking}', [BookingAthleteController::class, 'show']);
        Route::get('bookings/{booking}/receipt', [BookingAthleteController::class, 'downloadReceipt']);

        Route::get('bookings/{booking}/participants', [BookingParticipantController::class, 'index']);
        Route::post('bookings/{booking}/participants', [BookingParticipantController::class, 'store']);
        Route::put('bookings/{booking}/participants/{booking_participant}', [BookingParticipantController::class, 'update']);
        Route::delete('bookings/{booking}/participants/{booking_participant}', [BookingParticipantController::class, 'destroy']);

        Route::get('bookings/{booking}/statistics', [BookingStatisticController::class, 'index']);
        Route::post('bookings/{booking}/statistics', [BookingStatisticController::class, 'store']);
        Route::put('bookings/{booking}/statistics/{player_statistic}', [BookingStatisticController::class, 'update']);
        Route::delete('bookings/{booking}/statistics/{player_statistic}', [BookingStatisticController::class, 'destroy']);

        Route::get('bookings/{booking}/ratings', [BookingRatingController::class, 'index']);
        Route::post('bookings/{booking}/ratings', [BookingRatingController::class, 'store']);
        Route::put('bookings/{booking}/ratings/{player_rating}', [BookingRatingController::class, 'update']);
        Route::delete('bookings/{booking}/ratings/{player_rating}', [BookingRatingController::class, 'destroy']);

        Route::get('bookings/{booking}/team-booking', [BookingTeamController::class, 'show']);
        Route::post('bookings/{booking}/team-booking', [BookingTeamController::class, 'store']);
        Route::put('bookings/{booking}/team-booking', [BookingTeamController::class, 'update']);
        Route::delete('bookings/{booking}/team-booking', [BookingTeamController::class, 'destroy']);

        Route::get('team-bookings/{team_booking}/statistics', [TeamStatisticsBookingController::class, 'index']);
        Route::post('team-bookings/{team_booking}/statistics', [TeamStatisticsBookingController::class, 'store']);
        Route::put('team-bookings/{team_booking}/statistics/{team_statistics_booking}', [TeamStatisticsBookingController::class, 'update']);
        Route::delete('team-bookings/{team_booking}/statistics/{team_statistics_booking}', [TeamStatisticsBookingController::class, 'destroy']);

        Route::get('teams/me', [TeamController::class, 'show']);
        Route::get('teams', [TeamController::class, 'index']);
        Route::post('teams', [TeamController::class, 'store']);
        Route::put('teams/{team}', [TeamController::class, 'update'])->where('team', '[0-9]+');
        Route::delete('teams/{team}', [TeamController::class, 'destroy'])->where('team', '[0-9]+');
        Route::post('teams/{team}/image', [TeamController::class, 'updateImage'])->where('team', '[0-9]+');
        Route::get('teams/{team}/statistics', [TeamController::class, 'statistics'])->where('team', '[0-9]+');
        Route::get('teams/{team}/departures', [TeamController::class, 'departures'])->where('team', '[0-9]+');

        Route::get('teams/{team}/players', [TeamPlayerController::class, 'index']);
        Route::post('teams/{team}/players', [TeamPlayerController::class, 'store']);
        Route::put('teams/{team}/players/{team_player}', [TeamPlayerController::class, 'update']);
        Route::delete('teams/{team}/players/{team_player}', [TeamPlayerController::class, 'destroy']);
        Route::delete('teams/{team}/players', [TeamPlayerController::class, 'leave']);

        Route::get('sports', [SportController::class, 'index']);
        Route::get('skills', [SkillController::class, 'index']);
        Route::get('positions', [PositionController::class, 'index']);
        Route::get('team-types', [TeamTypesController::class, 'index']);
        Route::get('teams-available', [TeamSelectController::class, 'index']);
        Route::get('features', [FeaturesController::class, 'index']);
        Route::get('statistics', [StatisticsController::class, 'index']);
        Route::get('statistics/sport/{sportId}', [StatisticsController::class, 'getBySport']);
    });

    Route::prefix('notification-messages')->name('notification-messages.')->group(function () {
        Route::get('/', [NotificationMessageController::class, 'index']);
        Route::post('/{notification_message}/read', [NotificationMessageController::class, 'markAsRead']);
        Route::post('/{notification_message}/unread', [NotificationMessageController::class, 'markAsUnread']);
        Route::post('/mark-all-read', [NotificationMessageController::class, 'markAllAsRead']);
        Route::delete('/{notification_message}', [NotificationMessageController::class, 'destroy']);
        Route::post('/delete-all', [NotificationMessageController::class, 'deleteAll']);
    });
});
