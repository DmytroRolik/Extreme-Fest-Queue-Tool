<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        $this->registerGates();
    }

    public function registerGates(){

        // Активности
        Gate::define('activities-view', function ($user) {
            return $user->role_id == 1 || in_array(1, $user->permissionsIds());
        });
        Gate::define('activities-edit', function ($user) {
            return $user->role_id == 1 || in_array(2, $user->permissionsIds());
        });

        // Расписание
        Gate::define('schedule-view', function ($user) {
            return $user->role_id == 1 || in_array(3, $user->permissionsIds());
        });
        Gate::define('schedule-edit', function ($user) {
            return $user->role_id == 1 || in_array(4, $user->permissionsIds());
        });

        // Аккаунт
        Gate::define('accounts-view', function ($user) {
            return $user->role_id == 1 || in_array(5, $user->permissionsIds());
        });
        Gate::define('accounts-edit', function ($user) {
            return $user->role_id == 1 || in_array(6, $user->permissionsIds());
        });

        // Пользователи
        Gate::define('users-view', function ($user) {
            return $user->role_id == 1 || in_array(7, $user->permissionsIds());
        });
        Gate::define('users-edit', function ($user) {
            return $user->role_id == 1 || in_array(8, $user->permissionsIds());
        });

        // Очередь
        Gate::define('queue-view', function ($user) {
            return $user->role_id == 1 || in_array(9, $user->permissionsIds());
        });
        Gate::define('queue-edit', function ($user) {
            return $user->role_id == 1 || in_array(10, $user->permissionsIds());
        });
    }
}
