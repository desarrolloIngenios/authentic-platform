<?php

use App\Http\Middleware\AdminMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\EnsureAuthenticated;
use App\Http\Middleware\RedirectIfSessionExpired;
use App\Http\Middleware\UserMiddleware;
use App\Http\Middleware\HandleSessionError;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: '*');
        // Middleware global para manejar errores de sesión
        $middleware->append(HandleSessionError::class);

        $middleware->alias([
            'auth' => EnsureAuthenticated::class,
            'expiredSession' => RedirectIfSessionExpired::class,
            'admin' => AdminMiddleware::class,
            'user' => UserMiddleware::class,
            'handleSessionError' => HandleSessionError::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
