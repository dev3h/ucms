<?php

namespace App\Http\Controllers\Api\User\Auth;

use App\Consts\ErrorCode;
use App\Enums\StatusRegisterEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\CheckOtpRegisterRequest;
use App\Http\Requests\User\SendOtpRequest;
use App\Models\OtpCode;
use App\Models\User;
use App\Traits\SendOtpTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Exceptions\ValidationException;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;

class RegisterController extends Controller
{
    use SendOtpTrait;

    private $blockTime = 60;

    protected function checkBlock($user)
    {
        if ($user->temporary_block_time) {
            $dateBlock = format_date($user->temporary_block_time);
            $dateNow = format_date(now());
            if ($user->temporary_block_time >= now()->subMinutes($this->blockTime) && $dateBlock == $dateNow) {
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

    public function sendOtpRegister(SendOtpRequest $request)
    {
        $phoneNumber = $request->phone_number;
        $randomNumber = rand(100000, 999999);

        $user = User::where('phone_number', $phoneNumber)
            ->withoutGlobalScope('isRegistered')
            ->first();

        if ($user && $user->status_register != StatusRegisterEnum::VERIFYING_CODE->value) {
            throw ValidationException::withMessages(['phone_number' => __('Phone number has already been registered.')]);
        }
        if (!$user) {
            $user = User::create([
                'phone_number' => $phoneNumber
            ]);
        }

        $checkBlock = $this->checkBlock($user);
        if (!$checkBlock) {
            return $checkBlock;
        }
        $countOtpCode = $user->otpCodes()
            ->where('created_at', '>=', now()->subMinutes(10))
            ->count();
        if ($countOtpCode >= 5) {
            $user->update([
                'temporary_block_time' => now()
            ]);
            throw ValidationException::withMessages(['phone_number' => __('Temporary phone number lock.')])
                ->errorCode(ErrorCode::ERROR_BLOCKED_FOR_1_HOUR);
        }

        try {
            $otpCode = $this->sendOtp($user, $phoneNumber, $randomNumber);
            $dataResponse = [
                'expired_at' => 60 * 60,
                'request_count' => $countOtpCode + 1,
                'phone' => $otpCode->phone_number,
                'type' => 'Register'
            ];

            return $this->sendSuccessResponse($dataResponse, __('Send otp successfully.'));
        } catch (\Exception $e) {
            return $this->sendErrorResponse(__('Something went wrong.'), $e->getMessage());
        }
    }

    public function checkOtpRegister(CheckOtpRegisterRequest $request)
    {
        $data = $request->validated();
        $user = User::where('phone_number', $data['phone_number'])
            ->withoutGlobalScope('isRegistered')
            ->first();

        if ($user && $user->status_register != StatusRegisterEnum::VERIFYING_CODE->value) {
            throw ValidationException::withMessages(['phone_number' => __('Phone number has already been registered.')]);
        }

        $checkBlock = $this->checkBlock($user);
        if (!$checkBlock) {
            return $checkBlock;
        }

        $otpCode = $user->getOtpCode();
        if ($otpCode && $otpCode->otp == $data['otp']) {
            $timeOtp = Carbon::parse($otpCode->created_at);
            if ($timeOtp->diffInMinutes(now()) > $this->blockTime) {
                throw ValidationException::withMessages(['otp' => __('OTP code has expired.')])
                    ->errorCode(ErrorCode::ERROR_BLOCKED_FOR_1_HOUR);
            }

            DB::beginTransaction();
            try {
                $token = JWTAuth::fromUser($user);
                $user->update([
                    'error_count' => 0,
                    'temporary_block_time' => null,
                    'status_register' => StatusRegisterEnum::REGISTERING,
                    'last_login_at' => now()
                ]);
                $user->otpCodes()->delete();
                DB::commit();

                $dataResponse = [
                    'token' => $token,
                    'status_register' => $user->status_register,
                    'is_guide_viewed' => $user->is_guide_viewed,
                    'user_id' => $user->id,
                ];
                return $this->sendSuccessResponse($dataResponse, __('Register successfully.'));
            } catch (\Exception $e) {
                DB::rollBack();
                return $this->sendErrorResponse(__('Something went wrong.'), $e->getMessage());
            }
        } else {
            $dataUpdate['error_count'] = $user->error_count + 1;
            if ($dataUpdate['error_count'] >= 5) {
                $dataUpdate['temporary_block_time'] = now();
            }
            $user->update($dataUpdate);
            throw ValidationException::withMessages(['otp' => __('Otp code is incorrect.')]);
        }
    }
}
