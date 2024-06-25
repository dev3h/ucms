<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest as BaseFormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class FormRequest extends BaseFormRequest
{
    protected $status = 422; // Unprocessable Entity
    protected $errorCode = 'VALIDATION_ERROR'; // Custom error code
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
            //
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            new JsonResponse([
                'status_code' => $this->status,
                'message' => $validator->errors()->first() ?? null,
                'errors' => [
                    'error_code' => $this->errorCode,
                    'error_message' => $validator->errors()->first() ?? null,
                    'error_data' => $validator->errors()->toArray() ?? [],
                ]
            ], $this->status)
        );
    }
}
