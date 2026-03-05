<?php


namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Api\AstrologerApiService;
use Illuminate\Support\Facades\Config;
use App\Services\Api\BlogService;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use App\Services\Api\AuthApiService;
use App\Services\Api\Contracts\AuthApiServiceInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind AstrologerApiService with config injection
        $this->app->bind(AstrologerApiService::class, function ($app) {
            $config = Config::get('services.astrologer_api', []);
            return new AstrologerApiService($config);
        });
        // Bind BlogService with config injection (using astrologer_api config for base_url)
        $this->app->bind(BlogService::class, function ($app) {
            $config = \Illuminate\Support\Facades\Config::get('services.astrologer_api', []);
            return new BlogService($config);
        });

        // Bind AuthApiServiceInterface to AuthApiService with config injection
        $this->app->bind(AuthApiServiceInterface::class, function ($app) {
            $config = \Illuminate\Support\Facades\Config::get('auth_api', []);
            return new AuthApiService($config);
        });

        // Bind RegisterApiService with config injection
        $this->app->bind(\App\Services\Api\RegisterApiService::class, function ($app) {
            $config = \Illuminate\Support\Facades\Config::get('auth_api', []);
            return new \App\Services\Api\RegisterApiService($config);
        });
    }

    /**
     * Bootstrap any application services.
     */
  public function boot(): void
    {
        RateLimiter::for('otp', function (Request $request) {
            return [
                Limit::perMinute(5)->by($request->ip()),
            ];
        });
    }
}
