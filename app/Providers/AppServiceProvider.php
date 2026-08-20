<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        Paginator::useBootstrap();
        if (app()->environment('local', 'development')) {
            $currentLimit = ini_get('memory_limit');
            if ($currentLimit !== '-1') {
                ini_set('memory_limit', '1G');
            }
        }
    }
}
