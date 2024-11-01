<?php

namespace App\Events\UserActivities;

use App\Abstracts\UserActivityEvent;

class UpdatedPreference extends UserActivityEvent
{
    /**
     * User activity action
     */
    protected function action(): string
    {
        return 'updated preference';
    }
}
