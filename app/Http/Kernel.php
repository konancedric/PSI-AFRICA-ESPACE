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
        // \App\Http\Middleware\TrustHosts::class,
        \App\Http\Middleware\TrustProxies::class,
        \Fruitcake\Cors\HandleCors::class,
        \App\Http\Middleware\CheckForMaintenanceMode::class, // Laravel 7
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
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
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            'throttle:60,1',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
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
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        
        // ==================== MIDDLEWARES SPATIE PERMISSION ====================
        'role' => \Spatie\Permission\Middlewares\RoleMiddleware::class,
        'permission' => \Spatie\Permission\Middlewares\PermissionMiddleware::class,
        'role_or_permission' => \App\Http\Middleware\RoleOrPermissionMiddleware::class,
        'bypass.permission' => \App\Http\Middleware\BypassPermissionCheck::class,
        
        // ==================== MIDDLEWARE CUSTOM USER TYPE - EXISTANT ====================
        'user_type' => \App\Http\Middleware\UserTypeMiddleware::class,
        
        // ==================== NOUVEAUX MIDDLEWARES D'ACCÈS PSI AFRICA ====================
        'admin.access' => \App\Http\Middleware\AdminAccessMiddleware::class,
        'commercial.access' => \App\Http\Middleware\CommercialAccessMiddleware::class,
        'comptoir.access' => \App\Http\Middleware\ComptoirAccessMiddleware::class,
        'commercial.or.admin.access' => \App\Http\Middleware\CommercialOrAdminAccessMiddleware::class,
        'comptoir.or.admin.access' => \App\Http\Middleware\ComptoirOrAdminAccessMiddleware::class,
        'check.crm.permission' => \App\Http\Middleware\CheckCRMPermission::class,
        'check.caisse.access' => \App\Http\Middleware\CheckCaisseAccess::class,
    ];

/**
 * The application's middleware aliases.
 *
 * @var array
 */
protected $middlewareAliases = [
    // ... autres middlewares
    'crm.permission' => \App\Http\Middleware\CheckCRMPermission::class,
];
}