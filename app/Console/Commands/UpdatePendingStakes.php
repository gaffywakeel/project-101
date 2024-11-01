<?php

namespace App\Console\Commands;

use App\Models\Stake;
use Illuminate\Console\Command;
use Illuminate\Support\LazyCollection;
use Symfony\Component\Console\Command\Command as CommandAlias;

class UpdatePendingStakes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stakes:update-pending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update stake subscription to pending when due';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->models()->each(function (Stake $stake) {
            rescue(fn () => $this->process($stake));
        });

        return CommandAlias::SUCCESS;
    }

    /**
     * Models that should be processed
     */
    protected function models(): LazyCollection
    {
        return Stake::whereStatus('holding')->oldest()->lazyById();
    }

    /**
     * Process Stake
     */
    protected function process(Stake $stake): void
    {
        $stake->acquireLock(function (Stake $stake) {
            if ($stake->redemption_date->isBefore(now())) {
                $stake->update(['status' => 'pending']);
            }
        });
    }
}
