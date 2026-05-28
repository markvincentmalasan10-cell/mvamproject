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
    ->withCommands([
        \App\Console\Commands\RepairSchema::class,
    ])
    ->withMiddleware(function (Middleware $middleware): void {
        
        //route middeleware

        $middleware->alias([
            'maintenance' => \App\Http\Middleware\DownForMaintenanceMw::class,
            'sessionUserMw' => \App\Http\Middleware\SessionUserMw::class,
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);

        //middleware group

        $middleware->group('groupMiddleware', [
            \App\Http\Middleware\MiddlewareOne::class,
            \App\Http\Middleware\MiddlewareTwo::class,
            \App\Http\Middleware\DownForMaintenanceMw::class,
        ]);

        //global middleware

        $middleware->append(App\Http\Middleware\PromotionMw::class);

        $middleware->validateCsrfTokens(except: [
            'change-password',
            'logout',
        ]);



    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
