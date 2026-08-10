<?php
// app/Providers/ZanzibarServiceProvider.php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\ZanzibarService;

class ZanzibarServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('zanzibar', function ($app) {
            return new ZanzibarService();
        });
    }

    public function boot()
    {
        // Make $zanzibar available in all views
        view()->composer('*', function ($view) {
            try {
                $view->with('zanzibar', app('zanzibar'));
            } catch (\Exception $e) {
                // Silently fail if service not available
            }
        });
    }
}