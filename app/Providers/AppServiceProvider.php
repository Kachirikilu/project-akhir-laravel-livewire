<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
        if (env('APP_URL')) {
            URL::forceRootUrl(env('APP_URL'));
        }
        if (env('APP_ENV') !== 'local') {
            URL::forceScheme('https');
        }
        // register custom route middleware aliases since kernel file isn't
        // used in this project structure. this mirrors the existing
        // "is_admin" alias that appears in routes/web.php.
        $router = $this->app->make(\Illuminate\Routing\Router::class);
        $router->aliasMiddleware('is_admin', \App\Http\Middleware\IsAdmin::class);
        $router->aliasMiddleware('is_dosen', \App\Http\Middleware\IsDosen::class);
        $router->aliasMiddleware('is_staff', \App\Http\Middleware\IsStaff::class);
        $router->aliasMiddleware('is_mahasiswa', \App\Http\Middleware\IsMahasiswa::class);
    }
}

