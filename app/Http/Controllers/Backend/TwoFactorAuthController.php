<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


class TwoFactorAuthController extends Controller
{
    public function generate2faSecret(Request $request)
    {
        $user = $this->getCurrentUser();
        $secretKey = $user->generate2faSecretKey();

        return $this->sendSuccessResponse(['secret' => $secretKey, 'image' => $user->generate2faQrCode($user->email, $secretKey)]);
    }

    public function enable2fa(Request $request)
    {
    }
}
