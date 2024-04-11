<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

abstract class Controller
{
    use AuthorizesRequests;
    use ValidatesRequests;

    /**
     * @see ResponseHelper::sendSuccessResponse
     */
    public function sendSuccessResponse(mixed $data, string $message = '', int $code = 200)
    {
        return ResponseHelper::sendSuccessResponse($data, $message, $code);
    }

    /**
     * @see ResponseHelper::sendErrorResponse
     */
    public function sendErrorResponse(string $message, mixed $errors = null, int $code = 500)
    {
        return ResponseHelper::sendErrorResponse($message, $errors, $code);
    }

    public function getCurrentUser()
    {
        if (auth()->check()) {
            return auth()->user();
        }
        if (auth('admin')->check()) {
            return auth('admin')->user();
        }
        return null;
    }
}
