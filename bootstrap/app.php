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

    /**
     * Register application middlewares
     */
    ->withMiddleware(function (Middleware $middleware): void {

        // Register custom middleware alias
        $middleware->alias([
            'blockIP' => \App\Http\Middleware\BlockIpMiddleware::class,
        ]);

    })

    /**
     * Register exception handling
     */
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })

    ->create();
