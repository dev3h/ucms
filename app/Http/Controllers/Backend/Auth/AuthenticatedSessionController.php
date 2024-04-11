<?php

namespace App\Http\Controllers\Backend\Admin\Auth;

use App\Enums\PassFirstChangeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordFirstRequest;
use App\Http\Requests\LoginRequest;
use App\Models\Admin;
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
        $user = Admin::where('email', $email)->first();

        if ($user === null) {
            throw ValidationException::withMessages(['email' => __('auth.failed')]);
        }

        if (!Hash::check($password, $user->password)) {
            throw ValidationException::withMessages(['password' => __('auth.password')]);
        }
        $routeRedirect = route('admin.account.index');
        Auth::guard('admin')->loginUsingId($user->id, (bool)$request->remember);

        if ($user->is_change_password == PassFirstChangeEnum::NOT_CHANGE->value) {
            $str = Str::random(32);
            $user->token_first_change = $str;
            $user->save();
            return $this->sendSuccessResponse(route('admin.password-first.form', ['token' => $str]));
        }

        return $this->sendSuccessResponse($routeRedirect);
    }

    public function changePasswordFirst(ChangePasswordFirstRequest $request)
    {
        $data = $request->validated();
        $token = $request->token;
        $user = Admin::where('email', $data['email'])->where('token_first_change', $token)->first();
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
