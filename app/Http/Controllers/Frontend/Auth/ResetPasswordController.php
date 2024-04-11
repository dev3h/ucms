<?php

namespace App\Http\Controllers\Frontend\Admin\Auth;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class ResetPasswordController extends Controller
{
    public function formForgotPassword()
    {
        return Inertia::render('Auth/Password/ForgotPassword');
    }

    public function confirmForgotPassword()
    {
        return Inertia::render('Auth/Password/ConfirmForgotPassword');
    }
    public function passwordReset(string $token)
    {
        return Inertia::render('Auth/Password/ResetPassword', [
            'token' => $token
        ]);
    }
}
