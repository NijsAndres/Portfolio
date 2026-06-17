<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Sets the application locale for the public frontend from the matched route.
 *
 * Each public home route declares its locale via ->defaults('locale', ...)
 * (/ = en, /nl = nl); route defaults are merged into the route parameters, so
 * they are readable here. Anything outside the supported set is ignored and the
 * configured default (config('app.locale')) stands, so a bad value can't leak.
 */
class SetLocale
{
    /** Locales the public site is available in. */
    public const SUPPORTED = ['en', 'nl'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->route('locale');

        if (in_array($locale, self::SUPPORTED, true)) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
