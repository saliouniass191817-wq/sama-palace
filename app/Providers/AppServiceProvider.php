<?php

namespace App\Providers;

use App\Models\Product;
use App\Policies\ProductPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Http\Middleware\TrustProxies;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

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
        Gate::policy(Product::class, ProductPolicy::class);
        TrustProxies::at(config('trustedproxy.proxies'));

        RateLimiter::for('login', function (Request $request) {
            $key = Str::transliterate(Str::lower($request->input('phone', '')).'|'.$request->ip());

            return Limit::perMinute(5)->by($key);
        });

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
