<?php

namespace App\Providers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class PluginServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $plugins = $this->getPlugins();

        foreach ($plugins as $path) {
            if (class_exists($class = $this->getProvider($path))) {
                $this->app->register($class);
            }
        }
    }

    /**
     * Get plugins
     */
    protected function getPlugins(): array
    {
        return File::directories(app_path('Plugins'));
    }

    /**
     * Get plugin provider
     */
    protected function getProvider($path): string
    {
        return 'App\\Plugins\\' . basename($path) . '\\PluginServiceProvider';
    }
}
