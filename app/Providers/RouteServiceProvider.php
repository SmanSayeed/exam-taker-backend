<?php

namespace App\Providers;

use App\Models\ModelTest;
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

    private const ADMIN_ROUTE_PATH = 'routes/api/v1/admin_routes';
    private const STUDENT_ROUTE_PATH = 'routes/api/v1/student';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->routes(function () {

            Route::middleware('web')
                ->group(base_path('routes/web.php'));  // This line is crucial

            Route::middleware('api')
                ->prefix('api/v1')
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
        Route::group([], base_path(self::ADMIN_ROUTE_PATH . '/admin_public_routes.php'));
        Route::group([], base_path(self::STUDENT_ROUTE_PATH . '/student_public_routes.php'));
    }

    /**
     * Map the protected API routes.
     */
    protected function mapProtectedRoutes()
    {
        Route::prefix('admin')->group(base_path(self::ADMIN_ROUTE_PATH . '/admin_protected_routes/admin_profile_routes.php'));

        Route::prefix('admin')->group(base_path(self::ADMIN_ROUTE_PATH . '/admin_protected_routes/package_routes.php'));

        Route::prefix('admin')->group(base_path(self::ADMIN_ROUTE_PATH . '/admin_protected_routes/model_tests_routes.php'));

        Route::prefix('admin/manage')->group(base_path(self::ADMIN_ROUTE_PATH . '/admin_protected_routes/admin_manage_students_routes.php'));

        Route::prefix('admin')->group(base_path(self::ADMIN_ROUTE_PATH . '/admin_protected_routes/model_tests_routes.php'));

        Route::prefix('admin/questions')->group(base_path(self::ADMIN_ROUTE_PATH . '/admin_protected_routes/questions/questions_category_routes.php'));

        Route::prefix('admin/que')->group(base_path(self::ADMIN_ROUTE_PATH . '/admin_protected_routes/questions/que_routes.php'));

        Route::prefix('admin/manage')->group(base_path(self::ADMIN_ROUTE_PATH . '/admin_protected_routes/admin_manage_subscription.php'));

        Route::prefix('student')->group(base_path(self::STUDENT_ROUTE_PATH . '/student_protected_routes.php'));

        Route::prefix('admin/')->group(base_path(self::ADMIN_ROUTE_PATH . '/admin_protected_routes/que_tag_routes.php'));

        Route::prefix('admin/')->group(base_path(self::ADMIN_ROUTE_PATH . '/admin_protected_routes/admin_manage_pdf_routes.php'));
    }
}
