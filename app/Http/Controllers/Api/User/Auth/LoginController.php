<?php

namespace App\Http\Controllers\Api\User\Auth;

use App\Consts\ErrorCode;
use App\Enums\StatusRegisterEnum;
use App\Enums\StatusUserEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\CheckOtpRequest;
use App\Http\Requests\User\SendOtpRequest;
use App\Models\OtpCode;
use App\Models\User;
use App\Traits\SendOtpTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Exceptions\ValidationException;
use App\Models\DeviceToken;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginController extends Controller
{
    use SendOtpTrait;

    private $blockTime = 60;

    protected function checkBlock($user)
    {
        if ($user->temporary_block_time) {
            $dateBlock = format_date($user->temporary_block_time);
            $dateNow = format_date(now());
            if ($user->temporary_block_time >= now()->now()->subMinutes($this->blockTime) && $dateBlock == $dateNow) {
                throw ValidationException::withMessages(['phone_number' => __('Temporary phone number lock.')])
                    ->errorCode(ErrorCode::ERROR_BLOCKED_FOR_1_HOUR);
            } else {
                $user->update([
                    'error_count' => 0,
                    'temporary_block_time' => null,
                ]);
                $user->otpCodes()->delete();
            }
        }
        return true;
    }

    public function sendOtpLogin(SendOtpRequest $request)
    {
        $phoneNumber = $request->phone_number;
        $randomNumber = rand(100000, 999999);

        $user = User::where('phone_number', $phoneNumber)
            ->withoutGlobalScope('isRegistered')
            ->whereNot('status_register', StatusRegisterEnum::VERIFYING_CODE)
            ->first();

        if (!$user) {
            throw ValidationException::withMessages(['phone_number' => __('Phone number is incorrect.')]);
        }

        if ($user->status == StatusUserEnum::BLOCK->value) {
            throw ValidationException::withMessages(['phone_number' => __('The account has been blocked by the administrator.')]);
        }

        try {
            $otpCode = $this->sendOtp($user, $phoneNumber, $randomNumber);

            $dataResponse = [
                'expired_at' => 60 * 60,
                'request_count' => 0,
                'phone' => $otpCode->phone_number,
                'type' => 'Login'
            ];

            return $this->sendSuccessResponse($dataResponse, __('Send otp successfully.'));
        } catch (\Exception $e) {
            return $this->sendErrorResponse(__('Something went wrong.'), $e->getMessage());
        }
    }

    public function checkOtpLogin(CheckOtpRequest $request)
    {
        $data = $request->validated();
        $user = User::where('phone_number', $data['phone_number'])
            ->withoutGlobalScope('isRegistered')
            ->whereNot('status_register', StatusRegisterEnum::VERIFYING_CODE)
            ->first();

        if (!$user) {
            throw ValidationException::withMessages(['phone_number' => __('Phone number is incorrect.')]);
        }

        if ($user->status == StatusUserEnum::BLOCK->value) {
            throw ValidationException::withMessages(['phone_number' => __('The account has been blocked by the administrator.')]);
        }

        $checkBlock = $this->checkBlock($user);
        if (!$checkBlock) {
            return $checkBlock;
        }

        $otpCode = $user->getOtpCode();
        if ($otpCode && $otpCode->otp == $data['otp']) {
            $timeOtp = Carbon::parse($otpCode->created_at);
            if ($timeOtp->diffInMinutes(now()) > $this->blockTime) {
                throw ValidationException::withMessages(['otp' => __('OTP code has expired.')]);
            }

            $token = JWTAuth::fromUser($user);
            $user->update([
                'error_count' => 0,
                'temporary_block_time' => null,
                'last_login_at' => now(),
            ]);
            $user->otpCodes()->delete();

            $dataResponse = [
                'token' => $token,
                'status_register' => $user->status_register,
                'is_guide_viewed' => $user->is_guide_viewed,
                'user_id' => $user->id,
            ];
            return $this->sendSuccessResponse($dataResponse, __('Login successfully.'));
        } else {
            $dataUpdate['error_count'] = $user->error_count + 1;
            if ($dataUpdate['error_count'] >= 5) {
                $dataUpdate['temporary_block_time'] = now();
            }
            $user->update($dataUpdate);
            throw ValidationException::withMessages(['otp' => __('Otp code is incorrect.')]);
        }
    }

    public function refresh()
    {
        try {
            $newToken = JWTAuth::parseToken()->refresh();

            return $this->sendSuccessResponse(['new_token' => $newToken], __('Refresh token successfully.'));
        } catch (\Exception $e) {
            return $this->sendErrorResponse(__('The token is invalid.'), [], 401);
        }
    }

    public function logout()
    {
        $currentUser = $this->getCurrentUser();

        try {
            $token = Auth::guard('user')->getToken();
            JWTAuth::setToken($token)->invalidate($token);

            // remove device token
            $deviceTokens = DeviceToken::where('user_id', $currentUser->id)->get();
            foreach ($deviceTokens as $deviceToken) {
                $deviceToken->delete();
            }

            return $this->sendSuccessResponse(true, __('Logout successfully.'));
        } catch (\Exception $e) {
            return $this->sendErrorResponse(__('Something went wrong.'), $e->getMessage());
        }
    }
}
