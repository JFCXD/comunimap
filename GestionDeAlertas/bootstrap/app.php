<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        /*
        |--------------------------------------------------------------------------
        | PROXIES DE CONFIANZA EN RAILWAY
        |--------------------------------------------------------------------------
        |
        | Railway recibe las conexiones HTTPS y las
        | reenvía a Laravel mediante un proxy.
        |
        | Esta configuración permite que Laravel
        | reconozca correctamente el protocolo HTTPS
        | original y genere enlaces y formularios seguros.
        |
        */

        $middleware->trustProxies(
            at: '*'
        );

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
