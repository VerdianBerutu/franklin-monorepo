<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     */
    protected $middleware = [
        // ✅ PERBAIKAN: Tambahkan CORS middleware di paling atas
        \Illuminate\Http\Middleware\HandleCors::class,
        
        // Trust proxies
        // \App\Http\Middleware\TrustProxies::class,
        
        // Maintenance mode
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        
        // Validate post size
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        
        // Trim strings
        \App\Http\Middleware\TrimStrings::class,
        
        // Convert empty strings to null
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            // ✅ PERBAIKAN: Tambahkan CORS untuk API routes
            \Illuminate\Http\Middleware\HandleCors::class,
            
            // Sanctum middleware untuk stateful API
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            
            // Throttle requests
            'throttle:api',
            
            // Substitute bindings
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     */
    protected $routeMiddleware = [
        // Authentication
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
        
        // Authorization
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        
        // Guest
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        
        // Password confirmation
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        
        // Signed routes
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        
        // Throttle
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        
        // Email verification
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        
        // ✅ PERBAIKAN: Spatie Permission middlewares
        'role' => \Spatie\Permission\Middlewares\RoleMiddleware::class,
        'permission' => \Spatie\Permission\Middlewares\PermissionMiddleware::class,
        'role_or_permission' => \Spatie\Permission\Middlewares\RoleOrPermissionMiddleware::class,
    ];
}