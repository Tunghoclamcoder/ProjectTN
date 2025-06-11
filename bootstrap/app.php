<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            // 'auth.customer' => \App\Http\Middleware\CustomerAuthentication::class,
            // 'auth.admin' => \App\Http\Middleware\AdminAuthentication::class,
            // 'check.role' => \App\Http\Middleware\CheckRole::class,
            'prevent-back-history' => \App\Http\Middleware\PreventBackHistory::class,

        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
