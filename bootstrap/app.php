<?php

use App\Http\Middleware\SetFamilyContext;
use App\Providers\AuthServiceProvider;
use App\Providers\BroadcastServiceProvider;
use App\Support\ApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\HandleCors;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withProviders([
        AuthServiceProvider::class,
        BroadcastServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
    ])
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(HandleCors::class);
        $middleware->append(SetFamilyContext::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Throwable $e, Request $request) {
            if (! $request->expectsJson()) {
                return null;
            }

            if ($e instanceof ValidationException) {
                return ApiResponse::error(
                    $e->errors(),
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    'Validation Error',
                    'https://httpstatuses.com/422'
                );
            }

            if ($e instanceof AuthenticationException) {
                return ApiResponse::error(
                    ['message' => [$e->getMessage() ?: 'Unauthenticated.']],
                    Response::HTTP_UNAUTHORIZED,
                    'Unauthorized',
                    'https://httpstatuses.com/401'
                );
            }

            if ($e instanceof AuthorizationException) {
                return ApiResponse::error(
                    ['message' => [$e->getMessage() ?: 'Forbidden.']],
                    Response::HTTP_FORBIDDEN,
                    'Forbidden',
                    'https://httpstatuses.com/403'
                );
            }

            if ($e instanceof HttpExceptionInterface) {
                return ApiResponse::error(
                    ['message' => [$e->getMessage() ?: Response::$statusTexts[$e->getStatusCode()] ?? 'Error']],
                    $e->getStatusCode(),
                    Response::$statusTexts[$e->getStatusCode()] ?? 'Error',
                    "https://httpstatuses.com/{$e->getStatusCode()}"
                );
            }

            report($e);

            return ApiResponse::error(
                ['message' => ['Internal server error.']],
                Response::HTTP_INTERNAL_SERVER_ERROR,
                'Internal Server Error',
                'https://httpstatuses.com/500'
            );
        });
    })->create();
