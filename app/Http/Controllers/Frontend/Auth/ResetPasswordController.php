<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

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
    public function passwordReset(string $token)
    {
        return Inertia::render('Auth/Page/Password/ResetPassword', [
            'token' => $token
        ]);
    }
}
