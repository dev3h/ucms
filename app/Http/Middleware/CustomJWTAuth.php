<?php

namespace App\Http\Middleware;

use App\Helpers\ResponseHelper;
use Closure;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

class CustomJWTAuth extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            $user = $this->auth->parseToken()->authenticate();
        } catch (TokenExpiredException $e) {
            return ResponseHelper::errorResponse(401, ['error' => 'token_expired'], 'TOKEN_EXPIRED');
        } catch (JWTException $e) {
            return ResponseHelper::errorResponse(401, ['error' => 'token_invalid'], 'TOKEN_INVALID');
        }

        if (!$user) {
            return ResponseHelper::errorResponse(404, ['error' => 'user_not_found'], 'USER_NOT_FOUND');
        }

        return $next($request);
    }
}
