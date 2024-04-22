<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SystemRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('systems', 'name')->ignore($this->id)->whereNull('deleted_at'),
            ],
            'code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('systems', 'code')->ignore($this->id)->whereNull('deleted_at'),
            ],
        ];
    }
}
