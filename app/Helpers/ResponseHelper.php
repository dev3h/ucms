<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class ResponseHelper
{
    /**
     * Sends a success response in JSON format.
     *
     * @param mixed $data
     * @param string $message
     * @param int $code
     * @return JsonResponse
     */
    public static function sendSuccessResponse(mixed $data, string $message = '', int $code = 200): JsonResponse
    {
        return response()->json([
            'status_code' => $code,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * Send an error response in JSON format.
     *
     * @param string $message
     * @param mixed $errors
     * @param int $code
     * @return JsonResponse
     */
    public static function sendErrorResponse(string $message, mixed $errors = null, int $code = 500): JsonResponse
    {
        return response()->json([
            'status_code' => $code,
            'message' => $message,
            'errors' => $errors,
        ], $code);
    }

    /**
     * Create a JSON error response.
     *
     * @param int $status
     * @param \Illuminate\Contracts\Validation\Validator|array $errors
     * @param string|null $errorCode
     * @return \Illuminate\Http\JsonResponse
     */
    public static function errorResponse(int $status, $errors, string $errorCode = null): JsonResponse
    {
        $errorMessage = null;
        $errorData = [];

        if (is_array($errors)) {
            $errorMessage = $errors['error'] ?? null;
            $errorData = $errors;
        } elseif ($errors instanceof \Illuminate\Contracts\Validation\Validator) {
            $errorMessage = $errors->errors()->first() ?? null;
            $errorData = $errors->errors() ?? [];
        }

        return new JsonResponse([
            'status_code' => $status,
            'message' => $errorMessage,
            'errors' => [
                'error_code' => $errorCode,
                'error_message' => $errorMessage,
                'error_data' => $errorData,
            ],
        ], $status);
    }
}
