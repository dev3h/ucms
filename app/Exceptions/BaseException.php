<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Helpers\ResponseHelper;
use Illuminate\Contracts\Support\Responsable;
use RuntimeException;

class BaseException extends RuntimeException implements Responsable
{
    /**
     * @var int
     */
    protected $code;

    /**
     * @var string
     */
    protected $message;

    /**
     * @var mixed
     */
    protected $errors;

    /**
     * @var array
     */
    protected $headers;

    /**
     * BaseException constructor.
     *
     * @param string $message
     * @param mixed  $errors
     * @param int    $code
     * @param array  $headers
     */
    public function __construct(string $message = '', mixed $errors = null, int $code = 500, array $headers = [])
    {
        $this->message = $message;
        $this->errors = $errors;
        $this->code = $code;
        $this->headers = $headers;
    }

    /**
     * @param string $message
     */
    public function setErrorMessage(string $message): void
    {
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getErrorMessage(): string
    {
        return $this->message;
    }

    /**
     * @param mixed $errors
     *
     */
    public function setErrors(mixed $errors): void
    {
        $this->errors = $errors;
    }

    /**
     * @return mixed
     */
    public function getErrors(): mixed
    {
        return $this->errors;
    }

    /**
     * @param int $code
     */
    public function setErrorCode(int $code): void
    {
        $this->code = $code;
    }

    /**
     * @return int
     */
    public function getErrorCode(): int
    {
        return $this->code;
    }

    /**
     * @param array $headers
     */
    public function setHeaders(array $headers): void
    {
        $this->headers = $headers;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers ?? [];
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @see ResponseHelper::sendErrorResponse
     */
    public function toResponse($request)
    {
        return ResponseHelper::sendErrorResponse(
            $this->getErrorMessage(),
            $this->getErrors(),
            $this->getErrorCode(),
            $this->getHeaders(),
        );
    }
}
