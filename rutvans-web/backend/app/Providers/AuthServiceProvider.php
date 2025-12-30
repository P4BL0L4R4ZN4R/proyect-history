<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Gate para super-admin
        Gate::define('super-admin', function (User $user) {
            return $user->hasRole('super-admin');
        });

        // Gate para gestión de compañías y sitios (solo super-admin)
        Gate::define('manage-companies-and-sites', function (User $user) {
            return $user->hasRole('super-admin');
        });

        // Gate para admin y coordinate
        Gate::define('admin-coordinate', function (User $user) {
            return $user->hasRole('admin') || $user->hasRole('coordinate');
        });

        // Gates individuales por si se necesitan
        Gate::define('admin', function (User $user) {
            return $user->hasRole('admin');
        });

        Gate::define('coordinate', function (User $user) {
            return $user->hasRole('coordinate');
        });

        Gate::define('driver', function (User $user) {
            return $user->hasRole('driver');
        });

        Gate::define('cashier', function (User $user) {
            return $user->hasRole('cashier');
        });

        // Gate para el blog (si es necesario)
        Gate::define('manage-blog', function (User $user) {
            return $user->hasRole('admin') || $user->hasRole('super-admin');
        });

        // Gate para usuarios operativos (drivers y cashiers)
        Gate::define('operational-staff', function (User $user) {
            return $user->hasRole('driver') || $user->hasRole('cashier');
        });

        // Gate para todos los usuarios con roles de gestión (admin, coordinate, super-admin)
        Gate::define('management', function (User $user) {
            return $user->hasRole('admin') || $user->hasRole('coordinate') || $user->hasRole('super-admin');
        });
    }
}
