<?php

namespace App\Events\UserActivities;

use App\Abstracts\UserActivityEvent;

class UpdatedPicture extends UserActivityEvent
{
    /**
     * User activity action
     */
    protected function action(): string
    {
        return 'changed picture';
    }
}
