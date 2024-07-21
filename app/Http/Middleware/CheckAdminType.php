<?php

namespace App\Http\Middleware;

use App\Enums\UserTypeEnum;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(auth()->user()?->type !== UserTypeEnum::ADMIN->value){
            if($request->routeIs('admin.api.*')){
                return response()->json(['message' => __('auth.unauthorized')], 401);
            } else {
                abort(403, __('auth.unauthorized'));
            }
        }
        return $next($request);
    }
}
