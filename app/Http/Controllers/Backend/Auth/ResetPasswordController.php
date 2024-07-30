<?php

namespace App\Http\Controllers\Backend\Auth;

use App\Enums\PassFirstChangeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\UpdateResetPasswordRequest;
use App\Jobs\SendResetPasswordMail;
use App\Models\Admin;
use App\Models\AdminPasswordResetToken;
use App\Models\PasswordResetToken;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;

class ResetPasswordController extends Controller
{
    public function sendMailResetPassword(ResetPasswordRequest $request)
    {
        $data = $request->validated();
        $user = User::where('email', $data['email'])->first();
        if ($request->routeIs('admin.api.*')) {
            $user = Admin::where('email', $data['email'])->first();
        }

        if (!$user) {
            return $this->sendErrorResponse(__('The email address you entered does not exist'));
        }
        $token = \Str::random(60);
        if ($request->routeIs('user.api.*')) {
            PasswordResetToken::updateOrInsert(
                ['email' => $data['email']],
                [
                    'token' => \Hash::make($token),
                    'created_at' => \Carbon\Carbon::now()
                ]
            );
        } elseif ($request->routeIs('admin.api.*')) {
            AdminPasswordResetToken::updateOrInsert(
                ['email' =>$data['email']],
                [
                    'token' => \Hash::make($token),
                    'created_at' => \Carbon\Carbon::now()
                ]
            );
        }
        $route = 'user.password.reset';
        if (request()->routeIs('admin.api.*')) {
            $route = 'admin.password.reset';
        }
        SendResetPasswordMail::dispatch($user, $token, $route);

        return $this->sendSuccessResponse([], __('A link has been sent to the email address you entered'));
    }
    public function passwordResetUpdate(UpdateResetPasswordRequest $request)
    {
        $data = $request->validated();
        $status = Password::reset(
            $data,
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ]);

                if ($user->is_change_password_first === PassFirstChangeEnum::NOT_CHANGE->value) {
                    $user->is_change_password_first = PassFirstChangeEnum::CHANGED->value;
                    $user->token_first_change = null;
                }

                $user->save();

                event(new PasswordReset($user));
            }
        );
        return $status === Password::PASSWORD_RESET
            ? $this->sendSuccessResponse([], __('Updated successfully'))
            : $this->sendErrorResponse(__('Something went wrong'));
    }
}
