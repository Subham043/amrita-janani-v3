<?php

use App\Middlewares\EnsureIsAdmin;
use App\Middlewares\EnsureIsNotBlocked;
use App\Middlewares\EnsurePasswordIsSet;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
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
            'password_is_set' => EnsurePasswordIsSet::class,
            'is_not_blocked' => EnsureIsNotBlocked::class,
            'is_admin' => EnsureIsAdmin::class
        ]);

        $middleware->redirectGuestsTo(fn (Request $request) => $request->is('admin/*') ? route('signin') : route('login'));
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
