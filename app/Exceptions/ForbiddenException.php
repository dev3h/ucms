<?php

declare(strict_types=1);

namespace App\Exceptions;

class ForbiddenException extends BaseException
{
    /**
     * {@inheritdoc}
     */
    public function toResponse($request)
    {
        $this->setErrorMessage(__('errors.forbidden'));
        $this->setErrors('');
        $this->setErrorCode(403);

        return parent::toResponse($request);
    }
}
