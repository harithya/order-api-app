<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn(Request $request) => $request->is('api/*'),
        );

        $exceptions->render(function (\Throwable $e, Request $request) {
            if ($request->is('api/*')) {
                $status = match (true) {
                    $e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException => 404,
                    $e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException => 404,
                    $e instanceof \Illuminate\Validation\ValidationException => 422,
                    $e instanceof \Symfony\Component\HttpKernel\Exception\HttpException => $e->getStatusCode(),
                    default => 500,
                };

                $data = match (true) {
                    $status === 404 => ['message' => 'Data not found'],
                    $e instanceof \Illuminate\Validation\ValidationException => [
                        'message' => $e->getMessage(),
                        'errors' => $e->errors(),
                    ],
                    default => ['message' => $e->getMessage() ?: 'An error occurred'],
                };

                return response()->json($data, $status);
            }
        });
    })->create();
