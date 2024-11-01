<?php

namespace App\Jobs;

use App\Models\PaymentTransaction;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class ProcessPaymentTransaction implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Pending transaction
     */
    protected PaymentTransaction $transaction;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 1;

    /**
     * The number of seconds the job can run before timing out.
     */
    public int $timeout = 120;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(PaymentTransaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Execute the job.
     *
     *
     * @throws Exception
     */
    public function handle(): void
    {
        if ($this->transaction->type === 'receive') {
            if ($this->transaction->isPendingOverdue()) {
                $this->transaction->cancelPending();
            } elseif ($this->transaction->isPendingGateway()) {
                $this->transaction->completeGateway();
            }
        }
    }

    /**
     * The unique ID of the job.
     */
    public function uniqueId(): string
    {
        return $this->transaction->id;
    }

    /**
     * Get the cache driver for the unique job lock.
     */
    public function uniqueVia(): Repository
    {
        return app()->isProduction() ? Cache::store('redis') : Cache::store();
    }
}
