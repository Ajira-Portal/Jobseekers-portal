<?php
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsEmployer;
use App\Http\Middleware\IsJobseeker;
use App\Http\Middleware\CheckProfileComplete;
use App\Http\Middleware\PreventBackHistory;
use App\Http\Middleware\SetLocale;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            SetLocale::class,
            SecurityHeaders::class,
        ]);

        $middleware->redirectGuestsTo(fn () => route('login'));
        $middleware->redirectUsersTo(function () {
            if (auth()->check()) {
                return match (auth()->user()->role) {
                    'admin' => route('admin.dashboard'),
                    'employer' => route('employer.dashboard'),
                    default => '/',
                };
            }
            return route('home');
        });

        $middleware->alias([
            'isAdmin' => IsAdmin::class,
            'isEmployer' => IsEmployer::class,
            'isJobseeker' => IsJobseeker::class,
            'profile.complete' => CheckProfileComplete::class,
            'PreventBackHistory' => PreventBackHistory::class,
            'prevent.back.history' => PreventBackHistory::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
