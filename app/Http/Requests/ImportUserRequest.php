<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ImportUserRequest extends FormRequest
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
            'data' => ['required', 'array'],
            'dâata.*.Name' => [
                'required',
                'string',
                'distinct',
                'max:255'
            ],
            'data.*.Email' => [
                'required',
                'email',
                'distinct',
                Rule::unique('users', 'email')->whereNull('deleted_at')
            ],
        ];
    }
}
