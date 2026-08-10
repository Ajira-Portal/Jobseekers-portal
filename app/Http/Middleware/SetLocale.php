<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $locale = (string) $request->session()->get('locale', config('app.locale', 'sw'));

        if (!in_array($locale, ['sw', 'en'], true)) {
            $locale = 'sw';
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
