<?php

namespace App\Http;

use App\Http\Middleware\VerifyTeamIsSubscribed;
use App\Http\Middleware\VerifyUserHasTeam;
use App\Http\Middleware\VerifyUserIsDeveloper;
use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \Fruitcake\Cors\HandleCors::class,
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\CreateFreshApiToken::class,
        ],

        'api' => [
            'throttle:5000,1',
            'bindings',
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'dev' =>VerifyUserIsDeveloper::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'hasTeam' => VerifyUserHasTeam::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        //        'subscribed' => \Laravel\Spark\Http\Middleware\VerifyUserIsSubscribed::class,
        'subscribed' => \App\Http\Middleware\IsSubscribed::class,
        'teamSubscribed' => VerifyTeamIsSubscribed::class,
        'authorized' => \App\Http\Middleware\API\V1\Authorized::class,
        'verifyStripeSignature' => \App\Http\Middleware\VerifyStripeSignature::class,
        'verifyProjectUser' => \App\Http\Middleware\VerifyProjectUser::class,
        'projectPublished' => \App\Http\Middleware\ProjectPublished::class,
    ];
}
