<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        RateLimiter::for('api_limit', function(Request $request) {

            return Limit::perMinute(5)->response(function() {
                Log::channel("api_logs")->info("API /appTopCategory rate limit exceeded");
                return new Response([
                    'status_code' => 400,
                    'message' => 'bad',
                    'data' => 'API calls rate limit exceeded (max 5 per minute)'
                ]);
            });

        });
    }
}
