<?php

namespace App\Events\UserActivities;

use App\Abstracts\UserActivityEvent;

class PasswordReset extends UserActivityEvent
{
    /**
     * User activity action
     */
    protected function action(): string
    {
        return 'password reset';
    }
}
