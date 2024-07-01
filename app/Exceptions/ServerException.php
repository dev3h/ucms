<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class ServerException extends BaseException
{
    protected $exception;

    public function __construct(?Exception $exception = null)
    {
        if ($exception != null) {
            $this->exception = $exception;
            Log::error($exception->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function toResponse($request)
    {
        $this->setErrorMessage(__('errors.something_went_wrong'));
        $this->setErrors($this->exception->getMessage());
        $this->setErrorCode(500);

        return parent::toResponse($request);
    }
}
