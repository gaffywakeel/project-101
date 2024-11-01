<?php

namespace App\Events\UserActivities;

use App\Abstracts\UserActivityEvent;

class UpdatedProfile extends UserActivityEvent
{
    /**
     * User activity action
     */
    protected function action(): string
    {
        return 'updated profile';
    }
}
