<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;

class RateLimitServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // 10 req/min — для виджета (ip + UA + client_id если есть)
        RateLimiter::for('client-chat', function (Request $request) {
            $key = implode('|', [
                $request->ip(),
                substr((string) $request->userAgent(), 0, 80),
                optional($request->get('client'))->id ?? 'no-client',
            ]);
            return Limit::perMinute(10)->by($key);
        });

        // 10 req/min — для демо на лендинге (ip + UA)
        RateLimiter::for('demo-chat', function (Request $request) {
            $key = $request->ip().'|'.substr((string) $request->userAgent(), 0, 80);
            return Limit::perMinute(10)->by($key);
        });
    }
}
