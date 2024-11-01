<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\LazyCollection;
use Symfony\Component\Console\Command\Command as CommandAlias;

class UpdateUsersPresence extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:update-presence';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set users which last seen is a long time ago to offline';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->models()->each(function (User $user) {
            if (!$this->checkConnection($user)) {
                $user->updatePresence('offline');
            }
        });

        return CommandAlias::SUCCESS;
    }

    /**
     * Models that should be processed
     */
    protected function models(): LazyCollection
    {
        return User::whereIn('presence', ['online', 'away'])->lazyById();
    }

    /**
     * Check user's connection
     *
     * @return bool|void
     */
    protected function checkConnection(User $user)
    {
        if (Broadcast::getDefaultDriver() === 'pusher') {
            return $this->checkPusherConnection($user);
        }
    }

    /**
     * Get channel info
     */
    protected function checkPusherConnection(User $user): bool
    {
        return rescue(function () use ($user) {
            $pusher = Broadcast::driver()->getPusher();
            $channel = "private-{$user->broadcastChannel()}";
            $info = $pusher->getChannelInfo($channel);

            return (bool) $info->occupied;
        }, false, false);
    }
}
