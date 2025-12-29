<?php

namespace App\Providers;

use App\Models\Booking;
use App\Models\TeamPlayer;
use App\Models\TournamentTeam;
use App\Observers\BookingObserver;
use App\Observers\TeamPlayerObserver;
use App\Observers\TournamentTeamObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Booking::observe(BookingObserver::class);
        TeamPlayer::observe(TeamPlayerObserver::class);
        TournamentTeam::observe(TournamentTeamObserver::class);
    }
}
