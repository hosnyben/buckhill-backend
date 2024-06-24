<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

use App\Guards\JwtGuard;
use Illuminate\Contracts\Foundation\Application;

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
        // Register the JWT guard
        Auth::extend('jwt', function (Application $app, string $name, array $config) {
            return new JwtGuard(
                Auth::createUserProvider($config['provider']),
                $app['request']
            );
        });
    }
}
