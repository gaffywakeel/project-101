<?php

namespace App\Providers;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rules\Password;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->enableImplicitPermissionGrant();
        $this->passwordValidation();
    }

    /**
     * Set Password validation rules
     */
    protected function passwordValidation(): void
    {
        Password::defaults(function () {
            return Password::min(8)
                ->letters()
                ->numbers()
                ->symbols();
        });
    }

    /**
     * Enable implicit permission grant for "Administrator" role
     */
    protected function enableImplicitPermissionGrant(): void
    {
        Gate::after(function (User $user, string $ability): ?bool {
            if (Permission::whereName($ability)->exists()) {
                return $user->hasRole(Role::administrator());
            }

            return null;
        });
    }
}
