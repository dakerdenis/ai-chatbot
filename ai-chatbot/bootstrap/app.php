<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\AuthenticateClient;
use App\Http\Middleware\ClientSessionAuth;
use App\Http\Middleware\AdminSessionAuth;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withProviders([
        \App\Providers\RateLimitServiceProvider::class, // <— добавили
    ])
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'auth.client' => \App\Http\Middleware\AuthenticateClient::class,
            'client.auth' => \App\Http\Middleware\ClientSessionAuth::class,
            'admin.auth'  => \App\Http\Middleware\AdminSessionAuth::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

