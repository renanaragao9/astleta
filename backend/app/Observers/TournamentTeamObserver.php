<?php

namespace App\Observers;

use App\Models\TournamentTeam;
use App\Notifications\MessageNotification;
use App\Notifications\TournamentWelcomeNotification;

class TournamentTeamObserver
{
    public function created(TournamentTeam $tournamentTeam): void
    {
        $user = $tournamentTeam->team->user;
        $user->notify(new TournamentWelcomeNotification($tournamentTeam));

        $message = 'Sua equipe ' . $tournamentTeam->team->name . ' foi inscrita no torneio ' . $tournamentTeam->tournament->name . '.';

        $user->createNotificationMessage(
            get_class($tournamentTeam),
            (new MessageNotification)
                ->message($message)
                ->icon('pi-plus-circle')
                ->toArray()
        );
    }

    public function updated(TournamentTeam $tournamentTeam): void
    {
        //
    }

    public function deleted(TournamentTeam $tournamentTeam): void
    {
        //
    }

    public function restored(TournamentTeam $tournamentTeam): void
    {
        //
    }

    public function forceDeleted(TournamentTeam $tournamentTeam): void
    {
        //
    }
}