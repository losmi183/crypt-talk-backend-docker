<?php

use App\Http\Middleware\JWTMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
// use Throwable;

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

                // Default status i poruka
                $status = 500;
                $message = 'Server error';

                // Razlikovanje tipa exception-a
                if ($e instanceof \Illuminate\Auth\AuthenticationException) {
                    $status = 401;
                    $message = 'Unauthenticated';
                } elseif ($e instanceof \Illuminate\Validation\ValidationException) {
                    $status = 422;
                    $message = $e->errors(); // detalji validacije
                } elseif ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                    $status = $e->getStatusCode(); // status iz HttpException
                    $message = $e->getMessage();
                } else {
                    $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;
                    $message = $e->getMessage();
                }

                // Dev mode -> full debug info
                if(app()->environment('local')) {
                    return response()->json([
                        'error'     => true,
                        'exception' => class_basename($e),
                        'message'   => $e->getMessage(),
                        'file'      => $e->getFile(),
                        'line'      => $e->getLine(),
                        'trace'     => $e->getTrace(),
                    ], $status);
                }

                // Production -> samo minimalna poruka
                if(app()->environment('production')) {
                    return response()->json([
                        'error'   => true,
                        'message' => $message,
                    ], $status);
                }
            }
            return null;
        });
    })
    ->create();
