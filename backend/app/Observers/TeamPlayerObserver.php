<?php

namespace App\Observers;

use App\Models\TeamPlayer;
use App\Notifications\MessageNotification;
use App\Notifications\TeamPlayerWelcomeNotification;

class TeamPlayerObserver
{
    public function created(TeamPlayer $teamPlayer): void
    {
        $user = $teamPlayer->user;
        $user->notify(new TeamPlayerWelcomeNotification($teamPlayer));

        $message = 'Você foi adicionado ao time ' . $teamPlayer->team->name . '.';

        $user->createNotificationMessage(
            get_class($teamPlayer),
            (new MessageNotification)
                ->message($message)
                ->icon('pi-plus-circle')
                ->toArray()
        );
    }

    public function updated(TeamPlayer $teamPlayer): void
    {
        //
    }

    public function deleted(TeamPlayer $teamPlayer): void
    {
        //
    }

    public function restored(TeamPlayer $teamPlayer): void
    {
        //
    }

    public function forceDeleted(TeamPlayer $teamPlayer): void
    {
        //
    }
}