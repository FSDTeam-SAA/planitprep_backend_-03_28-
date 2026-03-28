<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'adminAuth' => \App\Http\Middleware\Authenticate::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Throwable $e, Illuminate\Http\Request $request) {
            \Log::error(get_class($e).': '.$e->getMessage());

            if ($e instanceof \Illuminate\Auth\AuthenticationException) {
                return response()->json([
                    'status' => false,
                    'msg' => 'Unauthorized. Token missing or invalid.',
                ], 401);
            }

            // Optional: handle the redirect-to-login route not found
            if ($e instanceof \Symfony\Component\Routing\Exception\RouteNotFoundException &&
                str_contains($e->getMessage(), 'login')) {
                return response()->json([
                    'status' => false,
                    'msg' => 'Login route not defined. You must handle unauthenticated API properly.',
                ], 401);
            }

            if ($request->is('api/*') || $request->expectsJson()) {
                $status = 500;

                if ($e instanceof \Illuminate\Validation\ValidationException) {
                    $status = 422;
                } elseif ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface) {
                    $status = $e->getStatusCode();
                }

                $response = [
                    'status' => false,
                    'msg' => app()->hasDebugModeEnabled()
                        ? $e->getMessage()
                        : 'Server error.',
                ];

                if (app()->hasDebugModeEnabled()) {
                    $response['exception'] = class_basename($e);
                }

                return response()->json($response, $status);
            }

            return null;
        });
    })
    ->create();
