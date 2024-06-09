<?php

namespace App\Http\Controllers\Backend\Auth;

use App\Enums\PassFirstChangeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordFirstRequest;
use App\Http\Requests\LoginRequest;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthenticatedSessionController extends Controller
{
    public function handleLogin(LoginRequest $request)
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

//        change\
        if ($user->hasRole('master_admin') || $user->hasRole('master_user')) {
            $routeRedirect = route('master.account.index');
            Auth::guard('admin')->loginUsingId($user->id, $request->remember ? true : false);
        } else {
            $routeRedirect = route('business.account.index');
            Auth::guard('business')->loginUsingId($user->id, $request->remember ? true : false);
        }
        if ($user->pass_is_changed == PassFirstChangeEnum::NOT_CHANGE) {
            do {
                // Tạo một chuỗi ngẫu nhiên mới
                $str = Str::random(16);
                // Kiểm tra xem chuỗi này đã được sử dụng bởi bất kỳ người dùng nào khác chưa
            } while (Admin::where('token_first_change', $str)->exists());
            $user->token_first_change = $str;
            $user->save();
            if ($user->hasRole('master_admin') || $user->hasRole('master_user')) {
                $routeRedirect = 'master.password-first.form';
            } else {
                $routeRedirect = 'business.password-first.form';
            }
            return $this->sendSuccessResponse(route($routeRedirect, ['token' => $str]));
        }
        return $this->sendSuccessResponse($routeRedirect);
//        end change

        $routeRedirect = route('admin.profile');
        if($user->two_factor_confirmed_at) {
            $request->session()->put('login.id', $user->id);
            return $this->sendSuccessResponse(route('two-factor.login'));
        }

        Auth::loginUsingId($user->id, (bool)$request->remember);

        if ($user->is_change_password == PassFirstChangeEnum::NOT_CHANGE->value) {
            $str = Str::random(32);
            $user->token_first_change = $str;
            $user->save();
            return $this->sendSuccessResponse(route('admin.password-first.form', ['token' => $str]));
        }
        activity()
            ->causedBy($user)
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
}
