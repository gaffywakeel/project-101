<?php

namespace App\Abstracts;

use Illuminate\Support\ServiceProvider;

abstract class CoinServiceProvider extends ServiceProvider
{
    /**
     * Plugin name for resource bindings
     */
    protected string $name;

    /**
     * Adapters to be registered
     */
    protected array $adapters;

    /**
     * Register services.
     */
    public function register(): void
    {
        $this->registerConfig();
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->publishAssets();

        collect($this->adapters)->each(function ($adapter) {
            app('coin.manager')->addAdapter($adapter);
        });
    }

    /**
     * Where to find plugin assets
     */
    abstract protected function resourcePath(): string;

    /**
     * Publish assets
     */
    protected function publishAssets(): void
    {
        if (file_exists($path = rtrim($this->resourcePath(), '/') . '/assets')) {
            $this->publishes([$path => public_path("coin/$this->name")], 'coin-assets');
        }
    }

    /**
     * Register resources
     */
    protected function registerConfig(): void
    {
        if (file_exists($path = rtrim($this->resourcePath(), '/') . '/config.php')) {
            $this->mergeConfigFrom($path, $this->name);
        }
    }
}
