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
    public const HOME = '/';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware(['web'])
                ->group(base_path('routes/web.php'));

            $shopAddon = \App\Models\Addon::where('unique_identifier', 'shop')->where('status', 1)->first();
            if ($shopAddon) {
                Route::middleware(['web'])
                    ->group(base_path('routes/shop.php'));
            }

            // Always load service_selling routes (remove addon check for testing)
            if (file_exists(base_path('routes/service_selling.php'))) {
                Route::middleware(['web'])
                    ->group(base_path('routes/service_selling.php'));
            }

            // Always load customer routes
            if (file_exists(base_path('routes/customer.php'))) {
                Route::middleware(['web', 'auth'])
                    ->group(base_path('routes/customer.php'));
            }
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
}
