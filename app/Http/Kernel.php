<?php

namespace App\Http;

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
            \Laravel\Spark\Http\Middleware\CreateFreshApiToken::class,
        ],

        'api' => [
            'throttle:60,1',
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
        'dev' => \Laravel\Spark\Http\Middleware\VerifyUserIsDeveloper::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'hasTeam' => \Laravel\Spark\Http\Middleware\VerifyUserHasTeam::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'subscribed' => \Laravel\Spark\Http\Middleware\VerifyUserIsSubscribed::class,
        'teamSubscribed' => \Laravel\Spark\Http\Middleware\VerifyTeamIsSubscribed::class,
        'role' => \Zizaco\Entrust\Middleware\EntrustRole::class,
        'permission' => \Zizaco\Entrust\Middleware\EntrustPermission::class,
        'ability' => \Zizaco\Entrust\Middleware\EntrustAbility::class,
        'ownsMerchant' => \App\Http\Middleware\UserOwnsMerchant::class,
        'auth.integration.webhook' => \App\Http\Middleware\AuthIntegrationWebhook::class,
        'launcher.valid-connection' => \App\Http\Middleware\Api\Launcher\ValidConnection::class,
        'launcher.customer-auth' => \App\Http\Middleware\Api\Launcher\CustomerAuth::class,
        'vip_permissions' => \App\Http\Middleware\Permissions\VIPPermissions::class,
        'referral_permissions' => \App\Http\Middleware\Permissions\ReferralPermissions::class,
        'rewards_permissions' => \App\Http\Middleware\Permissions\RewardsPermissions::class,
        'reports_permissions' => \App\Http\Middleware\Permissions\ReportsPermissions::class,
        'points_permissions' => \App\Http\Middleware\Permissions\PointsEarningPermissions::class,
        'spending_permissions' => \App\Http\Middleware\Permissions\PointsSpendingPermissions::class,
        'widget.valid-connection' => \App\Http\Middleware\Api\Widget\ValidConnection::class,
        'widget.customer-auth' => \App\Http\Middleware\Api\Widget\CustomerAuth::class,
    ];
}
