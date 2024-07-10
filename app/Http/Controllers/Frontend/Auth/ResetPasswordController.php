<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $passwordToken = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        $isValidToken = true;
        if ($passwordToken && Hash::check($token, $passwordToken->token)) {
            $expirationTime = config('auth.passwords.users.expire');
            $tokenCreatedAt = Carbon::parse($passwordToken->created_at);

            if (!Carbon::now()->subMinutes($expirationTime)->lt($tokenCreatedAt)) {
                $isValidToken = false;
            }
        } else {
            $isValidToken = false;
        }

        if (!$isValidToken) {
            abort(404);
        }

        return Inertia::render('Auth/Page/Password/ResetPassword', [
            'token' => $token
        ]);
    }
}
