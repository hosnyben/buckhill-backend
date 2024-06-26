<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Response;

use App\Guards\JwtGuard;
use Illuminate\Contracts\Foundation\Application;

use App\Models\User;

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
            $userProvider = Auth::createUserProvider($config['provider']);

            if (!$userProvider) {
                // Handle the null case, e.g., throw an exception or provide a default UserProvider
                throw new \Exception("User provider cannot be null.");
            }

            return new JwtGuard(
                $userProvider,
                $app->make('request')
            );
        });

        // Allow all operations for administrators
        Gate::before(function (User $user, string $ability) {
            if ($user->is_admin) {
                return true;
            }
        });

        // Define the gate for managing admin accounts
        Gate::define('manage-admin-accounts', function (User $user, $targetuser = null) {
            return $user->is_admin;
        });

        // Custom api response macro for consistent responses
        Response::macro('apiSuccess', function ($data) {
            return response()->json([
                'success' => 1,
                'data' => $data,
                'error' => null,
                'errors' => [],
                'extra' => [],
            ], 200);
        });

        Response::macro('apiError', function (\Exception $e, int $status = 500) {
            return response()->json([
                'success' => 0,
                'data' => [],
                'error' => $e->getMessage(),
                'errors' => [],
                'trace' => config('app.env') ? $e->getTrace() : [],
            ], $status);
        });
    }
}
