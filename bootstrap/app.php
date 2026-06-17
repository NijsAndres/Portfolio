<?php

use App\Http\Middleware\SetLocale;
use App\Http\Middleware\VerifyCmsApiToken;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // The /track endpoint is hit via navigator.sendBeacon, which cannot
        // attach the CSRF header, so it is exempt from CSRF verification.
        $middleware->validateCsrfTokens(except: ['track']);

        // Bearer-token guard for the internal /api/cms endpoints (Step 12, MCP).
        // setlocale: picks NL/EN for the public frontend (see routes/web.php).
        $middleware->alias([
            'cms.token' => VerifyCmsApiToken::class,
            'setlocale' => SetLocale::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();

// Shared hosting (Combell) locks the web root to a sibling `www` folder, so the
// public directory lives outside basePath. When the default basePath/public is
// absent, point Laravel at ../www so asset()/@vite/storage:link resolve there.
if (! is_dir($app->basePath('public')) && is_dir($webroot = dirname($app->basePath()).'/www')) {
    $app->usePublicPath($webroot);
}

return $app;
