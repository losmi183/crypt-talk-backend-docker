<?php

use App\Http\Middleware\JWTMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Throwable;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'jwt' => JWTMiddleware::class,
        ]);
    })
    ->withExceptions(function ($exceptions) {
        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'error'     => true,
                    'exception' => class_basename($e),
                    'message'   => $e->getMessage(), // tekst poruke
                    'file'      => $e->getFile(),    // fajl gde je exception
                    'line'      => $e->getLine(),    // linija gde je exception
                    'trace'     => app()->isLocal() ? $e->getTrace() : [],
                ], 500);
            }

            return null;
        });
    })
    ->create();
