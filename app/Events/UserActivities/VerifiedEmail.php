<?php

namespace App\Events\UserActivities;

use App\Abstracts\UserActivityEvent;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class VerifiedEmail extends UserActivityEvent implements ShouldBroadcast
{
    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [new PrivateChannel("App.Models.User.{$this->user->id}")];
    }

    /**
     * User activity action
     */
    protected function action(): string
    {
        return 'verified email';
    }
}
