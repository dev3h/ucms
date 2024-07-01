<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use PragmaRX\Google2FALaravel\Facade as Google2FA;

class CreateTwoFactorAuthRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'token' => ['required', 'string', 'digits:6']
        ];
    }

    public function validateToken(): bool
    {
        try {
            return Google2FA::verifyKey($this->user()->two_fa_secret, $this->token);
        } catch (\Exception $e) {
            report($e);
            return false;
        }
    }
}
