<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
         Schema::defaultStringLength(191);
        $link = public_path('storage');
        $target = storage_path('app/public');

        if (app()->isProduction() && !file_exists($link) && is_dir($target)) {
            try {
                File::link($target, $link);
            } catch (\Throwable $e) {
                Log::warning('Could not create storage symlink automatically.', [
                    'link' => $link,
                    'target' => $target,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
