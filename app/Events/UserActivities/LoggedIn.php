<?php

namespace App\Events\UserActivities;

use App\Abstracts\UserActivityEvent;

class LoggedIn extends UserActivityEvent
{
    /**
     * User activity action
     */
    protected function action(): string
    {
        return 'logged in';
    }
}
