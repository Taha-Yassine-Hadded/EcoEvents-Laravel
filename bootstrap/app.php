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
            'jwt.optional' => \App\Http\Middleware\OptionalJWT::class,
        ]);
        
        // Exclure les routes API du CSRF
        $middleware->validateCsrfTokens(except: [
            'api/sponsor/notifications/*',
            'api/sponsor/feedback/*',
            'api/sponsor/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
