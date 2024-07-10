<?php

namespace App\Http\Controllers\Backend\Auth;

use App\Enums\PassFirstChangeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordFirstRequest;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;

class AuthenticatedSessionController extends Controller
{
    public function handleLogin(LoginRequest $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');
        $user = User::query()->where('email', $email)->first();

        if ($user === null) {
            throw ValidationException::withMessages(['email' => __('auth.failed')]);
        }

        if (!Hash::check($password, $user->password)) {
            throw ValidationException::withMessages(['password' => __('auth.password')]);
        }

        $routeRedirect = route('admin.user.index');
        Auth::loginUsingId($user->id, (bool)$request->remember);
        Auth::user()->recordAuditEvent('login', [], ['logged_in_at' => now()]);

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
