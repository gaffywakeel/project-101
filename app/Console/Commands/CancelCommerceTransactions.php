<?php

namespace App\Console\Commands;

use App\Models\CommerceTransaction;
use Illuminate\Console\Command;
use Illuminate\Support\LazyCollection;
use Symfony\Component\Console\Command\Command as CommandAlias;

class CancelCommerceTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'commerce-transactions:cancel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancel pending commerce transactions';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->models()->each(function (CommerceTransaction $transaction) {
            rescue(fn () => $this->process($transaction));
        });

        return CommandAlias::SUCCESS;
    }

    /**
     * Cancel commerce transaction
     */
    protected function process(CommerceTransaction $transaction): void
    {
        $transaction->acquireLock(function (CommerceTransaction $transaction) {
            return $transaction->cancel();
        });
    }

    /**
     * Models that should be processed
     */
    protected function models(): LazyCollection
    {
        return CommerceTransaction::isPendingOverdue()->lazyById();
    }
}
