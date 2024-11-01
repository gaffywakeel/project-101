<?php

namespace App\Listeners;

use App\Abstracts\UserActivityEvent;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogUserActivity implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(UserActivityEvent $event): void
    {
        $event->getUser()->log(
            $event->action,
            $event->ip,
            $event->source,
            $event->agent
        );
    }
}
