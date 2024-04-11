<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Social;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    public function redirectSocial($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callbackSocial($provider)
    {
        try {
            $providerUser = Socialite::driver($provider)->user();

            $social = Social::where('provider_id', $providerUser->getId())
                ->where('provider_type', $provider)
                ->first();
            if (!$social) {
                return redirect(route('admin.login.form'))->withErrors(__('User not found'));
            }
            $user = User::find($social->user_id);
            Auth::login($user);

            return redirect()->route('my-page.index');
        } catch (\Exception $e) {
            return redirect(route('admin.login.form'));
        }
    }
}
