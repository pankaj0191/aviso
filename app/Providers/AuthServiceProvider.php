<?php

namespace App\Providers;

use App\Traits\TokenGuard;
use Illuminate\Support\Facades\Auth;
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

        $this->registerApiTokenRepository();

        Auth::viaRequest('jwt', function ($request) {
            return app(TokenGuard::class)->user($request);
        });
        
    }

    /**
     * Register the Api Token repository.
     *
     * @return void
     */
    private function registerApiTokenRepository()
    {
        $concrete = 'App\Services\TokenRepository';

        $this->app->singleton('App\Contracts\TokenRepository', $concrete);
    }
}
