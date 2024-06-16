<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class DBException extends BaseException
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
        $this->setErrorMessage(__('errors.db_error'));
        $this->setErrors(__('errors.db_error'));
        $this->setErrorCode(500);

        return parent::toResponse($request);
    }
}
