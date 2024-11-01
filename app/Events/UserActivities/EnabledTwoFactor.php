<?php

namespace App\Events\UserActivities;

use App\Abstracts\UserActivityEvent;

class EnabledTwoFactor extends UserActivityEvent
{
    /**
     * User activity action
     */
    protected function action(): string
    {
        return 'enabled two factor';
    }
}
