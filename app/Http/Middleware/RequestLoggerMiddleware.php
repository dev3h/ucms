<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;

class RequestLoggerMiddleware
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $isProduction = config('app.env') == 'production';
        if ($isProduction) {
            return $response;
        }

        if (!$request->is('log-viewer/api/*') && ($request->expectsJson() || $request->is('api/*'))) {
            $requestBody = Arr::except($request->all(), $this->dontFlash);

            $logs = [
                'uri' => $request->getUri(),
                'user_id' => get_current_user_login()?->id,
                'method' => $request->getMethod(),
                'request_body' => $requestBody,
                'response' => json_decode($response->getContent()),
            ];

            Log::channel('infolog')->info('This is the log info from the api: ' . $logs['uri'] ?? null, $logs);
        }

        return $response;
    }
}
