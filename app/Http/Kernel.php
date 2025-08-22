<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * Middleware globales que se ejecutan en TODAS las peticiones.
     *
     * ¡OJO!: aquí nunca metas helpers como auth() ni Request,
     * siempre deben ser clases de middleware válidas.
     */
    protected $middleware = [
        // Detección de proxies / X-Forwarded-*
        \App\Http\Middleware\TrustProxies::class,

        // CORS nativo
        \Illuminate\Http\Middleware\HandleCors::class,

        // Mantenimiento y límites de tamaño
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,

        // Normalización de inputs
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * Grupos de middleware por tipo de ruta.
     */
    protected $middlewareGroups = [
        'web' => [
            // Cookies / Sesión
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,

            // Compartir errores a vistas
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,

            // CSRF sólo para formularios web (no para tu API remota)
            \App\Http\Middleware\VerifyCsrfToken::class,

            // Enlace de parámetros {id} -> modelos
            \Illuminate\Routing\Middleware\SubstituteBindings::class,

            // Inertia adapter
            \App\Http\Middleware\HandleInertiaRequests::class,

            // Preload headers para assets
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ],

        'api' => [
            // Rate limit por defecto para API
            \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            // NOTA: sin sesiones ni CSRF aquí
        ],

        // Grupo opcional sin sesión/CSRF para endpoints públicos locales (p.ej. /ping-local)
        'noauth' => [
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * Alias de middleware individuales (para usar en rutas ->middleware('auth') etc.)
     */
    protected $routeMiddleware = [
        'auth'       => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can'        => \Illuminate\Auth\Middleware\Authorize::class,
        'guest'      => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed'     => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle'   => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified'   => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
    ];
}
