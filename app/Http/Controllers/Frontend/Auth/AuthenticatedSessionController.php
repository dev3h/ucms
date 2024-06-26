<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Http\Controllers\Controller;
use App\Models\DeviceToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AuthenticatedSessionController extends Controller
{
    public function formLogin()
    {
        return Inertia::render('Auth/Page/Login');
    }

    public function formChangePasswordFirst(Request $request)
    {
        $token = $request->token;
        $user = User::where('token_first_change', $token)->select('email', 'token_first_change')->first();
        if ($user === null) {
            return to_route('admin.login.form')->with('error', __('Token is invalid'));
        }
        return Inertia::render('Auth/Page/Password/ChangePasswordFirst', [
            'user' => $user,
        ]);
    }

//    public function logout()
//    {
//        $routeRedirect = route('admin.login.form');
//
//        $currentUser = $this->getCurrentUser();
//        $user = User::whereId($currentUser->id)->first();
//        $routeRedirect = route('master.form-login');
//
//        if ($user->hasRole('admin_enterprise') || $user->hasRole('user_enterprise')) {
//            $routeRedirect = route('business.form-login');
//        }
//
//        try {
//            $token = Auth::guard('user')->getToken();
//            JWTAuth::setToken($token)->invalidate($token);
//
//            // remove device token
//            $deviceTokens = DeviceToken::where('user_id', $currentUser->id)->get();
//            foreach ($deviceTokens as $deviceToken) {
//                $deviceToken->delete();
//            }
//
//            return $this->sendSuccessResponse(true, __('Logout successfully.'));
//        } catch (\Exception $e) {
//            return $this->sendErrorResponse(__('Something went wrong.'), $e->getMessage());
//        }
////        return redirect($routeRedirect);
//    }
    public function logout()
    {
        $routeRedirect = route('admin.login.form');
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect($routeRedirect);
    }

    public function formTwoFactorChallenge()
    {
        return Inertia::render('Auth/Page/TwoFactorChallenge');
    }
}
