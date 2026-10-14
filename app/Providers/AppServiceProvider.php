<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register custom Blade directive for checking roles: @role('admin') ... @endrole
        Blade::if('role', function ($role) {
            return auth()->check() && auth()->user()->hasRole($role);
        });

        // Register custom Blade directive for checking permissions: @permission('create-product') ... @endpermission
        Blade::if('permission', function ($permission) {
            return auth()->check() && auth()->user()->hasPermissionTo($permission);
        });

        // Implicitly grant abilities via Gate so standard @can(...) checks also work
        Gate::before(function (User $user, string $ability) {
            if ($user->hasPermissionTo($ability)) {
                return true;
            }
        });
    }
}
