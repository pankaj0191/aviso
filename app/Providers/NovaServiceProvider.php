<?php

namespace App\Providers;

use App\Plan;
use App\User;
use App\Announcement;
use Laravel\Nova\Nova;
use Laravel\Nova\Cards\Help;
use App\Nova\Metrics\NewUsers;
use App\Observers\PlanObserver;
use App\Observers\UserObserver;
use App\Nova\Metrics\TotalVolume;
use App\Nova\Metrics\UsersTrailing;
use App\Nova\Metrics\YearlyRevenue;
use App\Nova\Metrics\MonthlyRevenue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Observers\AnnouncementObserver;
use Eminiarts\NovaPermissions\NovaPermissions;
use Laravel\Nova\NovaApplicationServiceProvider;

class NovaServiceProvider extends NovaApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        Nova::serving(function () {
            Announcement::observe(AnnouncementObserver::class);
            User::observe(UserObserver::class);
            Plan::observe(PlanObserver::class);
        });

        Nova::style('miami-ice-theme', public_path('css/miami.css'));
    }

    /**
     * Register the Nova routes.
     *
     * @return void
     */
    protected function routes()
    {
        Nova::routes()
                ->withAuthenticationRoutes()
                ->withPasswordResetRoutes()
                ->register();
    }

    /**
     * Register the Nova gate.
     *
     * This gate determines who can access Nova in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define('viewNova', function ($user) {
            return $user->hasPermissionTo('Browes Nova') ? $user->hasPermissionTo('Browes Nova') : Auth::logout();
            // return in_array($user->email, [
            //     'shanejohnlong@gmail.com',
            //     'gm.xerk@gmail.com'
            // ]);
        });
    }

    /**
     * Get the cards that should be displayed on the default Nova dashboard.
     *
     * @return array
     */
    protected function cards()
    {
        return [
            new MonthlyRevenue,
            new YearlyRevenue,
            new TotalVolume,
            new UsersTrailing,
            new NewUsers,
            new NovaPermissions(),
        ];
    }

    /**
     * Get the extra dashboards that should be displayed on the Nova dashboard.
     *
     * @return array
     */
    protected function dashboards()
    {
        return [];
    }

    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array
     */
    public function tools()
    {
        return [
            new \Themsaid\CashierTool\CashierTool(),
            new \Tightenco\NovaStripe\NovaStripe,
            (new NovaPermissions())->canSee(function ($request) {
                return $request->user()->isSuperAdmin();
            }),
        ];
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
