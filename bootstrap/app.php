<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'auth.user' => \App\Http\Middleware\CheckUserAuth::class,
            'admin.auth' => \App\Http\Middleware\CheckAdminAuth::class,
            'admin.role' => \App\Http\Middleware\CheckAdminRole::class,
            'kyc.step' => \App\Http\Middleware\CheckKycStep::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
