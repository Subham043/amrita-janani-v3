<?php

use App\Middlewares\EnsurePasswordIsSet;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Csp\AddCspHeaders;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web:[
             __DIR__.'/../routes/web/user.php',
            __DIR__.'/../routes/web/admin.php',
        ],
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
        $middleware->web(prepend: [
            AddCspHeaders::class,
        ]);

        $middleware->alias([
            'social' => EnsurePasswordIsSet::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
