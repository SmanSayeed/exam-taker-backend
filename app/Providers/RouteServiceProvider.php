<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::prefix('api/v1')
                ->middleware('api')
                ->group(function () {
                    // Public API routes
                    $this->mapPublicRoutes();

                    // Protected API routes
                    Route::middleware(['auth:sanctum'])->group(function () {
                        $this->mapProtectedRoutes();
                    });
                });
        });
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }

    /**
     * Map the public API routes.
     */
    protected function mapPublicRoutes()
    {
        Route::group([], base_path('routes/api/v1/admin/admin_public_routes.php'));
    }

    /**
     * Map the protected API routes.
     */
    protected function mapProtectedRoutes()
    {
        Route::prefix('admin')->group(base_path('routes/api/v1/admin/admin_protected_routes.php'));
        // You can add more route groups for different user roles or sections.
    }
}
