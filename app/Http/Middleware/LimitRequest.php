<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LimitRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (
            $request->isMethod('GET') &&
            $request->has('limit') &&
            ($request->expectsJson() || $request->is('admin/api/*') || $request->is('api/*'))
        ) {
            $request->merge([
                'limit' => min($request->limit, 200),
            ]);
        }

        return $next($request);
    }
}
