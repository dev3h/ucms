<?php

namespace App\Http\Controllers\Backend\Auth;

use App\Enums\PassFirstChangeEnum;
use App\Enums\PlatFormEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordFirstRequest;
use App\Http\Requests\LoginRequest;
use App\Models\DeviceToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthenticatedSessionController extends Controller
{
    public function handleLogin1(LoginRequest $request)
    {
        $email = $request->email;
        $password = $request->password;
        $user = User::where('email', $email)->first();

        if ($user === null) {
            throw ValidationException::withMessages(['email' => __('auth.failed')]);
        }

        if (!Hash::check($password, $user->password)) {
            throw ValidationException::withMessages(['password' => __('auth.password')]);
        }

//        if ($user->hasRole('master_admin') || $user->hasRole('master_user')) {
//            $routeRedirect = route('master.account.index');
//            Auth::guard('admin')->loginUsingId($user->id, $request->remember ? true : false);
//        } else {
//            $routeRedirect = route('business.account.index');
//            Auth::guard('business')->loginUsingId($user->id, $request->remember ? true : false);
//        }
//        if ($user->pass_is_changed == PassFirstChangeEnum::NOT_CHANGE) {
//            do {
//                // Tạo một chuỗi ngẫu nhiên mới
//                $str = Str::random(16);
//                // Kiểm tra xem chuỗi này đã được sử dụng bởi bất kỳ người dùng nào khác chưa
//            } while (Admin::where('token_first_change', $str)->exists());
//            $user->token_first_change = $str;
//            $user->save();
//            if ($user->hasRole('master_admin') || $user->hasRole('master_user')) {
//                $routeRedirect = 'master.password-first.form';
//            } else {
//                $routeRedirect = 'business.password-first.form';
//            }
//            return $this->sendSuccessResponse(route($routeRedirect, ['token' => $str]));
//        }
//        return $this->sendSuccessResponse($routeRedirect);
//        end change
        if($user->two_factor_confirmed_at) {
            $request->session()->put('login.id', $user->id);
            return $this->sendSuccessResponse(route('two-factor.login'));
        }

//        Auth::loginUsingId($user->id, (bool)$request->remember);
//        $token =  auth()->attempt(['email' => $email, 'password' => $password]);
        $token = JWTAuth::fromUser($user);
        $refreshToken = $this->createRefreshToken($user);
        $platform = $request->input('platform') || PlatFormEnum::WEB->value;
        $device_id = $request->input('device_id');
        $deviceToken = DeviceToken::query()->where('user_id', $user->id)->where('platform', $platform)->first();
        if ($deviceToken === null) {
            DeviceToken::query()->create([
                'user_id' => $user->id,
                'device_id' => $device_id,
                'token' => $token,
                'platform' => $platform
            ]);
        } else {
            $deviceToken->update([
                'token' => $token,
            ]);
        }
        $dataResponse = $this->respondWithToken($token, $refreshToken);

        if ($user->is_change_password == PassFirstChangeEnum::NOT_CHANGE->value) {
            $str = Str::random(32);
            $user->token_first_change = $str;
            $user->save();
            return $this->sendSuccessResponse(route('admin.password-first.form', ['token' => $str]));
        }
        activity()
            ->causedBy($user)
            ->log('Login success');

        return $this->sendSuccessResponse($dataResponse, __('Login successfully.'));
    }

    public function handleLogin(LoginRequest $request)
    {
        $email = $request->email;
        $password = $request->password;
        $user = User::query()->where('email', $email)->first();

        if ($user === null) {
            throw ValidationException::withMessages(['email' => __('auth.failed')]);
        }

        if (!Hash::check($password, $user->password)) {
            throw ValidationException::withMessages(['password' => __('auth.password')]);
        }

        $routeRedirect = route('admin.user.index');
        Auth::loginUsingId($user->id, (bool)$request->remember);
        activity()
            ->causedBy($user)
            ->logType('info')
            ->log('Login success');


        return $this->sendSuccessResponse($routeRedirect);
    }

    public function changePasswordFirst(ChangePasswordFirstRequest $request)
    {
        $data = $request->validated();
        $token = $request->token;
        $user = User::where('email', $data['email'])->where('token_first_change', $token)->first();
        if ($user === null) {
            throw ValidationException::withMessages(['token' => __('Token is invalid')]);
        }
        $user->password = Hash::make($data['password']);
        $user->is_change_password = PassFirstChangeEnum::CHANGED->value;
        $user->token_first_change = null;
        $user->save();

        return $this->sendSuccessResponse(true, __('Update Password Success'));
    }

    public function me()
    {
        return response()->json(auth()->user());
    }

    public function createRefreshToken($user) {
        $data =  [
            'sub' => $user->id,
            'random' => rand() . time(),
            'exp' => time() + config('jwt.refresh_ttl')
        ];
        return JWTAuth::getJWTProvider()->encode($data);
    }
    public function refresh($request)
    {
        try {
            $refreshToken = $request->input('refresh_token');
            $decoded = JWTAuth::getJWTProvider()->decode($refreshToken);
            $user = User::find($decoded['sub']);
            if($user === null) {
                throw ValidationException::withMessages(['refresh_token' => __('Refresh token is invalid.')]);
            }
            auth()->invalidate();
            $newToken = JWTAuth::fromUser($user);
            $newRefreshToken = $this->createRefreshToken($user);
            $dataResponse = $this->respondWithToken($newToken, $newRefreshToken);
            return $this->sendSuccessResponse($dataResponse, __('Login successfully.'));

        } catch (\Exception $e) {
            return $this->sendErrorResponse(__('Refresh token is invalid.'), $e->getMessage());
        }
    }

    public function respondWithToken($token, $refreshToken)
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'refresh_token' => $refreshToken,
            'expires_in' => auth()->factory()->getTTL() * 60
        ];
    }
}
