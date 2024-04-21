<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AuthenticatedSessionController extends Controller
{
    public function formLogin()
    {
        return Inertia::render('Auth/Page/Login');
    }

    public function formChangePasswordFirst(Request $request)
    {
        $token = $request->token;
        $user = User::where('token_first_change', $token)->select('email', 'token_first_change')->first();
        if ($user === null) {
            return to_route('admin.login.form')->with('error', __('Token is invalid'));
        }
        return Inertia::render('Auth/Password/ChangePasswordFirst', [
            'user' => $user,
        ]);
    }

    public function logout()
    {
        $routeRedirect = route('admin.login.form');
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect($routeRedirect);
    }
}
