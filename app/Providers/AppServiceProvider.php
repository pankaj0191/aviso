<?php

namespace App\Providers;

use App\Project;
use Laravel\Cashier\Cashier;
use App\Observers\ProjectObserve;
use Illuminate\Support\Facades\Queue;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use App\Providers\TelescopeServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Project::observe(ProjectObserve::class);
        Schema::defaultStringLength(191);
        Queue::failing(function (JobFailed $event) {
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //        if ($this->app->environment() !== 'production') {
        ////            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        //        }
        Cashier::ignoreMigrations();

        if ($this->app->environment('local')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }
}
