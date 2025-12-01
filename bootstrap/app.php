<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->prepend([
            \App\Http\Middleware\AddRequestId::class,
        ]);

        $middleware->api(
            append: [
                \App\Http\Middleware\ForceJsonResponse::class,
            ]
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\App\Modules\Transfer\Exceptions\TransactionException $e) {
            return $e->render(request());
        });

        $exceptions->dontReport([
            \App\Modules\Transfer\Exceptions\TransactionException::class,
        ]);

        $exceptions->report(function (Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Exception occurred', [
                'message' => $e->getMessage(),
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request_id' => \Illuminate\Support\Facades\Context::get('request_id'),
                'user_id' => request()->user()?->id,
            ]);
        });
    })->create();
