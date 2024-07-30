<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Auth\Middleware\Authenticate as AuthenticateTemplate;
use Symfony\Component\HttpFoundation\Response;

class Authenticate extends AuthenticateTemplate
{
    protected function redirectTo(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => __('Unauthenticated.')], 401);
        }

        if ($request->routeIs('admin.*')) {
            return route('admin.login.form');
        } else {
            return route('user.login.form');
        }
    }
}
