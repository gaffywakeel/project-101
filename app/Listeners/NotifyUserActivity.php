<?php

namespace App\Listeners;

use App\Abstracts\UserActivityEvent;
use App\Notifications\Auth\UserActivity;

class NotifyUserActivity
{
    /**
     * Handle the event.
     */
    public function handle(UserActivityEvent $event): void
    {
        $event->getUser()->notify(new UserActivity(
            $event->action,
            $event->ip,
            $event->source,
            $event->agent
        ));
    }
}
