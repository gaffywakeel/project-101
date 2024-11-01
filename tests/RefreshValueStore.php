<?php

namespace Tests;

use Illuminate\Support\Str;
use Spatie\Valuestore\Valuestore as ValueStore;

trait RefreshValueStore
{
    /**
     * SetUp RefreshValueStore
     */
    public function setUpRefreshValueStore(): void
    {
        $store = ValueStore::make('/tmp/' . Str::uuid() . '.test.json');

        $this->originalValueStore = $this->app->make(ValueStore::class);
        $this->instance(ValueStore::class, $store);
    }

    /**
     * TearDown RefreshValueStore
     */
    public function tearDownRefreshValueStore(): void
    {
        $this->app->make(ValueStore::class)->flush();
    }
}
