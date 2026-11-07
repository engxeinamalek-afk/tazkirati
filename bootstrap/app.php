<?php

use App\Http\Middleware\CheckRole;
use App\Http\Middleware\ForceJsonResponse;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Database\QueryException;
use Illuminate\Auth\AuthenticationException;
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->prependToGroup('api', ForceJsonResponse::class);

        $middleware->alias([
            'role' => CheckRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $apiResponse = new class {use \App\Traits\ApiResponse;};

        $exceptions->shouldRenderJsonWhen(function (Request $request, Throwable $e) {
            if ($request->is('api/*')) {
                return true; 
            }
            return $request->expectsJson();
        });
        // اخطاء الفاليديشن
        $exceptions->render(function (ValidationException $e, Request $request) use ($apiResponse) {
            return $apiResponse->errorResponse(
                message: 'Validation failed: Invalid input data.',
                errors: $e->errors(),
                statusCode: 422
            );
        });

        $exceptions->render(function (NotFoundHttpException $e, Request $request) use ($apiResponse) {
        
            if ($e->getPrevious() instanceof ModelNotFoundException) {
                return $apiResponse->errorResponse(
                    message: 'Resource not found.',
                    errors: 'The requested database record or ID does not exist.',
                    statusCode: 404
                );
            }

            return $apiResponse->errorResponse(
                message: 'Resource not found.',
                errors: 'The requested URL route or HTTP method is invalid.',
                statusCode: 404
            );
        });

        // اخطاء الصلاحيات
        $exceptions->render(function (AccessDeniedHttpException $e) use ($apiResponse) {
            return $apiResponse->errorResponse(
                message: 'Access denied: Unauthorized operation.',
                errors: $e->getMessage(),
                statusCode: 403
            );
        });

        // غير مسجل دخول
        $exceptions->render(function (AuthenticationException $e, Request $request) use ($apiResponse) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return $apiResponse->errorResponse(message:'Unauthorized. Please log in first to access this resource.',
                                                    errors:$e->getMessage(),
                                                    statusCode: 401);
            }
        });

        $exceptions->render(function (QueryException $e, Request $request) use ($apiResponse) {
            return $apiResponse->errorResponse(
                message: 'Database connection failed or query error occurred.',
                errors: config('app.debug') ? $e->getMessage() : 'Unable to connect to the database infrastructure.',
                statusCode: 500
            );
        });

        $exceptions->render(function (Throwable $e, Request $request) use ($apiResponse) {
            $statusCode = 500;
            if($e instanceof HttpException){
                $statusCode=$e->getStatusCode();
            }
            elseif ($e->getCode() >= 400 && $e->getCode() < 600) {
                $statusCode = $e->getCode();
            }
            return $apiResponse->errorResponse(
                message: 'Internal server error occurred.', 
                errors: config('app.debug') ? $e->getMessage() : "Something went wrong on our end. Please try again later.",
                statusCode: $statusCode
            );
        });

    })->create();
