<?php

namespace App\Http\Controllers\Backend\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTwoFactorAuthRequest;
use Illuminate\Http\Request;
use PragmaRX\Google2FALaravel\Facade as Google2FA;

class TwoFactorAuthController extends Controller
{
    public function generateSecret(Request $request)
    {
        $user = $request->user();
        $secretKey = Google2FA::generateSecretKey();
        if ($user->two_fa_enabled_at) {
           return $this->sendErrorResponse('Two-factor authentication is already enabled.', null, 400);
        }
        $user->update([
            'two_fa_secret' => $secretKey,
        ]);

        $google2fa_url = Google2FA::getQRCodeInline(
            config('app.name'),
            $user->email,
            $secretKey
        );

        $data = [
            'secret' => $secretKey,
            '$google2fa_url' => $google2fa_url,
        ];

        return $this->sendSuccessResponse($data, 'Secret key generated successfully.', 200);
    }

    public function enable(CreateTwoFactorAuthRequest $request)
    {
        $user = $request->user();
        if ($user->two_fa_enabled_at) {
            return $this->sendErrorResponse('Two-factor authentication is already enabled.', null, 400);
        }
        if(!$user->two_fa_secret) {
            return $this->sendErrorResponse('Secret key is not generated.', null, 400);
        }
        if (!$request->validateToken()) {
            return $this->sendErrorResponse('Invalid verification code.', null, 400);
        }

        $user->update([
            'two_fa_enabled_at' => now(),
        ]);

        return $this->sendSuccessResponse(null, 'Two-factor authentication enabled successfully.', 200);
    }

    public function disable(CreateTwoFactorAuthRequest $request)
    {
        $user = $request->user();
        if (!$user->two_fa_enabled_at) {
            return $this->sendErrorResponse('Two-factor authentication is not enabled.', null, 400);
        }
        if (!$request->validateToken()) {
            return $this->sendErrorResponse('Invalid verification code.', null, 400);
        }

        $user->update([
            'two_fa_secret' => null,
            'two_fa_enabled_at' => null,
        ]);

        return $this->sendSuccessResponse(null, 'Two-factor authentication disabled successfully.', 200);
    }

    public function verify(CreateTwoFactorAuthRequest $request)
    {
        $user = $request->user();
        if (!$user->two_fa_enabled_at) {
            return $this->sendErrorResponse('Two-factor authentication is not enabled.', null, 400);
        }
        if (!$request->validateToken()) {
            return $this->sendErrorResponse('Invalid verification code.', null, 400);
        }
        [$accessToken, $expiresAt] = $this->generateAcessCredentialsFor($user, ['two-fa-verified'=>true]);

        return $this->sendSuccessResponse(null, 'Verification code is valid.', 200);
    }
}
