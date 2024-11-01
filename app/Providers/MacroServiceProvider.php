<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;

class MacroServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->setRedirectMacros();
        $this->setBuilderMacros();
    }

    /**
     * Bind redirect macros
     */
    protected function setRedirectMacros(): void
    {
        RedirectResponse::macro('notify', function ($message, $type = 'info'): RedirectResponse {
            return $this->with('notification', compact('type', 'message'));
        });
    }

    /**
     * Bind builder macros
     */
    protected function setBuilderMacros(): void
    {
        Builder::macro('autoPaginate', function () {
            return paginate($this, Request::instance());
        });

        Relation::macro('autoPaginate', function () {
            return paginate($this, Request::instance());
        });
    }
}
