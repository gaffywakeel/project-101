<?php

namespace App\Events;

use App\Models\ExchangeTrade;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ExchangeTradeSaved implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected ExchangeTrade $exchangeTrade;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(ExchangeTrade $exchangeTrade)
    {
        $this->exchangeTrade = $exchangeTrade;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel("App.Models.User.{$this->exchangeTrade->walletAccount->user->id}");
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'status' => $this->exchangeTrade->status,
            'completed_at' => $this->exchangeTrade->completed_at,
        ];
    }
}
