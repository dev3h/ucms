<?php

namespace App\Exceptions;

use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException as IlluminateValidationException;

class CustomValidationException extends IlluminateValidationException
{
    protected $errorCode = 212;

    public function render($request): JsonResponse
    {
        return new JsonResponse([
            'status_code' => $this->status,
            'message' => $this->validator->errors()->first() ?? null,
            'errors' => [
                'error_code'  => $this->errorCode,
                'error_message'  => $this->validator->errors()->first() ?? null,
                'error_data' => $this->validator->errors() ?? [],
            ]
        ], $this->status);
    }

    public function errorCode($errorCode)
    {
        $this->errorCode = $errorCode;

        return $this;
    }
}
