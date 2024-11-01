<?php

namespace App\Events\UserActivities;

use App\Abstracts\UserActivityEvent;

class PasswordChanged extends UserActivityEvent
{
    /**
     * User activity action
     */
    protected function action(): string
    {
        return 'password changed';
    }
}
