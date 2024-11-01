<?php

namespace App\Notifications\Auth;

use App\Notifications\Traits\Notifier;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use UAParser\Exception\FileNotFoundException;
use UAParser\Parser;

class UserActivity extends Notification implements ShouldQueue
{
    use Queueable, Notifier;

    protected $action;

    protected $ip;

    protected $source;

    protected $agent;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($action, $ip, $source, $agent)
    {
        $this->action = $action;
        $this->ip = $ip;
        $this->source = $source;
        $this->agent = $agent;
    }

    /**
     * Replacement Parameters and Values
     *
     *
     * @throws FileNotFoundException
     */
    protected function parameters($notifiable): array
    {
        $location = geoip($this->ip);

        $agent = rescue(function () {
            $parser = Parser::create();
            $result = $parser->parse($this->agent);

            return $result->toString();
        }, 'Unknown');

        return [
            'action' => $this->action,
            'ip' => $this->ip,
            'country' => $location->country,
            'state' => $location->state,
            'source' => $this->source,
            'agent' => $agent,
        ];
    }
}
