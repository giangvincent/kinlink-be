<?php

use App\Http\Middleware\SetFamilyContext;
use App\Providers\AuthServiceProvider;
use App\Support\ApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withProviders([
        AuthServiceProvider::class,
    ])
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(SetFamilyContext::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $e, Request $request) {
            if (! $request->expectsJson()) {
                return null;
            }

            if ($e instanceof ValidationException) {
                return ApiResponse::error(
                    $e->errors(),
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }

            if ($e instanceof AuthenticationException) {
                return ApiResponse::error(
                    ['message' => [$e->getMessage() ?: 'Unauthenticated.']],
                    Response::HTTP_UNAUTHORIZED
                );
            }

            if ($e instanceof AuthorizationException) {
                return ApiResponse::error(
                    ['message' => [$e->getMessage() ?: 'Forbidden.']],
                    Response::HTTP_FORBIDDEN
                );
            }

            if ($e instanceof HttpExceptionInterface) {
                return ApiResponse::error(
                    ['message' => [$e->getMessage() ?: Response::$statusTexts[$e->getStatusCode()] ?? 'Error']],
                    $e->getStatusCode()
                );
            }

            report($e);

            return ApiResponse::error(
                ['message' => ['Internal server error.']],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        });
    })->create();
