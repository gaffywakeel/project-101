<?php

namespace App\Events;

use App\Models\WalletAccount;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WalletAccountSaved implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected WalletAccount $walletAccount;

    /**
     * Create a new event instance.
     */
    public function __construct(WalletAccount $walletAccount)
    {
        $this->walletAccount = $walletAccount;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel("App.Models.User.{$this->walletAccount->user->id}");
    }
}
