<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = session('locale', config('app.locale'));

        \Log::info('SetLocale middleware - Session locale: ' . $locale);

        if (in_array($locale, ['en', 'km'])) {
            app()->setLocale($locale);
            \Log::info('SetLocale middleware - App locale set to: ' . app()->getLocale());
        }

        return $next($request);
    }
}
