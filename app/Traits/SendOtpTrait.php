<?php

namespace App\Traits;

use Aws\Credentials\Credentials;
use Aws\Sns\SnsClient;
use Illuminate\Support\Facades\Log;

trait SendOtpTrait
{
    public function sendOtp($user, $phoneNumber, $randomNumber)
    {
        $otpCode = $user->otpCodes()->create([
            'phone_number' => $phoneNumber,
            'otp' => $randomNumber
        ]);

        try {
            $message = '認識コード:' . $randomNumber;
            $client = new SnsClient([
                'region' => config('aws.region'),
                'credentials' => new Credentials(
                    config('aws.credentials.key'),
                    config('aws.credentials.secret')
                ),
                'version' => config('aws.version'),
            ]);
            $client->SetSMSAttributes(
                [
                    'attributes' => [
                        'DefaultSenderID' =>  config('aws.sender_id'),
                        'DefaultSMSType' => 'Transactional'
                    ]
                ]
            );
            $client->publish([
                'Message' => $message,
                'PhoneNumber' => config('aws.sender_country_code') . ltrim($phoneNumber, '0'),
            ]);
        } catch (\Exception $e) {
            Log::debug('Send otp failed.', ['error' => $e]);
        }

        return $otpCode;
    }
}
