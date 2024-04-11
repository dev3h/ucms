<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            'email' => [
                'required',
                'string',
                'email',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}+$/',
                Rule::exists('users', 'email')
                    ->whereNull('deleted_at')
            ],
            'password' => [
                'required',
                'between:8,16',
                'regex:/^[0-9a-zA-Z!"#$%&\'()-^\\@\[;:\],.\/=~|`{+*}<>?_]+$/',
            ],
        ];
    }
}
