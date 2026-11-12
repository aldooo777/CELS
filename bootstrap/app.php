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
        // Alias middleware khusus CELS
        $middleware->alias([
            'role'          => \App\Http\Middleware\RoleMiddleware::class,
            'campus.active' => \App\Http\Middleware\EnsureCampusActive::class,
        ]);

        // Pengguna yang sudah login diarahkan ke dashboard sesuai perannya
        $middleware->redirectUsersTo(fn () => route('dashboard'));
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
