<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * ---------------------------------------------------------
     * Global HTTP middleware stack.
     * These middleware run during every request to your app.
     * ---------------------------------------------------------
     */
    protected $middleware = [
        // Handles trusted proxies
        \App\Http\Middleware\TrustProxies::class,

        // Handles CORS (Cross-Origin Resource Sharing)
        \Illuminate\Http\Middleware\HandleCors::class,

        // Checks if the application is in maintenance mode
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,

        // Validates POST request size
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,

        // Trims strings from request data
        \App\Http\Middleware\TrimStrings::class,

        // Converts empty strings to null
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * ---------------------------------------------------------
     * Middleware Groups
     * ---------------------------------------------------------
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,

            // Allows errors to be shared with views
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,

            // CSRF protection
            \App\Http\Middleware\VerifyCsrfToken::class,

            // Substitutes route bindings
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            // Throttle API requests (60 per minute by default)
            'throttle:api',

            // Substitutes route bindings
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * ---------------------------------------------------------
     * Route Middleware Aliases
     * ---------------------------------------------------------
     * These middleware may be assigned to groups or used individually.
     */
    protected $middlewareAliases = [
        // Laravel authentication
        'auth' => \App\Http\Middleware\Authenticate::class,

        // Redirects authenticated users away from guest-only routes
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,

        // Verifies email if required
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,

        // ✅ Custom Admin Middleware (important for your admin routes)
        'admin' => \App\Http\Middleware\IsAdmin::class,
    ];

    protected $routeMiddleware = [
    'admin' => \App\Http\Middleware\IsAdmin::class,
    'auth' => \App\Http\Middleware\Authenticate::class,
    
];

}
