<?php

namespace App\Http\Controllers\Backend\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\UpdateResetPasswordRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;

class ResetPasswordController extends Controller
{
    public function sendMailResetPassword(ResetPasswordRequest $request)
    {
        $data = $request->validated();
        $status = Password::sendResetLink(['email' => $data['email']]);
        return $status === Password::RESET_LINK_SENT
            ? $this->sendSuccessResponse([], __('A link has been sent to the email address you entered'))
            : back()->withErrors(['email' => __('The email address you entered does not exist')]);
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

                $user->save();

                event(new PasswordReset($user));
            }
        );
        return $status === Password::PASSWORD_RESET
            ? $this->sendSuccessResponse([], __('Updated successfully'))
            : $this->sendErrorResponse(__('Something went wrong'));
    }
}
