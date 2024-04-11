<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class ChangePasswordFirstRequest extends FormRequest
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
        $user = User::where('email', request()->input('email'))->first();
        $passwordRules = [
            'required', 'string', 'min:8', 'max:16',
            'regex:/^[0-9a-zA-Z!"#$%&\'()-^\\@\[;:\],.\/=~|`{+*}<>?_]+$/'
        ];
        return [
            'email' => 'required',
            'token' => 'exists:users,token_first_change|nullable',
            'old_password' => array_merge($passwordRules, [
                function ($attribute, $value, $fail) use ($user) {
                    if (!Hash::check($value, $user->password)) {
                        $error = __('First password is wrong');
                        $fail($error);
                    }
                }
            ]),
            'password' => $passwordRules,
            'password_confirmation' => array_merge($passwordRules, ['same:password'])
        ];
    }
}
