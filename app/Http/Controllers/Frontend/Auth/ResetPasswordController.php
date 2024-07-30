<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Http\Controllers\Controller;
use App\Models\AdminPasswordResetToken;
use App\Models\PasswordResetToken;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    public function formForgotPassword()
    {
        return Inertia::render('Auth/Page/Password/ForgotPassword');
    }

    public function confirmForgotPassword()
    {
        return Inertia::render('Auth/Page/Password/ConfirmForgotPassword');
    }
    public function passwordReset(string $token, Request $request)
    {
        $model = PasswordResetToken::query();
        if($request->routeIs('admin.*')){
            $model = AdminPasswordResetToken::query();
        }
        $passwordToken = $model->where('email', $request->email)->first();

        $isValidToken = true;
        if ($passwordToken && Hash::check($token, $passwordToken->token)) {
            $expirationTime = config('auth.passwords.users.expire');
            if($request->routeIs('admin.*')){
                $expirationTime = config('auth.passwords.admin.expire');
            }
            $tokenCreatedAt = Carbon::parse($passwordToken->created_at);

            if (!Carbon::now()->subMinutes($expirationTime)->lt($tokenCreatedAt)) {
                $isValidToken = false;
            }
        } else {
            $isValidToken = false;
        }

        if (!$isValidToken) {
            abort(403, __('Invalid signature'));
        }

        return Inertia::render('Auth/Page/Password/ResetPassword', [
            'token' => $token
        ]);
    }
}
