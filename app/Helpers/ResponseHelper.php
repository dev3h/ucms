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
}
