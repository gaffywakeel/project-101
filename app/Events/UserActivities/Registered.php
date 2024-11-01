<?php

namespace App\Events\UserActivities;

use App\Abstracts\UserActivityEvent;

class Registered extends UserActivityEvent
{
    /**
     * User activity action
     */
    protected function action(): string
    {
        return 'registered';
    }
}
