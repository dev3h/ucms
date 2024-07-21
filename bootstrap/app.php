<?php

use App\Exceptions\BaseException;
use App\Helpers\ResponseHelper;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->use([
            \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
            \App\Http\Middleware\RequestLoggerMiddleware::class,
            \App\Http\Middleware\LimitRequest::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
            \App\Http\Middleware\UserActivity::class,
        ]);

        $middleware->api([
            // \Illuminate\Routing\Middleware\ThrottleRequests::class . ':api',
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'type_admin_check' => \App\Http\Middleware\CheckAdminType::class,
        ]);

        $middleware->redirectTo(
            guests: '/admin/login',
            users: '/admin/profile',
        );
    })

    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->dontReport([
            BaseException::class
        ]);

        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->is('api/*') || $request->is('admin/api/*')) {
                if ($e instanceof NotFoundHttpException) {
                    return ResponseHelper::sendErrorResponse(__('errors.no_data'), null, 404);
                }

                if ($e instanceof TooManyRequestsHttpException) {
                    return ResponseHelper::sendErrorResponse(__('errors.too_many_requests'), null, 429);
                }
            }
        });
    })->create();
