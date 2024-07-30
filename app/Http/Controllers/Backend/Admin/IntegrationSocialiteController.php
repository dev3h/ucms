<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\IdentificationAgeResource;
use App\Http\Resources\UserSocialIntegrationResource;
use Laravel\Socialite\Facades\Socialite;

class IntegrationSocialiteController extends Controller
{
    public function getAllIntegrationSocial() {
        $user = $this->getCurrentUser();
        if(!$user) {
            return $this->sendErrorResponse(__('User not found'), 404);
        }
        $socials = $user?->socials;
        return UserSocialIntegrationResource::collection($socials)
            ->additional(["status_code" => 200]);
    }

    public function redirectIntegrationSocial($provider) {
        if($provider == 'google') {
            config(['services.' . $provider . '.redirect' => env('GOOGLE_CALLBACK_INTEGRATION_URL')]);
        }
        return Socialite::driver($provider)->redirect();
    }

    public function callbackIntegrationSocial($provider) {
       try {
           $user = $this->getCurrentUser();
           if(!$user) {
               return redirect(route('admin.login.form'))->withErrors(__('User not found'));
           }
           if($provider == 'google') {
               config(['services.' . $provider . '.redirect' => env('GOOGLE_CALLBACK_INTEGRATION_URL')]);
           }
           $providerUser = Socialite::driver($provider)->user();
           $user->socials()->updateOrCreate([
               'provider_id' => $providerUser->getId(),
               'provider_type' => $provider,
               'user_id' => $user->id,
               'name' => $providerUser->getName(),
                'email' => $providerUser->getEmail(),
               'token' => $providerUser->token,
                'refresh_token' => $providerUser->refreshToken,
                'expires_in' => $providerUser->expiresIn,
           ]);
           return redirect()->route('admin.profile')->with('success', __('Integration successfully'));
       } catch (\Exception $e) {
              return redirect()->route('admin.profile')->withErrors(__('Integration failed'));
       }
    }

    public function unlinkIntegrationSocial($provider_id) {
        try {
            $user = $this->getCurrentUser();
            if(!$user) {
                return $this->sendErrorResponse(__('User not found'), 404);
            }
            $user->socials()->where('provider_id', $provider_id)->delete();
            return $this->sendSuccessResponse([], __('Unlink successfully'));
        } catch (\Exception $e) {
            return $this->sendErrorResponse(__('Unlink failed'), $e->getMessage());
        }
    }
}
